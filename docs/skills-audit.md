# Cursor skills audit (2026-10-27)

Audit of project skills under `.cursor/skills/` and of common **platform** skills agents may also load in Cloud Agents.

## Scope

| Skill | Location | Verdict |
|-------|----------|---------|
| `plugin-architecture` | `.cursor/skills/plugin-architecture/SKILL.md` | **Added** — fact-checked against `includes/` |
| `release-workflow` | `.cursor/skills/release-workflow/SKILL.md` | **Added** — fact-checked against workflow + git layout |
| `code-quality` | `.cursor/skills/code-quality/SKILL.md` | **Added** — fact-checked against `composer.json` / `phpcs.xml` |

## Errors corrected during this audit

Claims that were **wrong or misleading** in informal release notes / agent context and are **corrected** in the skills above:

| Issue | Fact (repo evidence) | Skill fix |
|-------|----------------------|-----------|
| “Merge fix PR to `master`” | `prepare-production.yml` force-pushes `develop` → `master`; unrelated `master` commits are lost | `release-workflow`: work on `develop` only |
| “Taxonomy attaches to field groups” | `register_taxonomy( ..., array() )`; assignments are meta `_acf_field_categories` | `plugin-architecture`: data model section |
| “Translations work out of the box” | `load_textdomain()` uses derived name with spaces ≠ `codesoup-acf-admin-categories` (`includes/core/class-init.php`) | `plugin-architecture`: known bug + fix reference |
| “Run npm build before release” | No `package.json`; workflow still installs Node 22 | `release-workflow`: unused Node step noted |
| “Ship with Composer autoload” | Production copy excludes `vendor/`; `run.php` uses fallback autoloader | `plugin-architecture` + `release-workflow` |
| “`develop` and `master` share history” | GitHub compare: no common ancestor | `release-workflow`: orphan `master` |
| “Automated tests gate release” | No test suite; only `composer lint` / `wpcs` | `code-quality` |

## Inconsistencies between skills (resolved)

| Topic | Before | Resolution |
|-------|--------|------------|
| Where version lives | README only mentioned CHANGELOG | All three skills point to `index.php` + `run.php` + CHANGELOG |
| Metadata files | Implied single header | `release-workflow` table lists `readme.txt` after PR #4 |
| Partial templates | Assumed all `parts/` active | `plugin-architecture`: legacy partials warning |

## Platform skills (not in this repo) — relevance & accuracy for **this** project

These ship with Cursor Cloud Agents globally. They are **not** duplicated in `.cursor/skills/` to avoid drift.

| Platform skill | Use for this repo? | Accuracy / notes |
|----------------|-------------------|------------------|
| `env-setup` | **Sometimes** — if configuring Cloud Agent `environment.json` (PHP 8.1, Composer) | Accurate for Cloud VM model; this repo has no committed `environment.json` yet |
| `migrate-to-builds` | **If** adopting prebuilt agent environments | Accurate; optional for this plugin |
| `walkthrough-artifacts` | **Yes** — GUI proof for admin UI changes | Accurate; needs local WP + ACF (not in default agent image) |
| `canvas` | **Optional** — release dashboards / audit reports | Accurate |
| `subscribe` | **Optional** — wait on PR CI instead of polling | Accurate; repo has minimal CI (release workflow only) |
| `bdk-*` (Agent SDK) | **No** — not an Agent SDK project | Correct to **ignore**; following them here would be misleading |
| `bdk-cli` | **No** | N/A |

## Example fact-checks (spot checks)

1. **ACF post type constant** — `Admin\Init::ACF_POST_TYPE` = `acf-field-group` ✓ (`includes/admin/class-init.php`).
2. **Text domain in header** — `codesoup-acf-admin-categories` ✓ (`index.php`).
3. **Workflow trigger** — `workflow_dispatch` only ✓ (`.github/workflows/prepare-production.yml`).
4. **PHP require** — `>=8.1.0` in Composer, `8.1` in header ✓ (`composer.json`, `index.php`).
5. **CHANGELOG semver note** — 1.0.2 dated after 1.0.3 in file order is historical inconsistency in CHANGELOG only; skills do not repeat those dates.

## Maintenance

When changing release mechanics or storage model, update **both** the relevant `SKILL.md` and this audit (or add a dated subsection).

Pending merge **PR #4** adds `readme.txt`, `Tested up to`, and extended `validate-version.sh` — `release-workflow` already references that; after merge, remove “before PR #4” caveats in a follow-up commit.
