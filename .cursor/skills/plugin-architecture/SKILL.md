---
name: plugin-architecture
description: >-
  CodeSoup ACF Admin Categories layout, bootstrap, data model, and ACF integration.
  Use when changing PHP under includes/, hooks, categories, or admin UI.
---
# ACF Admin Categories — architecture

## Bootstrap

| File | Role |
|------|------|
| `index.php` | WordPress plugin header; requires `run.php` only |
| `run.php` | Autoloader (Composer `vendor/` or PSR-4 fallback), sets constants, hooks `after_setup_theme` priority 20 |
| `includes/class-autoloader.php` | Production fallback when `vendor/` is absent |
| `includes/core/class-init.php` | Singleton core: ACF check, textdomain, loads `Admin\Init` in admin |
| `includes/admin/class-init.php` | Taxonomy, field group UI, list columns, filters, saves |

Namespace root: `CodeSoup\ACFAdminCategories\`.

## Hard dependency

ACF must be active. If not, core shows an admin notice and does not load admin features.

## Categories data model (important)

- **Taxonomy** `acf_admin_category` is registered with **no post types** (`register_taxonomy( ..., array() )`). Terms are managed under **Custom Fields → Field Categories**.
- **Assignments** to field groups are stored in post meta `_acf_field_categories` as an array of term IDs — not via `wp_set_object_terms` on `acf-field-group`.
- Field group post type: `acf-field-group`.

Do not assume native term relationships on field groups; list filtering reads meta (see admin class).

## Admin partials

`includes/admin/parts/*.php` are legacy/template fragments; the live UI is largely inline in `includes/admin/class-init.php`. Check whether a partial is included before editing it.

## i18n

- Header and strings use text domain **`codesoup-acf-admin-categories`**.
- Bundled MO: `languages/en_US.mo`.
- **Known bug on `develop` (≤1.0.3):** `Core\Init::load_textdomain()` derives the domain from the display name (spaces), so MO files do not load. Use the literal domain `codesoup-acf-admin-categories` (fixed on release branch / PR #4).

## Constants

Runtime constants are set in `run.php` via `Core\Init::set_constants()` (`PLUGIN_VERSION`, `MIN_WP_VERSION`, `MIN_PHP_VERSION`, etc.). `HelpersTrait` reads them; avoid duplicating version strings in business logic.

## Production artifact

`master` is a **stripped tree** (no `scripts/`, `phpcs.xml`, or dev `vendor/`). Production relies on the built-in autoloader, not Composer autoload in the shipped ZIP.
