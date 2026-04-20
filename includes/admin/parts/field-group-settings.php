<?php
/**
 * Template for ACF field group category settings tab.
 *
 * @package CodeSoup\ACFAdminCategories
 * @var array $categories The list of available categories.
 * @var array $assigned_categories The categories assigned to this field group.
 * @var string $nonce The security nonce.
 */

declare( strict_types=1 );

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="acf-field">
	<div class="acf-label">
		<label for="acf-field-categories"><?php esc_html_e( 'Field Categories', 'codesoup-acf-admin-categories' ); ?></label>
		<i tabindex="0" class="acf-icon acf-icon-help acf-js-tooltip" title="<?php esc_attr_e( 'Assign this field group to one or more categories for better organization.', 'codesoup-acf-admin-categories' ); ?>">?</i>
	</div>
	<div class="acf-input">
		<?php
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			echo '<ul class="acf-checkbox-list">';
			foreach ( $categories as $category ) {
				$checked = checked( in_array( $category->term_id, $assigned_categories, true ), true, false );

				printf(
					'<li><label class="acf-checkbox-item"><input type="checkbox" name="acf_field_categories[]" value="%s" %s /><span>%s</span>',
					esc_attr( $category->term_id ),
					$checked,
					esc_html( $category->name )
				);

				if ( ! empty( $category->description ) ) {
					printf(
						'<small class="description">%s</small>',
						esc_html( $category->description )
					);
				}

				echo '</label></li>';
			}
			echo '</ul>';
		} else {
			printf(
				'<p class="acf-no-categories">%s</p>',
				sprintf(
					wp_kses(
						/* translators: %s: URL to create categories */
						__( 'No field categories found. <a href="%s">Create some categories</a> first.', 'codesoup-acf-admin-categories' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'edit-tags.php?taxonomy=codesoup_acf_admin_tax' ) )
				)
			);
		}

		printf(
			'<input type="hidden" name="acf_field_categories_nonce" value="%s" />',
			esc_attr( $nonce )
		);
		?>
	</div>
</div>
