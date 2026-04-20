<?php
/**
 * Plugin bootstrap file.
 *
 * @package CodeSoup\ACFAdminCategories
 */

namespace CodeSoup\ACFAdminCategories;

defined( 'ABSPATH' ) || die;

// Load autoloader.
$autoload_file = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload_file ) ) {
	require_once $autoload_file;
}

// Verify core class loaded.
if ( ! class_exists( 'CodeSoup\ACFAdminCategories\Core\Init' ) ) {
	wp_die(
		esc_html__( 'CodeSoup ACF Admin Categories: Core classes not found. Run composer install.', 'codesoup-acf-admin-categories' ),
		esc_html__( 'Plugin Error', 'codesoup-acf-admin-categories' ),
		array( 'back_link' => true )
	);
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
 */
add_action(
	'plugins_loaded',
	function () {
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
					'PLUGIN_VERSION'               => '1.0.2',
				)
			);
			$instance->init();
		} catch ( \Throwable $e ) {
			add_action(
				'admin_notices',
				function () use ( $e ) {
					if ( ! current_user_can( 'manage_options' ) ) {
						return;
					}
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html(
							sprintf(
								'CodeSoup ACF Admin Categories Error: %s',
								$e->getMessage()
							)
						)
					);
				}
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && function_exists( 'error_log' ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'CodeSoup ACF Admin Categories: ' . $e->getMessage() );
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( $e->getTraceAsString() );
			}
		}
	}
);