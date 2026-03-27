<?php
/**
 * Template for category column in field groups list.
 *
 * @package CodeSoup\ACFAdminCategories
 * @var array $category_links The list of category links.
 */

declare( strict_types=1 );

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! empty( $category_links ) ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in sprintf when building links.
	echo implode( ', ', $category_links );
} else {
	echo '<span class="na">—</span>';
}
