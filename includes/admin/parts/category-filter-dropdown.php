<?php
/**
 * Template for category filter dropdown in field groups list.
 *
 * @package CodeSoup\ACFAdminCategories
 * @var array $categories The list of available categories.
 * @var int $selected_category The currently selected category ID.
 * @var array $category_counts The count of field groups per category.
 */

declare( strict_types=1 );

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="alignleft actions" style="margin: 0 8px 0 0;">
	<form method="get" style="display: inline-block;">
		<input type="hidden" name="post_type" value="acf-field-group" />
		<select name="field_category" id="field-category-filter" onchange="this.form.submit()">
			<option value=""><?php esc_html_e( 'All Categories', 'codesoup-acf-admin-categories' ); ?></option>
			<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $selected_category, $category->term_id ); ?>>
						<?php echo esc_html( $category->name ); ?> (<?php echo absint( isset( $category_counts[ $category->term_id ] ) ? $category_counts[ $category->term_id ] : 0 ); ?>)
					</option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<noscript><input type="submit" value="<?php esc_attr_e( 'Filter', 'codesoup-acf-admin-categories' ); ?>" class="button" /></noscript>
	</form>
</div>

