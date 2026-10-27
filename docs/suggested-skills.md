# Suggested additional Cursor skills (not yet in repo)

Recommendations for **separate** skills to add after [PR #5 skills audit](.cursor/skills/) lands. Each should live under `.cursor/skills/<name>/SKILL.md` with frontmatter `name` + `description`.

## High priority

### `wordpress-manual-qa`

**Why:** No automated tests; releases depend on ACF admin flows.

**Should cover:**

- Prerequisites: WordPress + ACF Pro or Free, plugin activated
- Checklist: create terms, assign on field group tab, list column, dropdown filter, column sort, multi-category group, user without `edit_post` cannot save categories
- How to record evidence (`walkthrough-artifacts` platform skill)

**Fact anchor:** Meta key `_acf_field_categories`, screen `edit-acf-field-group`.

---

### `wordpress-metadata-release`

**Why:** PR #4 adds `readme.txt`, `Tested up to`, and extended `validate-version.sh`; easy to drift from `index.php`.

**Should cover:**

- Full matrix: version, Requires at least, Tested up to (latest stable WP you verified), Requires PHP, Stable tag
- Policy: bump **Tested up to** when QA passes on a new WordPress security release
- Run `./scripts/validate-version.sh` before every tag

**Depends on:** PR #4 merged into `develop`.

---

### `phpcs-change-discipline`

**Why:** Large admin class; PHPCS ignores are easy to misuse.

**Should cover:**

- When `phpcs:ignore` is acceptable (serialized meta, direct `$wpdb` with prepare)
- Run `composer wpcs` / `composer cbf` on `develop` only
- Do not copy `phpcs.xml` to `master` artifact (excluded by design)

---

## Medium priority

### `cloud-agent-environment`

**Why:** This repo has no committed `environment.json`; agents need PHP 8.1 + Composer for lint.

**Should cover:**

- Minimal `install`: `composer install`
- Optional: link to platform `env-setup` skill for builds/snapshots
- Explicitly **not** required: Node, Docker, MySQL unless adding integration tests

---

### `security-review-wp-plugin`

**Why:** Admin saves, nonces, capabilities, and SQL are security-sensitive.

**Should cover:**

- Review checklist for new `$_POST` handlers, AJAX, and `$wpdb` queries
- Map to existing patterns in `save_field_group_categories()`
- When to escalate to human review (capability changes, new endpoints)

**Note:** Use platform `security-review` subagent for diff reviews; this skill is **repo-specific** conventions.

---

## Low priority / future

| Skill | Trigger |
|-------|---------|
| `taxonomy-refactor` | If field groups attach to `acf_admin_category` natively |
| `composer-theme-integration` | Changes to `run.php` bootstrap or `codesoup_acf_admin_categories_plugin_dir_url` filter |
| `github-release-assets` | Automating ZIP + tag after production workflow |

## Skills to **not** add (misleading here)

| Skill | Reason |
|-------|--------|
| Any `bdk-*` Agent SDK skill | Wrong product; this is a WordPress plugin only |
| `npm-frontend-build` | No frontend build pipeline |
| `direct-master-release` | Contradicts force-push production model |

## Suggested implementation order

1. `wordpress-manual-qa`
2. `wordpress-metadata-release` (after PR #4)
3. `phpcs-change-discipline`
4. `cloud-agent-environment`

After each new skill is added, append a row to `docs/skills-audit.md` under **Scope**.
