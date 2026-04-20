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

	// Meta keys used for category assignments.
	$meta_key         = '_acf_field_categories';
	$primary_meta_key = '_acf_primary_category';

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
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key IN (%s, %s)",
			$meta_key,
			$primary_meta_key
		)
	);

	// 3. Delete all transients and options.
	delete_transient( 'codesoup_aac_field_group_ids' );
	delete_option( 'codesoup_aac_primary_category_migrated' );

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
