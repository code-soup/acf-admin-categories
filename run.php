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

	if ( ! class_exists( 'CodeSoup\ACFAdminCategories\Core\Init' ) ) {
		throw new \RuntimeException( 'Autoloader loaded but core classes not found.' );
	}
} else {
	// Fallback autoloader for non-standard installations.
	spl_autoload_register(
		function ( $class_name ) {
			$prefix   = 'CodeSoup\\ACFAdminCategories\\';
			$base_dir = __DIR__ . '/includes/';

			$len = strlen( $prefix );
			if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
				return;
			}

			$relative_class = substr( $class_name, $len );
			$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			// Convert class names to WordPress file naming convention.
			$file = str_replace(
				array( '/Traits/', '/Core/', '/Admin/' ),
				array( '/traits/', '/core/', '/admin/' ),
				$file
			);
			$file = preg_replace( '/([A-Z])/', '-$1', basename( $file ) );
			$file = strtolower( $file );
			$file = str_replace( '-.php', '.php', $file );
			$file = dirname( $file ) . '/class' . $file;

			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	);
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
			'MIN_PHP_VERSION'              => '8.1',
			'MIN_MYSQL_VERSION'            => '',
			'PLUGIN_PREFIX'                => 'codesoup_aac',
			'PLUGIN_NAME'                  => 'CodeSoup ACF Admin Categories',
			'PLUGIN_VERSION'               => '1.0.2',
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
	if ( is_admin() ) {
		// Check if hooks have already fired.
		$hook_fired = did_action( 'admin_notices' );

		if ( $hook_fired ) {
			// Display notice immediately if admin_notices already fired.
			if ( current_user_can( 'manage_options' ) ) {
				?>
				<div class="notice notice-error">
					<p><?php echo esc_html( 'CodeSoup ACF Admin Categories Plugin Error: ' . $e->getMessage() ); ?></p>
				</div>
				<?php
			}
		} else {
			// Register hook for later display.
			add_action(
				'admin_notices',
				function () use ( $e ) {
					if ( ! current_user_can( 'manage_options' ) ) {
						return;
					}
					?>
				<div class="notice notice-error">
					<p><?php echo esc_html( 'CodeSoup ACF Admin Categories Plugin Error: ' . $e->getMessage() ); ?></p>
				</div>
					<?php
				}
			);
		}
	}
}