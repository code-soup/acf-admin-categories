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

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

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
	public static array $constants = array();

	/**
	 * Assets service
	 *
	 * @var Assets|null
	 * @since 1.0.0
	 */
	protected ?Assets $assets = null;

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
			// Initialize services.
			$this->assets = new Assets();

			// Load textdomain.
			add_action( 'init', array( $this, 'load_textdomain' ) );

			// Initialize admin if in admin context.
			if ( is_admin() ) {
				new \CodeSoup\ACFAdminCategories\Admin\Init( $this->assets );
			}
		} catch ( \Exception $e ) {
			// Log the error and re-throw.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Plugin initialization failed: ' . $e->getMessage() );
			}
			throw new \RuntimeException(
				'Plugin initialization failed: ' . esc_html( $e->getMessage() ),
				0,
				$e // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception chaining parameter.
			);
		}
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
}
