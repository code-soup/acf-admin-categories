<?php

namespace CodeSoup\ACFAdminCategories;

// If this file is called directly, abort.
defined('ABSPATH') || die;

// Load composer autoloader for dependencies
$autoload_file = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload_file)) {
    require $autoload_file;
}

// Load our custom WordPress-compatible PSR-4 autoloader
require_once "includes/core/Autoloader.php";

// Register the autoloader
$autoloader = new \CodeSoup\ACFAdminCategories\Core\Autoloader();
$autoloader->register();

/**
 * Make main plugin class available via global function call.
 *
 * @since 1.0.0
 * @return Core\Init Main plugin instance.
 */
function plugin_instance(): Core\Init {
    return Core\Init::get_instance();
}

try {
    // Init plugin
    $plugin = plugin_instance();
    $plugin->set_constants([
        'MIN_WP_VERSION_SUPPORT_TERMS' => '6.0',
        'MIN_WP_VERSION'               => '6.0',
        'MIN_PHP_VERSION'              => '8.0',
        'MIN_MYSQL_VERSION'            => '',
        'PLUGIN_PREFIX'                => 'codesoup_aac',
        'PLUGIN_NAME'                  => 'ACF Admin Categories',
        'PLUGIN_VERSION'               => '0.0.1',
    ]);

    $plugin->init();
    
} catch (\Throwable $e) {
    // Log the error
    if (function_exists('error_log')) {
        error_log('ACF Admin Categories Plugin Error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
    
    // Only show admin notice if user is an admin
    if (is_admin() && current_user_can('manage_options')) {
        add_action('admin_notices', function() use ($e) {
            ?>
            <div class="notice notice-error">
                <p><?php echo esc_html('ACF Admin Categories Plugin Error: ' . $e->getMessage()); ?></p>
            </div>
            <?php
        });
    }
}