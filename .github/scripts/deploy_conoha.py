#!/usr/bin/env python3
"""ConoHa WING の公開ファイルを、削除せず安全に更新する。

GitHub Actions からのみ使用する。対象は Git で管理される公開ファイルに限定し、
一時名でアップロードして内容を確認してから FTP の同一ディレクトリ内 rename で
切り替える。FTP 上の既存ファイル・ディレクトリを削除する処理は持たない。
"""

from __future__ import annotations

import ftplib
import hashlib
import os
import ssl
import subprocess
import sys
from pathlib import Path, PurePosixPath


# このサイトで公開する拡張子だけを対象にする。運用資料や Actions 設定は送らない。
PUBLIC_SUFFIXES = {
    ".avif",
    ".css",
    ".gif",
    ".htm",
    ".html",
    ".ico",
    ".jpeg",
    ".jpg",
    ".js",
    ".php",
    ".png",
    ".svg",
    ".txt",
    ".webmanifest",
    ".webp",
    ".xml",
}
EXCLUDED_TOP_LEVEL = {
    ".gitignore",
    "AGENTS.md",
    "netlify.toml",
    "qodana.yaml",
    "sei-ko.org.code-workspace",
}
EXCLUDED_DIRECTORIES = {".github", ".idea", ".netlify", "docs", "tools"}


def require_environment(name: str) -> str:
    """必須のシークレットが未設定なら、接続前に止める。"""
    value = os.environ.get(name, "").strip()
    if not value:
        raise RuntimeError(f"必須環境変数 {name} が設定されていません。")
    return value


def tracked_public_files(workspace: Path) -> list[PurePosixPath]:
    """Git 追跡済みで、公開対象のファイルだけを列挙する。"""
    result = subprocess.run(
        ["git", "ls-files", "-z"],
        cwd=workspace,
        check=True,
        capture_output=True,
    )
    paths: list[PurePosixPath] = []
    for item in result.stdout.decode("utf-8").split("\0"):
        if not item:
            continue
        relative = PurePosixPath(item)
        if relative.parts[0] in EXCLUDED_DIRECTORIES:
            continue
        if len(relative.parts) == 1 and relative.name in EXCLUDED_TOP_LEVEL:
            continue
        if relative.name != ".htaccess" and relative.suffix.lower() not in PUBLIC_SUFFIXES:
            continue
        local_file = workspace.joinpath(*relative.parts)
        if local_file.is_file() and not local_file.is_symlink():
            paths.append(relative)

    # 参照される素材を先に更新し、入口であるトップページと設定を最後に切り替える。
    def deployment_order(path: PurePosixPath) -> tuple[int, str]:
        if path.as_posix() == "index.html":
            return (2, path.as_posix())
        if path.name == ".htaccess":
            return (3, path.as_posix())
        return (1, path.as_posix())

    return sorted(paths, key=deployment_order)


def make_remote_directories(ftp: ftplib.FTP_TLS, remote_root: str, relative: PurePosixPath) -> None:
    """公開先とその親ディレクトリを作成する。既存なら何もしない。"""
    for part in (PurePosixPath(remote_root.strip("/")) / relative.parent).parts:
        try:
            ftp.mkd(part)
        except ftplib.error_perm as error:
            # 550 は通常「すでに存在」。他の拒否は安全のため中断する。
            if not str(error).startswith("550"):
                raise
        ftp.cwd(part)


def remote_digest(ftp: ftplib.FTP_TLS, remote_name: str) -> str | None:
    """既存ファイルの SHA-256 を返す。存在しなければ None。"""
    digest = hashlib.sha256()
    try:
        ftp.retrbinary(f"RETR {remote_name}", digest.update)
    except ftplib.all_errors as error:
        if str(error).startswith("550"):
            return None
        raise
    return digest.hexdigest()


def local_digest(local_file: Path) -> str:
    """ローカルのファイル内容を比較するための SHA-256 を計算する。"""
    digest = hashlib.sha256()
    with local_file.open("rb") as source:
        for chunk in iter(lambda: source.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def upload_atomically(
    ftp: ftplib.FTP_TLS,
    local_file: Path,
    remote_name: str,
    run_id: str,
) -> None:
    """一時名で完全転送・サイズ確認してから rename する。"""
    temporary_name = f".{remote_name}.github-deploy-{run_id}.tmp"
    local_size = local_file.stat().st_size
    try:
        with local_file.open("rb") as source:
            ftp.storbinary(f"STOR {temporary_name}", source, blocksize=1024 * 1024)
        if ftp.size(temporary_name) != local_size:
            raise RuntimeError(f"一時ファイルのサイズ検証に失敗しました: {remote_name}")
        # 既存の公開ファイルを削除せず、同一ディレクトリ内で置き換える。
        ftp.rename(temporary_name, remote_name)
    except BaseException:
        try:
            ftp.delete(temporary_name)
        except ftplib.all_errors:
            pass
        raise


def main() -> int:
    workspace = Path(os.environ.get("GITHUB_WORKSPACE", Path.cwd())).resolve()
    host = require_environment("CONOHA_FTP_HOST")
    username = require_environment("CONOHA_FTP_USERNAME")
    password = require_environment("CONOHA_FTP_PASSWORD")
    remote_root = require_environment("CONOHA_FTP_REMOTE_DIR")
    dry_run = os.environ.get("DEPLOY_DRY_RUN", "false").lower() == "true"
    run_id = os.environ.get("GITHUB_RUN_ID", "manual")
    files = tracked_public_files(workspace)

    print(f"公開対象: {len(files)} ファイル。削除は行いません。")
    if dry_run:
        print("ドライラン: リモートとの差分確認のみで、変更は行いません。")

    context = ssl.create_default_context()
    ftp = ftplib.FTP_TLS(context=context, timeout=60)
    try:
        ftp.connect(host, 21)
        ftp.login(username, password)
        ftp.prot_p()
        ftp.cwd("/")

        changed: list[PurePosixPath] = []
        for relative in files:
            local_file = workspace.joinpath(*relative.parts)
            remote_directory = (PurePosixPath(remote_root.strip("/")) / relative.parent).as_posix()
            ftp.cwd("/")
            try:
                ftp.cwd(remote_directory)
            except ftplib.error_perm as error:
                if not str(error).startswith("550"):
                    raise
                ftp.cwd("/")
                make_remote_directories(ftp, remote_root, relative)

            if local_digest(local_file) == remote_digest(ftp, relative.name):
                print(f"変更なし: {relative.as_posix()}")
                continue
            changed.append(relative)
            if dry_run:
                print(f"更新予定: {relative.as_posix()}")
            else:
                print(f"更新中: {relative.as_posix()}")
                upload_atomically(ftp, local_file, relative.name, run_id)
                print(f"更新済み: {relative.as_posix()}")

        print(f"差分: {len(changed)} ファイル")
        return 0
    finally:
        try:
            ftp.quit()
        except ftplib.all_errors:
            ftp.close()


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except (RuntimeError, subprocess.CalledProcessError, ftplib.all_errors, OSError) as error:
        print(f"デプロイを中止しました: {error}", file=sys.stderr)
        raise SystemExit(1)
