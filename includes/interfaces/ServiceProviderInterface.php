<?php

declare(strict_types=1);

namespace CodeSoup\ACFAdminCategories\Interfaces;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Service Provider Interface
 *
 * Contract for service providers that register services with the DI container.
 *
 * @since 1.0.0
 */
interface ServiceProviderInterface {

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @param \CodeSoup\ACFAdminCategories\Core\Container $container The DI container
	 * @return void
	 */
	public function register( \CodeSoup\ACFAdminCategories\Core\Container $container ): void;

	/**
	 * Boot services after all providers have been registered
	 *
	 * @since 1.0.0
	 * @param \CodeSoup\ACFAdminCategories\Core\Container $container The DI container
	 * @return void
	 */
	public function boot( \CodeSoup\ACFAdminCategories\Core\Container $container ): void;

	/**
	 * Get the services provided by this provider
	 *
	 * @since 1.0.0
	 * @return array<string> Array of service identifiers
	 */
	public function provides(): array;
} 