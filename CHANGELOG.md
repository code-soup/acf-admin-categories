# Changelog

All notable changes to CodeSoup ACF Admin Categories will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.4] - 2026-10-26

### Fixed

- Load the correct `codesoup-acf-admin-categories` text domain so bundled translations apply
- Category list filtering matches serialized category assignments accurately
- Admin query hooks run only on the ACF field groups list screen
- Capability check when saving field group category assignments

### Changed

- WordPress compatibility metadata: tested up to 7.1.3 (requires WordPress 6.0+, PHP 8.1+)

## [1.0.3] - 2026-04-20

### Added

- Sortable category column - click header to sort alphabetically

### Fixed

- Category filtering now works correctly
- Plugin works standalone without composer
- Composer theme integration timing issue

## [1.0.2] - 2025-06-15

### Fixed

- Code review improvements from comprehensive analysis
- Version consistency across all files
- Performance optimizations (N+1 query elimination, batch operations)
- Type safety improvements (WP_Error check ordering)
- Security enhancements (sanitization, validation, ACF dependency check)
- Error handling improvements (exception escaping, hook timing)
- Added uninstall.php for complete cleanup
- Added fallback autoloader for Composer package support

### Changed

- PHP requirement: 8.1 (was 8.0)
- Made $constants private with public getters
- Improved cache management with version-based keys
- Enhanced documentation for architecture decisions

## [1.0.1] - 2025-06-12

- Removed unnecessary npm packages
- WPCS standards enforced

## [1.0.0] - 2025-03-25

### Changed

- Complete codebase refactoring and optimization
- Removed unnecessary abstractions
- Converted to pure PSR-4 autoloading
- Eliminated code duplication
- Full WordPress Coding Standards (PHPCS) compliance
- Reduced codebase by ~2,218 lines while maintaining all functionality

## [0.0.1] - 2025-01-03

### Added

- Initial release
- Custom taxonomy for organizing ACF field groups
- Category assignment interface in ACF field groups
- Category filtering and management in admin
- WordPress and ACF integration
