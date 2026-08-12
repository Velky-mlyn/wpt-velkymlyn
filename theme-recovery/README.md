# Velký Mlýn theme asset recovery

This directory contains a repeatable audit and recovery tool for public files
referenced by the `velkymlyn` WordPress theme. It uses only the Python standard
library and does not modify existing source files.

From the directory containing the theme:

```sh
python3 velkymlyn/theme-recovery/recover_theme_assets.py \
  --theme-dir ./velkymlyn --dry-run --verbose

python3 velkymlyn/theme-recovery/recover_theme_assets.py \
  --theme-dir ./velkymlyn --verbose
```

From inside the theme directory, use `--theme-dir .`.

Options include `--retries`, `--timeout`, `--delay`, and `--internal-pages`.
Dry-run performs local discovery and mapping only. A real run fetches the
homepage and at most three representative internal pages by default, discovers
theme CSS/JS used by rendered markup, and recursively inspects those files.

## Safety and mapping

- Theme-absolute paths beginning with
  `/wp-content/themes/velkymlyn/` map directly below the resolved theme root.
- Relative paths are based on the directory of the file containing the
  reference.
- WordPress theme-directory URI expressions are based at the theme root.
- Site-root paths such as `/favicon.svg` are reported separately and are not
  copied into the theme.
- Other hosts, uploads, data URLs, anchors, and non-assets are not downloaded.
- Decoded traversal, encoded path separators, NUL bytes, and destinations
  outside the resolved theme root are rejected.
- Redirects are allowed only when the target host remains
  `mlyn.honzakopta.cz`.
- Downloads require HTTP 200, a non-empty non-HTML asset body, and a plausible
  MIME type. Each accepted body receives a SHA-256 digest.
- A temporary sibling file is fully written before atomic replacement.
  Existing identical files are retained; existing files are replaced only by
  a successfully validated differing response.

The script exits nonzero if an asset is missing, unsafe, or suspicious.

## Reports

- `discovered-assets.json`: every reference and normalized unique asset.
- `download-report.json`: per-asset result plus every HTTP request and result.
- `missing-assets.txt`: missing, unsafe, and suspicious theme assets.
- `external-assets.txt`: external, site-root, and otherwise ignored references.

Reports are overwritten on each run so they describe that run exactly.

## Recovery audit (2026-07-29)

The reviewed recovery run found 54 references representing 22 unique local
theme destinations. Fourteen previously absent assets were downloaded: ten
files under `image/`, `slick/ajax-loader.gif`, and three missing Slick font
files (`.eot`, `.ttf`, and `.woff`). Eight destinations were already present.
No required theme asset was missing or suspicious.

A second real run downloaded nothing and validated all 22 destinations as
already present, demonstrating idempotence. The final `download-report.json`
describes that verification run. Eleven external or site-root references are
listed in `external-assets.txt`; this includes the intentionally ignored Travis
CI badge. The five favicon/manifest references were validated as available
under the theme base too, but were not copied because the source explicitly
links them from the site root.
