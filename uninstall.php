<?php
/**
 * Plugin uninstall handler.
 *
 * Cleans up all plugin data when the plugin is deleted.
 *
 * @package CodeSoup\ACFAdminCategories
 * @since 1.0.2
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete all plugin data
 *
 * @return void
 */
function codesoup_acf_admin_categories_uninstall() {
	global $wpdb;

	// Taxonomy name.
	$taxonomy_name = 'codesoup_acf_admin_tax';

	// Meta key used for category assignments.
	$meta_key = '_acf_field_categories';

	// 1. Delete all taxonomy terms.
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy_name,
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		foreach ( $terms as $term_id ) {
			wp_delete_term( $term_id, $taxonomy_name );
		}
	}

	// 2. Delete all post meta for category assignments.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Uninstall cleanup, direct query appropriate.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
			$meta_key
		)
	);

	// 3. Delete all transients.
	delete_transient( 'codesoup_aac_field_group_ids' );

	// Delete version-based asset manifest transients (all versions).
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Uninstall cleanup, direct query appropriate.
	$wpdb->query(
		"DELETE FROM {$wpdb->options}
		WHERE option_name LIKE '_transient_codesoup_aac_assets_manifest_%'
		OR option_name LIKE '_transient_timeout_codesoup_aac_assets_manifest_%'"
	);

	// 4. Unregister the taxonomy (in case it's still registered).
	unregister_taxonomy( $taxonomy_name );

	// 5. Delete user meta for dismissed notices.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Uninstall cleanup, direct query appropriate.
	$wpdb->query(
		"DELETE FROM {$wpdb->usermeta}
		WHERE meta_key = 'codesoup_aac_acf_notice_dismissed'"
	);
}

// Execute cleanup.
codesoup_acf_admin_categories_uninstall();
