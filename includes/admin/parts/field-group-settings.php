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
		<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
			<ul class="acf-checkbox-list">
				<?php foreach ( $categories as $category ) : ?>
					<li>
						<label class="acf-checkbox-item">
							<input
								type="checkbox"
								name="acf_field_categories[]"
								value="<?php echo esc_attr( $category->term_id ); ?>"
								<?php checked( in_array( $category->term_id, $assigned_categories, true ) ); ?>
							/>
							<span><?php echo esc_html( $category->name ); ?></span>
							<?php if ( ! empty( $category->description ) ) : ?>
								<small class="description"><?php echo esc_html( $category->description ); ?></small>
							<?php endif; ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="acf-no-categories">
				<?php
				printf(
					/* translators: %s: URL to create categories */
					esc_html__( 'No field categories found. <a href="%s">Create some categories</a> first.', 'codesoup-acf-admin-categories' ),
					esc_url( admin_url( 'edit-tags.php?taxonomy=codesoup_acf_admin_tax' ) )
				);
				?>
			</p>
		<?php endif; ?>
		
		<!-- Hidden field for nonce -->
		<input type="hidden" name="acf_field_categories_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
	</div>
</div>