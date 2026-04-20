<?php
/**
 * Autoloader for plugin classes.
 *
 * @package CodeSoup\ACFAdminCategories
 */

namespace CodeSoup\ACFAdminCategories;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * PSR-4 autoloader for plugin classes.
 *
 * @since 1.0.2
 */
class Autoloader {

	/**
	 * Namespace prefix
	 *
	 * @var string
	 */
	private string $prefix = 'CodeSoup\\ACFAdminCategories\\';

	/**
	 * Base directory for classes
	 *
	 * @var string
	 */
	private string $base_dir;

	/**
	 * Constructor
	 *
	 * @param string $base_dir Base directory path.
	 */
	public function __construct( string $base_dir ) {
		$this->base_dir = rtrim( $base_dir, '/' ) . '/';
	}

	/**
	 * Register autoloader
	 *
	 * @return void
	 */
	public function register(): void {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	/**
	 * Load class file
	 *
	 * @param string $class Fully qualified class name.
	 * @return void
	 */
	public function load_class( string $class ): void {
		$prefix_len = strlen( $this->prefix );
		if ( strncmp( $this->prefix, $class, $prefix_len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $prefix_len );
		$file           = $this->get_file_path( $relative_class );

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Convert class name to file path
	 *
	 * @param string $relative_class Relative class name.
	 * @return string File path.
	 */
	private function get_file_path( string $relative_class ): string {
		$parts          = explode( '\\', $relative_class );
		$class_name     = array_pop( $parts );
		$namespace_path = strtolower( implode( '/', $parts ) );

		$file_name = $this->get_file_name( $class_name );

		return $this->base_dir . $namespace_path . '/' . $file_name;
	}

	/**
	 * Convert class name to file name
	 *
	 * @param string $class_name Class name.
	 * @return string File name.
	 */
	private function get_file_name( string $class_name ): string {
		if ( str_ends_with( $class_name, 'Trait' ) ) {
			$base = substr( $class_name, 0, -5 );
			return 'trait-' . $this->dashify( $base ) . '.php';
		}

		return 'class-' . $this->dashify( $class_name ) . '.php';
	}

	/**
	 * Convert PascalCase to dash-case
	 *
	 * @param string $string Input string.
	 * @return string Dashified string.
	 */
	private function dashify( string $string ): string {
		return strtolower( preg_replace( '/([A-Z])/', '-$1', lcfirst( $string ) ) );
	}
}
