---
name: code-quality
description: >-
  Lint and coding standards for CodeSoup ACF Admin Categories. Use before release
  or when editing PHP in includes/.
---
# Code quality

## Requirements

- PHP **8.1+** (`declare(strict_types=1);` in plugin classes)
- WordPress Coding Standards via PHPCS

## Composer scripts (run on `develop` with dev dependencies)

```bash
composer install
composer lint    # parallel-lint, excludes vendor and .git
composer wpcs   # PHPCS full report
composer cbf    # auto-fix where possible
```

Config: `phpcs.xml` (WPCS + PHPCompatibility WP).

## Scope discipline

- Match existing patterns in `includes/admin/class-init.php` and `HelpersTrait`.
- Admin-only SQL: follow existing `phpcs:ignore` comments for direct queries; prefer core APIs when they fit serialized meta behavior.
- Security: nonces and capabilities on saves; sanitize `$_GET`/`$_POST` per WPCS.

## Tests

There is **no** automated PHPUnit/integration suite in this repository. Release confidence depends on manual ACF admin QA and PHPCS/lint.

## Uninstall

`uninstall.php` handles cleanup on plugin delete; consider meta/transients when changing storage.
