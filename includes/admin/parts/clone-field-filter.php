<?php
/**
 * Template for clone field category filter.
 *
 * @package CodeSoup\ACFAdminCategories
 * @var array $categories The list of available categories.
 */

declare( strict_types=1 );

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="acf-checkbox-group">
	<b><?php esc_html_e( 'Filter fields by category', 'codesoup-acf-admin-categories' ); ?></b>
	<?php
	foreach ( $categories as $category ) {
		printf(
			'<label><input type="checkbox" name="codesoup_category_filter" value="%s" />%s</label><br>',
			esc_attr( $category->term_id ),
			esc_html( $category->name )
		);
	}
	?>
</div>