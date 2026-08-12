#!/usr/bin/env python3
"""Recover public assets referenced by the Velky Mlyn WordPress theme."""

from __future__ import annotations

import argparse
import hashlib
import html
import json
import mimetypes
import os
import re
import sys
import tempfile
import time
from collections import deque
from pathlib import Path, PurePosixPath
from typing import Iterable
from urllib.error import HTTPError, URLError
from urllib.parse import unquote, urljoin, urlsplit, urlunsplit
from urllib.request import HTTPRedirectHandler, Request, build_opener

SITE_ORIGIN = "https://mlyn.honzakopta.cz"
THEME_WEB_PATH = "/wp-content/themes/velkymlyn/"
THEME_BASE = SITE_ORIGIN + THEME_WEB_PATH
USER_AGENT = "VelkyMlynThemeRecovery/1.0 (+asset audit; contact: local administrator)"
ASSET_EXTENSIONS = {
    ".avif", ".bmp", ".css", ".cur", ".eot", ".gif", ".ico", ".jpeg", ".jpg",
    ".js", ".json", ".map", ".mp3", ".mp4", ".ogg", ".otf", ".pdf", ".png",
    ".svg", ".ttf", ".webm", ".webmanifest", ".webp", ".woff", ".woff2",
}
TEXT_EXTENSIONS = {
    ".css", ".html", ".htm", ".js", ".json", ".less", ".md", ".php", ".scss",
    ".svg", ".txt", ".xml",
}
SKIP_DIRS = {".git", "node_modules", "vendor", "theme-recovery"}
URL_RE = re.compile(r"""(?<![-\w])url\(\s*(['"]?)(.*?)\1\s*\)""", re.I | re.S)
ATTR_RE = re.compile(
    r"""\b(?:src|href|poster)\s*=\s*(['"])(.*?)\1|\bsrcset\s*=\s*(['"])(.*?)\3""",
    re.I | re.S,
)
STRING_RE = re.compile(r"""(['"])([^'"\r\n]{1,1000})\1""")
THEME_FUNC_RE = re.compile(
    r"""(?:get_template_directory_uri|get_stylesheet_directory_uri)\s*\(\s*\)\s*\.\s*(['"])(.*?)\1""",
    re.I | re.S,
)
REMOTE_THEME_RE = re.compile(
    r"""https?://mlyn\.honzakopta\.cz/wp-content/themes/velkymlyn/[^\s'"()<>{}]+""",
    re.I,
)
MARKDOWN_LINK_RE = re.compile(r"""!?\[[^\]]*\]\((https?://[^)\s]+|//[^)\s]+)\)""", re.I)


