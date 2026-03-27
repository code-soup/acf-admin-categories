<?php
/**
 * Plugin bootstrap file.
 *
 * @package CodeSoup\ACFAdminCategories
 */

namespace CodeSoup\ACFAdminCategories;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

// Load composer autoloader.
$autoload_file = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload_file ) ) {
	require $autoload_file;
}

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
	// Init plugin.
	$plugin_instance = plugin_instance();
	$plugin_instance->set_constants(
		array(
			'MIN_WP_VERSION_SUPPORT_TERMS' => '6.0',
			'MIN_WP_VERSION'               => '6.0',
			'MIN_PHP_VERSION'              => '8.0',
			'MIN_MYSQL_VERSION'            => '',
			'PLUGIN_PREFIX'                => 'codesoup_aac',
			'PLUGIN_NAME'                  => 'CodeSoup ACF Admin Categories',
			'PLUGIN_VERSION'               => '1.0.0',
		)
	);

	$plugin_instance->init();

} catch ( \Throwable $e ) {
	// Log the error.
	if ( function_exists( 'error_log' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'CodeSoup ACF Admin Categories Plugin Error: ' . $e->getMessage() );
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'Stack trace: ' . $e->getTraceAsString() );
	}

	// Only show admin notice if user is an admin.
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		add_action(
			'admin_notices',
			function () use ( $e ) {
				?>
			<div class="notice notice-error">
				<p><?php echo esc_html( 'CodeSoup ACF Admin Categories Plugin Error: ' . $e->getMessage() ); ?></p>
			</div>
				<?php
			}
		);
	}
}