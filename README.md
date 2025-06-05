# ACF Admin Categories

A WordPress plugin that adds category organization to Advanced Custom Fields (ACF) field groups for better management and organization.

## Description

ACF Admin Categories extends the Advanced Custom Fields plugin by adding a custom taxonomy system that allows you to categorize and organize your field groups. This makes it easier to manage large numbers of field groups by providing filtering, sorting, and visual organization tools.

### Features

- **Custom Taxonomy**: Create and manage field group categories
- **Field Group Integration**: Assign categories directly from ACF field group settings
- **Advanced Filtering**: Filter field groups by category in the admin list
- **Category Column**: View assigned categories at a glance in the field groups list
- **Admin Menu Integration**: Seamlessly integrated into the ACF admin interface
- **Hierarchical Categories**: Support for parent/child category relationships

### Use Cases

- **Large Projects**: Organize field groups by project sections (Header, Footer, Content, etc.)
- **Multi-Site Management**: Categorize field groups by site or client
- **Development Workflow**: Separate field groups by development status (Active, Testing, Deprecated)
- **Content Types**: Group field groups by content type (Pages, Posts, Products, etc.)

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- Advanced Custom Fields (ACF) Pro or Free version
- Composer (for development installation)

## Installation

### Via WordPress Admin (Recommended)

1. Download the plugin ZIP file from the [releases page](https://github.com/code-soup/acf-admin-categories/releases)
2. Log in to your WordPress admin dashboard
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin**
5. Choose the downloaded ZIP file and click **Install Now**
6. Activate the plugin through the **Plugins** menu

### Via Composer

```bash
composer require codesoup/acf-admin-categories
```

**Note**: When installing via Composer, you need to manually include the plugin in your project:

```php
// Add this to your theme's functions.php
add_filter( 'acf_admin_categories_plugin_dir_url', function( $base_url ) {

    return sprintf(
        '%s/vendor/acf-admin-categories',
        get_stylesheet_directory_uri()
    );
});

require_once __DIR__ . '/vendor/acf-admin-categories/index.php';
```

### Manual Installation

1. Download the plugin files
2. Upload the `acf-admin-categories` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the **Plugins** menu in WordPress

## Usage

### Creating Field Categories

1. Navigate to **Custom Fields > Field Categories** in your WordPress admin
2. Click **Add New Category**
3. Enter a name and optional description for your category
4. Click **Add New Category** to save

### Assigning Categories to Field Groups

1. Edit any ACF Field Group
2. Click the **Field Category** tab in the field group settings
3. Check the categories you want to assign to this field group
4. Save the field group

### Filtering Field Groups

1. Go to **Custom Fields > Field Groups**
2. Use the category dropdown filter above the list to filter by specific categories
3. Click on any category name in the **Category** column to filter by that category

### Managing Categories

- **Edit Categories**: Go to **Custom Fields > Field Categories** to edit existing categories

## Screenshots

### 1. Field Groups with Categories


![Field Groups with Categories](https://raw.githubusercontent.com/code-soup/acf-admin-categories/develop/screenshots/screenshot-01.png)

### 2. Category Assignment UI  
Assign categories from field group settings.

![Category Assignment UI](https://raw.githubusercontent.com/code-soup/acf-admin-categories/develop/screenshots/screenshot-02.png)

### 3. Admin Menu
Access categories management from ACF admin menu

![Admin Menu](https://raw.githubusercontent.com/code-soup/acf-admin-categories/develop/screenshots/screenshot-03.png)

### 4. Manage Field Group Categories
Create and manage field group categories in usual WordPress way

![Manage Field Group Categories](https://raw.githubusercontent.com/code-soup/acf-admin-categories/develop/screenshots/screenshot-04.png)


## Frequently Asked Questions

### Does this plugin require ACF Pro?

No, this plugin works with both ACF Free and ACF Pro versions.

### Will this affect my existing field groups?

No, existing field groups will continue to work normally. Categories are optional and additive.

### Can I assign multiple categories to one field group?

Yes, field groups can be assigned to multiple categories for flexible organization.

## Development

### Requirements

- Node.js >= 20.19.2
- Composer
- Yarn or npm

### Setup

```bash
# Clone the repository
git clone https://github.com/code-soup/acf-admin-categories.git
cd acf-admin-categories

# Install PHP dependencies
composer install

# Install Node.js dependencies
yarn install

# Build assets
yarn build

# Development with hot reload
yarn dev
```

### Code Standards

This plugin follows WordPress coding standards:

```bash
# Check PHP code standards
composer run phpcs

# Fix PHP code standards
composer run phpcbf

# Lint PHP files
composer run lint

# Run all checks
composer run ci
```

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Changelog

### 0.0.1
- Initial release
- Custom taxonomy for field group categories
- Category assignment interface in ACF field groups
- Category filtering and column display
- Admin menu integration

## Support

Please use [GitHub Issues](https://github.com/code-soup/acf-admin-categories/issues) to submit any bugs or feature requests.

## License

This project is licensed under the GPL v3 or later - see the [LICENSE.txt](LICENSE.txt) file for details.

## Credits

- Built by [CodeSoup](https://www.codesoup.co)
- Based on the [WordPress Plugin Boilerplate](https://github.com/code-soup/wordpress-plugin-boilerplate)
- Designed to work seamlessly with [Advanced Custom Fields](https://www.advancedcustomfields.com/)

## Donate

Found this plugin useful and would like to donate? Consider supporting a local charity.
People in need are closer than we think.