class SameHostRedirectHandler(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        target = urlsplit(newurl)
        if target.scheme not in {"http", "https"} or target.hostname != "mlyn.honzakopta.cz":
            raise HTTPError(newurl, code, "redirect outside mlyn.honzakopta.cz", headers, fp)
        return super().redirect_request(req, fp, code, msg, headers, newurl)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--theme-dir", required=True, help="Local theme directory")
    parser.add_argument("--dry-run", action="store_true", help="Discover and map without network or writes")
    parser.add_argument("--verbose", action="store_true")
    parser.add_argument("--retries", type=int, default=2)
    parser.add_argument("--timeout", type=float, default=15.0)
    parser.add_argument("--delay", type=float, default=0.25)
    parser.add_argument("--internal-pages", type=int, default=3)
    return parser.parse_args()


def is_asset_reference(value: str) -> bool:
    value = html.unescape(value.strip())
    if not value or value.startswith(("data:", "#", "mailto:", "tel:", "javascript:")):
        return False
    if (
        any(char.isspace() for char in value)
        or any(char in value for char in ("*", "{", "}", "$"))
    ):
        return False
    path = urlsplit(value if not value.startswith("//") else "https:" + value).path
    return Path(path).suffix.lower() in ASSET_EXTENSIONS


def extract_references(text: str, allow_strings: bool = True) -> list[tuple[str, str]]:
    found: list[tuple[str, str]] = []
    theme_function_spans = []
    for match in URL_RE.finditer(text):
        found.append((match.group(2).strip(), "css-url"))
    for match in ATTR_RE.finditer(text):
        value = (match.group(2) or match.group(4) or "").strip()
        if match.group(4):
            for candidate in value.split(","):
                found.append((candidate.strip().split()[0], "html-srcset"))
        else:
            found.append((value, "html-attribute"))
    for match in THEME_FUNC_RE.finditer(text):
        found.append(("theme://" + match.group(2).lstrip("/"), "wordpress-theme-uri"))
        theme_function_spans.append(match.span())
    for match in REMOTE_THEME_RE.finditer(text):
        found.append((match.group(0), "absolute-theme-url"))
    for match in MARKDOWN_LINK_RE.finditer(text):
        found.append((match.group(1), "markdown-link"))
    if allow_strings:
        for match in STRING_RE.finditer(text):
            if any(start <= match.start() and match.end() <= end for start, end in theme_function_spans):
                continue
            value = match.group(2).strip()
            if is_asset_reference(value):
                found.append((value, "string-literal"))
    unique = []
    seen = set()
    for value, kind in found:
        key = (value, kind)
        if key not in seen:
            seen.add(key)
            unique.append(key)
    return unique


def safe_relative_path(encoded_path: str) -> PurePosixPath:
    raw_segments = encoded_path.replace("\\", "/").split("/")
    decoded = []
    for segment in raw_segments:
        if not segment:
            continue
        value = unquote(segment)
        if (
            value in {".", ".."}
            or "\x00" in value
            or "/" in value
            or "\\" in value
            or value.startswith("~")
        ):
            raise ValueError("unsafe path segment")
        decoded.append(value)
    if not decoded:
        raise ValueError("empty asset path")
    result = PurePosixPath(*decoded)
    if result.is_absolute() or ".." in result.parts:
        raise ValueError("path escapes theme")
    return result


def normalize_reference(reference: str, source: str) -> dict:
    original = html.unescape(reference.strip())
    clean = original.strip("'\" \t\r\n")
    record = {
        "original_reference": original,
        "source_file": source,
        "normalized_remote_url": None,
        "local_path": None,
        "classification": None,
        "normalization_error": None,
    }
    try:
        if clean.startswith("theme://"):
            rel = safe_relative_path(clean[len("theme://"):])
        elif clean.startswith("//"):
            return classify_absolute("https:" + clean, record)
        elif urlsplit(clean).scheme in {"http", "https"}:
            return classify_absolute(clean, record)
        elif clean.startswith(THEME_WEB_PATH):
            rel = safe_relative_path(urlsplit(clean).path[len(THEME_WEB_PATH):])
        elif clean.startswith("/"):
            record["classification"] = "site-root"
            record["normalized_remote_url"] = urljoin(SITE_ORIGIN + "/", clean.lstrip("/"))
            return record
        else:
            path = urlsplit(clean).path
            source_dir = PurePosixPath(source).parent
            joined = source_dir.joinpath(PurePosixPath(path))
            collapsed: list[str] = []
            for part in joined.parts:
                if part in {"", "."}:
                    continue
                if part == "..":
                    if not collapsed:
                        raise ValueError("relative path escapes theme")
                    collapsed.pop()
                else:
                    collapsed.append(part)
            rel = safe_relative_path("/".join(collapsed))
        record["classification"] = "theme"
        record["local_path"] = rel.as_posix()
        quoted_path = "/".join(
            __import__("urllib.parse", fromlist=["quote"]).quote(p, safe="!$&'()*+,;=:@")
            for p in rel.parts
        )
        record["normalized_remote_url"] = THEME_BASE + quoted_path
    except (ValueError, UnicodeError) as exc:
        record["classification"] = "unsafe"
        record["normalization_error"] = str(exc)
    return record


def classify_absolute(url: str, record: dict) -> dict:
    parsed = urlsplit(url)
    host = (parsed.hostname or "").lower()
    if host != "mlyn.honzakopta.cz":
        record["classification"] = "external"
        record["normalized_remote_url"] = url
        return record
    if parsed.path.startswith(THEME_WEB_PATH):
        try:
            rel = safe_relative_path(parsed.path[len(THEME_WEB_PATH):])
        except ValueError as exc:
            record["classification"] = "unsafe"
            record["normalization_error"] = str(exc)
            return record
        record["classification"] = "theme"
        record["local_path"] = rel.as_posix()
        record["normalized_remote_url"] = urlunsplit(
            ("https", "mlyn.honzakopta.cz", parsed.path, parsed.query, "")
        )
    else:
        record["classification"] = "site"
        record["normalized_remote_url"] = url
    return record


def iter_text_files(theme_dir: Path) -> Iterable[Path]:
    for root, dirs, files in os.walk(theme_dir):
        dirs[:] = sorted(d for d in dirs if d not in SKIP_DIRS)
        for name in sorted(files):
            path = Path(root, name)
            if path.suffix.lower() not in TEXT_EXTENSIONS:
                continue
            yield path


def read_text(path: Path) -> str | None:
    try:
        data = path.read_bytes()
        if b"\x00" in data[:4096]:
            return None
        return data.decode("utf-8", errors="replace")
    except OSError:
        return None


def collect_local(theme_dir: Path) -> list[dict]:
    records = []
    for path in iter_text_files(theme_dir):
        text = read_text(path)
        if text is None:
            continue
        source = path.relative_to(theme_dir).as_posix()
        allow_strings = path.suffix.lower() not in {".css", ".less", ".scss", ".svg"}
        for reference, kind in extract_references(text, allow_strings=allow_strings):
            if not is_asset_reference(reference.replace("theme://", "")):
                continue
            record = normalize_reference(reference, source)
            record["discovery_method"] = kind
            records.append(record)
    return records


def deduplicate(records: list[dict]) -> list[dict]:
    result = []
    seen = set()
    for record in records:
        key = (
            record["original_reference"],
            record["source_file"],
            record["normalized_remote_url"],
            record["local_path"],
        )
        if key not in seen:
            seen.add(key)
            result.append(record)
    return sorted(result, key=lambda item: (
        item["local_path"] or "",
        item["source_file"],
        item["original_reference"],
    ))


class Fetcher:
    def __init__(self, args: argparse.Namespace):
        self.args = args
        self.opener = build_opener(SameHostRedirectHandler())
        self.request_log: list[dict] = []
        self.last_request = 0.0

    def _wait(self):
        elapsed = time.monotonic() - self.last_request
        if elapsed < self.args.delay:
            time.sleep(self.args.delay - elapsed)

    def request(self, url: str, method: str = "GET") -> tuple[int, dict, bytes, str]:
        error = ""
        for attempt in range(self.args.retries + 1):
            self._wait()
            started = time.time()
            status = None
            final_url = url
            headers = {}
            body = b""
            try:
                req = Request(url, method=method, headers={"User-Agent": USER_AGENT, "Accept": "*/*"})
                with self.opener.open(req, timeout=self.args.timeout) as response:
                    status = response.status
                    final_url = response.geturl()
                    headers = dict(response.headers.items())
                    if method == "GET":
                        body = response.read()
                error = ""
            except HTTPError as exc:
                status = exc.code
                final_url = exc.geturl()
                headers = dict(exc.headers.items()) if exc.headers else {}
                error = str(exc)
            except (URLError, TimeoutError, OSError) as exc:
                error = str(exc)
            self.last_request = time.monotonic()
            entry = {
                "method": method, "url": url, "final_url": final_url,
                "status": status, "attempt": attempt + 1,
                "elapsed_seconds": round(time.time() - started, 3), "error": error,
            }
            self.request_log.append(entry)
            if self.args.verbose:
                print(f"{method} {url} -> {status or 'ERROR'} {error}", file=sys.stderr)
            if status is not None and status < 500:
                return status, headers, body, error
        return status or 0, headers, body, error


def plausible_body(path: str, mime: str, body: bytes) -> tuple[bool, str]:
    if not body:
        return False, "empty response body"
    prefix = body[:1024].lstrip().lower()
    ext = Path(path).suffix.lower()
    if ext not in {".html", ".htm"} and (
        prefix.startswith(b"<!doctype html") or prefix.startswith(b"<html")
    ):
        return False, "HTML response for non-HTML asset"
    expected, _ = mimetypes.guess_type(path)
    actual = mime.split(";", 1)[0].strip().lower()
    if actual == "text/html" and ext not in {".html", ".htm"}:
        return False, "HTML MIME type for asset"
    if expected and actual and actual not in {"application/octet-stream", expected}:
        compatible = (
            expected.startswith("font/") and actual in {
                "application/font-sfnt", "application/vnd.ms-fontobject",
                "application/x-font-ttf", "application/x-font-woff",
            }
        ) or (ext == ".js" and actual in {"application/javascript", "text/javascript"})
        if not compatible and not (
            expected.startswith("image/") and actual.startswith("image/")
        ):
            return False, f"implausible MIME type {actual!r}, expected {expected!r}"
    return True, ""


def fetch_asset(fetcher: Fetcher, record: dict, theme_dir: Path) -> dict:
    result = {
        **record, "status": "pending", "http_status": None, "mime_type": None,
        "byte_size": None, "sha256": None, "already_existed": False,
        "downloaded": False, "error_message": None,
    }
    rel = record["local_path"]
    destination = (theme_dir / rel).resolve()
    if theme_dir != destination and theme_dir not in destination.parents:
        result["status"] = "unsafe"
        result["error_message"] = "resolved destination escapes theme directory"
        return result
    result["already_existed"] = destination.is_file()
    status, headers, _, head_error = fetcher.request(record["normalized_remote_url"], "HEAD")
    if status in {403, 405, 501} or not status:
        status = 0
    if status and status != 200:
        result["status"] = "missing"
        result["http_status"] = status
        result["error_message"] = head_error or f"HEAD returned HTTP {status}"
        return result
    status, headers, body, error = fetcher.request(record["normalized_remote_url"], "GET")
    result["http_status"] = status
    mime = headers.get("Content-Type", "")
    result["mime_type"] = mime
    if status != 200:
        result["status"] = "missing"
        result["error_message"] = error or f"GET returned HTTP {status}"
        return result
    valid, validation_error = plausible_body(rel, mime, body)
    if not valid:
        result["status"] = "suspicious"
        result["error_message"] = validation_error
        return result
    digest = hashlib.sha256(body).hexdigest()
    result["byte_size"] = len(body)
    result["sha256"] = digest
    if destination.is_file():
        local = destination.read_bytes()
        if hashlib.sha256(local).hexdigest() == digest:
            result["status"] = "already-present"
            return result
    destination.parent.mkdir(parents=True, exist_ok=True)
    fd, temp_name = tempfile.mkstemp(prefix=".theme-recovery-", dir=destination.parent)
    try:
        with os.fdopen(fd, "wb") as handle:
            handle.write(body)
            handle.flush()
            os.fsync(handle.fileno())
        os.replace(temp_name, destination)
    finally:
        if os.path.exists(temp_name):
            os.unlink(temp_name)
    result["status"] = "downloaded"
    result["downloaded"] = True
    return result


def discover_public(fetcher: Fetcher, internal_limit: int) -> list[dict]:
    records: list[dict] = []
    pages = deque([(SITE_ORIGIN + "/", "rendered:homepage")])
    visited_pages = set()
    linked_pages = 0
    remote_text = deque()
    seen_remote_text = set()
    while pages:
        url, source = pages.popleft()
        if url in visited_pages:
            continue
        visited_pages.add(url)
        status, headers, body, _ = fetcher.request(url, "GET")
        if status != 200 or not body:
            continue
        text = body.decode("utf-8", errors="replace")
        for ref, kind in extract_references(text):
            absolute = urljoin(url, html.unescape(ref))
            rec = normalize_reference(absolute, source)
            rec["original_reference"] = ref
            rec["discovery_method"] = "rendered-" + kind
            if rec["classification"] == "theme" and is_asset_reference(absolute):
                records.append(rec)
                if Path(urlsplit(absolute).path).suffix.lower() in {".css", ".js"}:
                    remote_text.append((rec["normalized_remote_url"], rec["local_path"]))
        if source == "rendered:homepage" and linked_pages < internal_limit:
            for href in re.findall(r"""\bhref\s*=\s*(['"])(.*?)\1""", text, re.I | re.S):
                candidate = urljoin(url, html.unescape(href[1]))
                parsed = urlsplit(candidate)
                if (
                    parsed.hostname == "mlyn.honzakopta.cz"
                    and not parsed.path.startswith(("/wp-admin", "/wp-content/uploads/"))
                    and not parsed.path.rstrip("/").endswith("/feed")
                    and "ical=" not in parsed.query.lower()
                    and not Path(parsed.path).suffix
                    and candidate not in visited_pages
                ):
                    pages.append((candidate, f"rendered:internal-page-{linked_pages + 1}"))
                    linked_pages += 1
                    if linked_pages >= internal_limit:
                        break
    while remote_text:
        url, local_path = remote_text.popleft()
        if url in seen_remote_text:
            continue
        seen_remote_text.add(url)
        status, _, body, _ = fetcher.request(url, "GET")
        if status != 200 or not body:
            continue
        text = body.decode("utf-8", errors="replace")
        allow_strings = Path(urlsplit(url).path).suffix.lower() not in {
            ".css", ".less", ".scss", ".svg"
        }
        for ref, kind in extract_references(text, allow_strings=allow_strings):
            if not is_asset_reference(ref):
                continue
            rec = normalize_reference(ref, local_path)
            rec["source_file"] = "rendered:" + local_path
            rec["discovery_method"] = "remote-" + kind
            records.append(rec)
            if rec["classification"] == "theme" and Path(rec["local_path"]).suffix.lower() in {".css", ".js"}:
                remote_text.append((rec["normalized_remote_url"], rec["local_path"]))
    return records


def merge_asset_records(records: list[dict]) -> list[dict]:
    merged = {}
    for rec in records:
        if rec["classification"] != "theme" or not rec["local_path"]:
            continue
        key = rec["local_path"]
        if key not in merged:
            merged[key] = dict(rec)
            merged[key]["references"] = []
        merged[key]["references"].append({
            "original_reference": rec["original_reference"],
            "source_file": rec["source_file"],
            "discovery_method": rec.get("discovery_method"),
        })
    return sorted(merged.values(), key=lambda item: item["local_path"])


def probe_site_root_candidates(fetcher: Fetcher, records: list[dict]) -> None:
    """Audit whether site-root asset basenames also exist below the theme base."""
    cache = {}
    for record in records:
        if record["classification"] != "site-root":
            continue
        path = urlsplit(record["normalized_remote_url"]).path
        try:
            rel = safe_relative_path(path.lstrip("/"))
        except ValueError:
            continue
        candidate = THEME_BASE + rel.as_posix()
        if candidate not in cache:
            body = b""
            status, headers, _, error = fetcher.request(candidate, "HEAD")
            if status in {403, 405, 501} or not status:
                status, headers, body, error = fetcher.request(candidate, "GET")
            elif status == 200:
                status, headers, body, error = fetcher.request(candidate, "GET")
            mime = headers.get("Content-Type", "")
            valid, validation_error = plausible_body(rel.as_posix(), mime, body)
            cache[candidate] = (
                status, error, mime, len(body), hashlib.sha256(body).hexdigest() if body else None,
                bool(status == 200 and valid), validation_error,
            )
        status, error, mime, size, digest, available, validation_error = cache[candidate]
        record["theme_candidate_url"] = candidate
        record["theme_candidate_http_status"] = status
        record["theme_candidate_mime_type"] = mime
        record["theme_candidate_byte_size"] = size
        record["theme_candidate_sha256"] = digest
        record["theme_candidate_available"] = available
        record["theme_candidate_validation_error"] = validation_error or None
        record["theme_candidate_error"] = error or None


def write_reports(report_dir: Path, records: list[dict], results: list[dict], requests: list[dict]):
    report_dir.mkdir(parents=True, exist_ok=True)
    discovery = {
        "site_origin": SITE_ORIGIN,
        "theme_base_url": THEME_BASE,
        "references": records,
        "unique_theme_assets": merge_asset_records(records),
    }
    (report_dir / "discovered-assets.json").write_text(
        json.dumps(discovery, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    summary = summarize(records, results)
    download_report = {"summary": summary, "assets": results, "request_log": requests}
    (report_dir / "download-report.json").write_text(
        json.dumps(download_report, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    missing = [
        f"{item['local_path']}\t{item['normalized_remote_url']}\t{item.get('error_message') or ''}"
        for item in results if item["status"] in {"missing", "suspicious", "unsafe"}
    ]
    (report_dir / "missing-assets.txt").write_text(
        ("\n".join(missing) + "\n") if missing else "", encoding="utf-8"
    )
    external = [
        f"{item['classification']}\t{item['source_file']}\t{item['original_reference']}\t"
        f"{item.get('normalized_remote_url') or ''}"
        for item in records if item["classification"] != "theme"
    ]
    (report_dir / "external-assets.txt").write_text(
        ("\n".join(external) + "\n") if external else "", encoding="utf-8"
    )


def summarize(records: list[dict], results: list[dict]) -> dict:
    return {
        "references_discovered": len(records),
        "unique_normalized_assets": len(results),
        "downloaded": sum(x["status"] == "downloaded" for x in results),
        "already_present": sum(x["status"] == "already-present" for x in results),
        "missing": sum(x["status"] == "missing" for x in results),
        "external_or_ignored": sum(x["classification"] != "theme" for x in records),
        "suspicious_responses": sum(x["status"] == "suspicious" for x in results),
    }


def main() -> int:
    args = parse_args()
    theme_dir = Path(args.theme_dir).expanduser().resolve()
    if not theme_dir.is_dir():
        print(f"error: theme directory does not exist: {theme_dir}", file=sys.stderr)
        return 2
    script_dir = Path(__file__).resolve().parent
    records = collect_local(theme_dir)
    fetcher = Fetcher(args)
    if not args.dry_run:
        records.extend(discover_public(fetcher, max(0, args.internal_pages)))
    records = deduplicate(records)
    if not args.dry_run:
        probe_site_root_candidates(fetcher, records)
    assets = merge_asset_records(records)
    results = []
    if args.dry_run:
        for asset in assets:
            destination = (theme_dir / asset["local_path"]).resolve()
            results.append({
                **asset, "status": "dry-run", "http_status": None, "mime_type": None,
                "byte_size": destination.stat().st_size if destination.is_file() else None,
                "sha256": hashlib.sha256(destination.read_bytes()).hexdigest()
                if destination.is_file() else None,
                "already_existed": destination.is_file(), "downloaded": False,
                "error_message": None,
            })
    else:
        for asset in assets:
            results.append(fetch_asset(fetcher, asset, theme_dir))
    write_reports(script_dir, records, results, fetcher.request_log)
    summary = summarize(records, results)
    print(json.dumps(summary, ensure_ascii=False, indent=2))
    if args.dry_run:
        print("\nNormalized mappings:")
        for asset in assets:
            print(f"{asset['normalized_remote_url']} -> {asset['local_path']}")
        return 0
    return 1 if any(x["status"] in {"missing", "suspicious", "unsafe"} for x in results) else 0


if __name__ == "__main__":
    raise SystemExit(main())
