<?php
/**
 * The core plugin class.
 *
 * @package CodeSoup\ACFAdminCategories
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */

declare( strict_types=1 );

namespace CodeSoup\ACFAdminCategories\Core;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Core plugin initialization class.
 *
 * @since 1.0.0
 */
final class Init {

	use \CodeSoup\ACFAdminCategories\Traits\HelpersTrait;

	/**
	 * Main plugin instance
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Define plugin constants
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private static array $constants = array();

	/**
	 * Assets service
	 *
	 * @var Assets|null
	 * @since 1.0.0
	 */
	protected ?Assets $assets = null;

	/**
	 * Admin initialization instance
	 *
	 * @var \CodeSoup\ACFAdminCategories\Admin\Init|null
	 * @since 1.0.2
	 */
	protected ?\CodeSoup\ACFAdminCategories\Admin\Init $admin = null;

	/**
	 * Make constructor protected, to prevent direct instantiation
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {}

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return self Main instance.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run everything on init
	 *
	 * @since 1.0.0
	 * @return void
	 * @throws \RuntimeException If initialization fails.
	 */
	public function init(): void {
		try {
			// Check ACF dependency first.
			if ( ! $this->is_acf_active() ) {
				add_action( 'admin_notices', array( $this, 'acf_missing_notice' ) );
				return;
			}

			// Initialize services.
			$this->assets = new Assets();

			// Load textdomain.
			add_action( 'init', array( $this, 'load_textdomain' ) );

			// Initialize admin if in admin context.
			if ( is_admin() ) {
				$this->admin = new \CodeSoup\ACFAdminCategories\Admin\Init( $this->assets );
			}
		} catch ( \Exception $e ) {
			// Log the error and re-throw.
			self::log_debug( 'Plugin initialization failed: ' . $e->getMessage() );
			throw new \RuntimeException(
				'Plugin initialization failed: ' . $e->getMessage(), // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message, not HTML output.
				0,
				$e // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception chaining parameter.
			);
		}
	}

	/**
	 * Check if ACF is active
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	private function is_acf_active(): bool {
		return class_exists( 'ACF' ) || function_exists( 'acf' );
	}

	/**
	 * Display admin notice when ACF is not active
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function acf_missing_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<strong><?php esc_html_e( 'CodeSoup ACF Admin Categories', 'codesoup-acf-admin-categories' ); ?></strong>
				<?php esc_html_e( 'requires Advanced Custom Fields (ACF) to be installed and active.', 'codesoup-acf-admin-categories' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain(): void {
		$plugin_name = $this->get_plugin_name();
		$text_domain = strtolower( str_replace( '_', '-', $plugin_name ) );

		$languages_path = $this->get_plugin_dir_path( '/languages' );
		$relative_path  = plugin_basename( $languages_path );

		load_plugin_textdomain(
			$text_domain,
			false,
			$relative_path
		);
	}

	/**
	 * Get assets service
	 *
	 * @since 1.0.0
	 * @return Assets The assets service
	 */
	public function get_assets(): Assets {
		if ( null === $this->assets ) {
			$this->assets = new Assets();
		}

		return $this->assets;
	}

	/**
	 * Set plugin constants
	 *
	 * @since 1.0.0
	 * @param array<string, string> $constants Array of constants to set.
	 * @return void
	 */
	public function set_constants( array $constants ): void {
		self::$constants = $constants;
	}

	/**
	 * Get plugin constants
	 *
	 * @since 1.0.2
	 * @return array<string, string> Array of plugin constants.
	 */
	public static function get_constants(): array {
		return self::$constants;
	}

	/**
	 * Get single plugin constant by key
	 *
	 * @since 1.0.2
	 * @param string $key Constant key.
	 * @return string|null Constant value or null if not found.
	 */
	public static function get_constant( string $key ): ?string {
		return self::$constants[ $key ] ?? null;
	}

	/**
	 * Log message to error log if WP_DEBUG enabled
	 *
	 * @since 1.0.2
	 * @param string $message Message to log.
	 * @return void
	 */
	public static function log_debug( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && function_exists( 'error_log' ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $message );
		}
	}

	/**
	 * Add admin notice
	 *
	 * @since 1.0.2
	 * @param string $message Notice message.
	 * @param string $type Notice type (error|warning|success|info).
	 * @return void
	 */
	public static function add_admin_notice( string $message, string $type = 'error' ): void {
		add_action(
			'admin_notices',
			function () use ( $message, $type ) {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}
				printf(
					'<div class="notice notice-%s"><p>%s</p></div>',
					esc_attr( $type ),
					esc_html( $message )
				);
			}
		);
	}
}
