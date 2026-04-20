<?php
/**
 * Plugin bootstrap file.
 *
 * @package CodeSoup\ACFAdminCategories
 */

namespace CodeSoup\ACFAdminCategories;

defined( 'ABSPATH' ) || die;

// Load composer autoloader if exists (development or composer-installed).
$autoload_file = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload_file ) ) {
	require_once $autoload_file;
} else {
	// Fallback: Manual PSR-4 autoloader for production plugin installation.
	require_once __DIR__ . '/includes/class-autoloader.php';
	$autoloader = new \CodeSoup\ACFAdminCategories\Autoloader( __DIR__ . '/includes' );
	$autoloader->register();
}

/**
 * Get plugin instance.
 *
 * @since 1.0.0
 * @return Core\Init Plugin instance.
 */
function plugin(): Core\Init {
	return Core\Init::get_instance();
}

/**
 * Initialize plugin.
 * Hooks into after_setup_theme to ensure theme autoloader loaded first.
 */
add_action(
	'after_setup_theme',
	function () {
		// Verify classes available (either from plugin vendor or theme vendor).
		if ( ! class_exists( 'CodeSoup\ACFAdminCategories\Core\Init' ) ) {
			add_action(
				'admin_notices',
				function () {
					if ( ! current_user_can( 'manage_options' ) ) {
						return;
					}
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__( 'CodeSoup ACF Admin Categories: Core classes not found. Install plugin via composer or run composer install in plugin directory.', 'codesoup-acf-admin-categories' )
					);
				}
			);
			return;
		}

		try {
			$instance = plugin();
			$instance->set_constants(
				array(
					'MIN_WP_VERSION_SUPPORT_TERMS' => '6.0',
					'MIN_WP_VERSION'               => '6.0',
					'MIN_PHP_VERSION'              => '8.1',
					'MIN_MYSQL_VERSION'            => '',
					'PLUGIN_PREFIX'                => 'codesoup_aac',
					'PLUGIN_NAME'                  => 'CodeSoup ACF Admin Categories',
					'PLUGIN_VERSION'               => '1.0.3',
				)
			);
			$instance->init();
		} catch ( \Throwable $e ) {
			Core\Init::add_admin_notice(
				sprintf(
					'CodeSoup ACF Admin Categories Error: %s',
					$e->getMessage()
				)
			);
			Core\Init::log_debug( 'CodeSoup ACF Admin Categories: ' . $e->getMessage() );
			Core\Init::log_debug( $e->getTraceAsString() );
		}
	},
	20
);