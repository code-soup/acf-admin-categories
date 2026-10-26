=== CodeSoup ACF Admin Categories ===
Contributors: codesoup
Tags: acf, advanced-custom-fields, custom-fields, taxonomy, organization
Requires at least: 6.0
Tested up to: 7.1.3
Requires PHP: 8.1
Stable tag: 1.0.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Organize Advanced Custom Fields field groups with custom categories, filtering, and sorting in the WordPress admin.

== Description ==

CodeSoup ACF Admin Categories extends Advanced Custom Fields by adding a taxonomy for field groups. Categorize field groups, filter and sort them on the field groups list screen, and assign categories from each field group's settings.

**Requires** Advanced Custom Fields (ACF) Pro or Free.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the release ZIP via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** menu.
3. Use **Custom Fields → Field Categories** to create categories and assign them from field group settings.

== Changelog ==

= 1.0.4 =
* Fixed text domain loading for bundled translations.
* Fixed category filtering on the field groups list.
* Scoped admin list query hooks to the field groups screen.
* Added capability check when saving field group categories.
* Updated WordPress compatibility metadata (tested up to 7.1.3).

= 1.0.3 =
* Sortable category column; category filtering and standalone plugin fixes.
