<?php declare( strict_types=1 );

namespace CodeSoup\ACFAdminCategories\Core;

use CodeSoup\ACFAdminCategories\Interfaces\AssetsInterface;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @file
 * Get paths for assets
 *
 * @since 1.0.0
 */
class Assets implements AssetsInterface {

	use \CodeSoup\ACFAdminCategories\Traits\HelpersTrait;

	/**
	 * Manifest file object containing list of all hashed assets
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private array $manifest = array();

	/**
	 * URI to theme 'dist' folder
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $dist_uri;

	/**
	 * Initiate
	 *
	 * @since 1.0.0
	 * @throws \RuntimeException If manifest cannot be loaded.
	 */
	public function __construct() {
		$this->dist_uri = $this->get_plugin_dir_url( 'dist' );
		$this->load_manifest();
	}

	/**
	 * Load the assets manifest with caching
	 *
	 * @since 1.0.0
	 * @return void
	 * @throws \RuntimeException If manifest cannot be loaded.
	 */
	private function load_manifest(): void {
		// Try to get cached manifest first
		$cache_key       = $this->get_constant( 'PLUGIN_NAME' ) . $this->get_constant( 'PLUGIN_VERSION' );
		$cached_manifest = get_transient( $cache_key );
		$manifest_path   = $this->get_plugin_dir_path( 'dist/manifest.json' );

		if ( false !== $cached_manifest && is_array( $cached_manifest ) ) {
			$this->manifest = $cached_manifest;
			return;
		}

		// Check if manifest file exists
		if ( ! file_exists( $manifest_path ) ) {
			$this->manifest = array();
			return;
		}

		// Load manifest content
		$manifest_content = file_get_contents( $manifest_path );

		if ( false === $manifest_content ) {
			throw new \RuntimeException( 'Failed to read assets manifest file.' );
		}

		$decoded_manifest = json_decode( $manifest_content, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			throw new \RuntimeException( 'Invalid JSON in assets manifest: ' . json_last_error_msg() );
		}

		$this->manifest = is_array( $decoded_manifest ) ? $decoded_manifest : array();

		// Cache the manifest for 1 hour
		set_transient( $cache_key, $this->manifest, HOUR_IN_SECONDS );
	}

	/**
	 * Get full URI to single asset
	 *
	 * @since 1.0.0
	 * @param  string $filename File name
	 * @return string           URI to resource
	 */
	public function get( string $filename = '' ): string {
		return $this->locate( $filename );
	}

	/**
	 * Fix URL for requested files
	 *
	 * @since 1.0.0
	 * @param  string $filename Requested asset
	 * @return string           URL to the asset
	 */
	private function locate( string $filename = '' ): string {

		error_log( print_r( $this->manifest, true ) );
		// Return URL to requested file from manifest.
		if ( array_key_exists( $filename, $this->manifest ) ) {
			return $this->join_path( $this->dist_uri, $this->manifest[ $filename ] );
		}

		switch ( pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			case 'js':
				$filename = $this->join_path( 'scripts', $filename );
				break;

			case 'css':
				$filename = $this->join_path( 'styles', $filename );
				break;
		}

		// Return default file location.
		return $this->join_path( $this->dist_uri, $filename );
	}
}
