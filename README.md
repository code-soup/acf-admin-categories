
# CodeSoup ACF Admin Categories

CodeSoup ACF Admin Categories extends the Advanced Custom Fields plugin by adding a custom taxonomy system that allows you to categorize and organize your field groups. This makes it easier to manage large numbers of field groups by providing filtering, sorting, and visual organization tools.


### Use Cases
- **Large Projects**: Organize field groups by project sections (Header, Footer, Content, etc.)
- **Development Workflow**: Separate field groups by development status (Active, Testing, Deprecated)

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- Advanced Custom Fields (ACF) Pro or Free version
- Composer (for development installation)

## Installation

### Via WordPress Admin

1. Download the plugin ZIP file from the [releases page](https://github.com/code-soup/acf-admin-categories/releases)
2. Navigate to **Plugins > Add New > Upload Plugin**
3. Choose the downloaded ZIP file and click **Install Now**
4. Activate the plugin through the **Plugins** menu

### Via Composer

```bash
composer require codesoup/acf-admin-categories
```

### Manual Installation

1. Download the plugin files
2. Upload the `codesoup-acf-admin-categories` folder to your `/wp-content/plugins/` directory
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
- **Hierarchical Structure**: Create parent/child relationships between categories
- **Bulk Management**: Use WordPress's built-in bulk actions for category management

## Screenshots

1. **Field Group Categories Management** - Create and manage your field group categories
2. **Category Assignment Interface** - Assign categories directly from field group settings
3. **Filtered Field Groups List** - Filter and view field groups by category
4. **Category Column** - See assigned categories at a glance

## Changelog

### 1.0.0
- Initial release
- Custom taxonomy for field group categories
- Category assignment interface in ACF field groups
- Category filtering and column display

## Support

Please use [GitHub Issues](https://github.com/code-soup/acf-admin-categories/issues) to submit any bugs or feature requests. 

## License

This project is licensed under the GPL v3 or later - see the [LICENSE.txt](LICENSE.txt) file for details.
