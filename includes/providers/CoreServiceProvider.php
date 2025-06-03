<?php

declare(strict_types=1);

namespace CodeSoup\ACFAdminCategories\Providers;

use CodeSoup\ACFAdminCategories\Abstracts\AbstractServiceProvider;
use CodeSoup\ACFAdminCategories\Core\Container;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Core Service Provider
 *
 * Registers core plugin services like Hooker, Assets, and I18n.
 *
 * @since 1.0.0
 */
class CoreServiceProvider extends AbstractServiceProvider {

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function register( Container $container ): void {
		// Register Hooker as singleton
		$this->singleton(
			$container,
			'hooker',
			function ( Container $container ) {
				return new \CodeSoup\ACFAdminCategories\Core\Hooker();
			}
		);

		// Register Assets as singleton
		$this->singleton(
			$container,
			'assets',
			function ( Container $container ) {
				return new \CodeSoup\ACFAdminCategories\Core\Assets();
			}
		);

		// Register I18n as singleton
		$this->singleton(
			$container,
			'i18n',
			function ( Container $container ) {
				return new \CodeSoup\ACFAdminCategories\Core\I18n();
			}
		);

		// Register aliases for easier access
		$this->alias( $container, 'hooks', 'hooker' );
		$this->alias( $container, \CodeSoup\ACFAdminCategories\Core\Hooker::class, 'hooker' );
		$this->alias( $container, \CodeSoup\ACFAdminCategories\Core\Assets::class, 'assets' );
		$this->alias( $container, \CodeSoup\ACFAdminCategories\Core\I18n::class, 'i18n' );
	}

	/**
	 * Boot services after all providers have been registered
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function boot( Container $container ): void {
		parent::boot( $container );

		// Note: hooker->run() will be called after all providers are booted
		// This ensures all services have registered their hooks first
	}
}
