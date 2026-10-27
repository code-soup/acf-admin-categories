---
name: release-workflow
description: >-
  Ship CodeSoup ACF Admin Categories from develop to master and GitHub Releases.
  Use for version bumps, changelogs, production promote, and tags.
---
# Release workflow

## Branch model

| Branch | Purpose |
|--------|---------|
| **`develop`** | Full repo: plugin source, `scripts/`, PHPCS, Composer dev tools |
| **`master`** | Production-only mirror; **orphan history** (no shared merge-base with `develop` on GitHub) |

**Never** treat a direct merge into `master` as durable: the next production workflow **force-pushes** `master` from `develop` and will drop stray commits.

All feature and release prep work lands on **`develop`** (or a branch targeting `develop`).

## Version and WordPress metadata

Keep these aligned before tagging:

| Field | Locations |
|-------|-----------|
| Plugin version | `index.php` (`Version`), `run.php` (`PLUGIN_VERSION`), top entry in `CHANGELOG.md` |
| Requires at least | `index.php`, `run.php` (`MIN_WP_VERSION`) |
| Requires PHP | `index.php`, `run.php` (`MIN_PHP_VERSION`), `composer.json` `require.php` |
| Tested up to | After PR #4: `index.php`, `readme.txt`, `run.php` (`TESTED_WP_VERSION`) — set to **latest stable WordPress** you verified (e.g. 7.1.3) |

On `develop` before PR #4 merges: run `./scripts/validate-version.sh` (version + PHP only). After PR #4: script also checks WordPress fields and `readme.txt`.

```bash
./scripts/validate-version.sh
```

## Promote to production

1. Merge release PRs into **`develop`**.
2. Manual QA on WordPress + ACF (field categories, list filter/sort, permissions).
3. **Actions → Prepare Production Release** (`workflow_dispatch`, checks out **`develop`**).
4. Workflow runs `composer install --no-dev`, copies allowed paths to `/tmp/production-release`, `git init`, **force-push `HEAD:master`**.
5. Create GitHub **Release** tag `vX.Y.Z` and attach ZIP (workflow does **not** tag automatically).

### Copied to `master` (verify against `.github/workflows/prepare-production.yml`)

Includes: `includes/`, `.github/`, `languages/`, `assets/` (if present), `composer.json`, `index.php`, `run.php`, `uninstall.php`, `LICENSE.txt`, `README.md`, `CHANGELOG.md`, and `readme.txt` once PR #4 is merged.

**Not** copied: `scripts/`, `phpcs.xml`, `vendor/`, dev config.

## PR hygiene

- Open PRs against **`develop`**, not `master`, unless you are only fixing the production workflow file on develop.
- Cloud agent branches: `cursor/<name>-50a7`.

## Fact check: workflow quirks

- **Node.js** is installed in the workflow but this plugin has **no** `package.json`; step is unused for builds today.
- **`.github`** is copied into the production tree (workflows ship in the release artifact).
