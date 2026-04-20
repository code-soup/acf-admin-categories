<?php
/**
 * Helper methods trait.
 *
 * @package CodeSoup\ACFAdminCategories
 */

declare(strict_types=1);

namespace CodeSoup\ACFAdminCategories\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Helper methods
 *
 * @since 1.0.0
 */
trait HelpersTrait {

	/**
	 * Return absolute path to plugin dir
	 * Always returns path without trailing slash
	 *
	 * @since 1.0.0
	 * @param string $path Optional path to append.
	 * @return string Absolute path to plugin directory.
	 */
	private function get_plugin_dir_path( string $path = '' ): string {
		// Force baseurl to be plugin root directory.
		$base = dirname( __DIR__, 2 );

		return $this->join_path( $base, $path );
	}

	/**
	 * Return plugin directory URL
	 * Always returns URL without trailing slash
	 *
	 * @since 1.0.0
	 * @param string $path Optional path to append.
	 * @return string Plugin directory URL.
	 */
	private function get_plugin_dir_url( string $path = '' ): string {
		// Force baseurl to be plugin root directory.
		$base = plugins_url( '/', dirname( __DIR__, 1 ) );

		/**
		 * Filter the plugin directory URL.
		 *
		 * Useful when the plugin is installed via Composer in a non-standard location.
		 *
		 * @since 1.0.0
		 * @param string $base The default plugin directory URL.
		 */
		$base = apply_filters( 'codesoup_acf_admin_categories_plugin_dir_url', $base );

		return $this->join_path( $base, $path, '/' );
	}

	/**
	 * Returns PLUGIN_NAME constant
	 *
	 * @since 1.0.0
	 * @return string Plugin name.
	 */
	private function get_plugin_name(): string {
		$name = $this->get_constant( 'PLUGIN_NAME' );
		return false !== $name ? $name : 'CodeSoup ACF Admin Categories';
	}

	/**
	 * Returns PLUGIN_VERSION constant
	 *
	 * @since 1.0.0
	 * @return string Plugin version.
	 */
	private function get_plugin_version(): string {
		$version = $this->get_constant( 'PLUGIN_VERSION' );
		return false !== $version ? $version : '1.0.2';
	}

	/**
	 * Returns PLUGIN_PREFIX constant as ID
	 * Converts to-slug-like-id
	 * and appends additional text at the end for custom unique id
	 *
	 * @since 1.0.0
	 * @param string $append Optional string to append to the ID.
	 * @return string Plugin ID with optional appended string.
	 */
	private function get_plugin_id( string $append = '' ): string {
		$name   = $this->get_constant( 'PLUGIN_NAME' );
		$name   = false !== $name ? $name : 'codesoup-acf-admin-categories';
		$dashed = str_replace( '_', '-', $name );

		return sanitize_title( $dashed ) . $append;
	}

	/**
	 * Get plugin constant by name
	 *
	 * @since 1.0.0
	 * @param string $key Constant name.
	 * @return string|false Constant value or false if not found.
	 */
	private function get_constant( string $key ) {
		$name  = trim( strtoupper( $key ) );
		$value = \CodeSoup\ACFAdminCategories\Core\Init::get_constant( $name );

		// Check if constant is defined.
		if ( null === $value ) {
			// Log to error for debugging.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( "CodeSoup ACF Admin Categories: Invalid constant requested: {$name}" );
			}

			// Exit.
			return false;
		}

		// Return value by key.
		return $value;
	}

	/**
	 * Join two paths into single absolute path or URL
	 *
	 * @since 1.0.0
	 * @param string $base Base location.
	 * @param string $path Path to append.
	 * @param string $separator Directory separator to use.
	 * @return string Combined path.
	 */
	private function join_path( string $base = '', string $path = '', string $separator = DIRECTORY_SEPARATOR ): string {
		// Strip slashes on both ends.
		if ( $path ) {
			$path = rtrim( $path, '/' );
			$path = ltrim( $path, '/' );
		}

		// Strip trailingslash just in case.
		$base = untrailingslashit( $base );
		$url  = array_filter( array( $base, $path ) );
		$url  = implode( $separator, $url );

		return untrailingslashit( $url );
	}
}
