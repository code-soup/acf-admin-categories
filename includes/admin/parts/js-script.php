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

<script type="text/javascript">
	if ( ! ( 'acf' in window ) )
	{
		return;
	}

	acf.add_filter('select2_ajax_data', function( data, args, $input, field, instance ){

		// Get all checked category checkboxes
		const checkedCategories = [];
		document.querySelectorAll('input[name="codesoup_category_filter"]:checked').forEach(function(checkbox) {
			checkedCategories.push(checkbox.value);
		});

		// Add checked categories to data
		if ( checkedCategories.length > 0 ) {
			data.category_filter = checkedCategories;
		}

		// return
		return data;

	});
</script>