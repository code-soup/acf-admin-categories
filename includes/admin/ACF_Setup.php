<?php declare( strict_types=1 );

namespace CodeSoup\ACFAdminCategories\Admin;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @file
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class ACF_Setup {

	use \CodeSoup\ACFAdminCategories\Traits\HelpersTrait;

	/**
	 * Main plugin instance.
	 *
	 * @var \CodeSoup\ACFAdminCategories\Core\Init|null
	 * @since 1.0.0
	 */
	protected static ?\CodeSoup\ACFAdminCategories\Core\Init $instance = null;

	/**
	 * Assets loader class.
	 *
	 * @var \CodeSoup\ACFAdminCategories\Core\Assets|null
	 * @since 1.0.0
	 */
	protected ?\CodeSoup\ACFAdminCategories\Core\Assets $assets = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Main plugin instance.
		$instance     = \CodeSoup\ACFAdminCategories\plugin_instance();
		$hooker       = $instance->get_hooker();
		$this->assets = $instance->get_assets();

		// Admin hooks.
		$hooker->add_action( 'init', $this, 'register_custom_taxonomy' );
		$hooker->add_action( 'admin_menu', $this, 'admin_menu' );
		$hooker->add_action( 'parent_file', $this, 'set_parent_file' );
		$hooker->add_action( 'acf/field_group/additional_group_settings_tabs', $this, 'additional_group_settings_tabs' );
		$hooker->add_action( 'acf/field_group/render_group_settings_tab/codesoup_acf_field_taxonomy', $this, 'render_acf_group_setting' );
		$hooker->add_action( 'acf/update_field_group', $this, 'save_field_group_categories' );
		$hooker->add_action( 'views_edit-acf-field-group', $this, 'edit_view' );
		$hooker->add_action( 'pre_get_posts', $this, 'filter_field_groups_by_category' );
		$hooker->add_filter( 'manage_acf-field-group_posts_columns', $this, 'add_category_column', 20 );
		$hooker->add_action( 'manage_acf-field-group_posts_custom_column', $this, 'display_category_column', 20, 2 );
		$hooker->add_filter( 'manage_edit-acf-field-group_sortable_columns', $this, 'make_category_column_sortable', 20 );
	}

	public function additional_group_settings_tabs( $tabs ) {
		$tabs['codesoup_acf_field_taxonomy'] = __( 'Field Category', 'codesoup-acf-admin-categories' );
		return $tabs;
	}

	/**
	 * Render ACF field group settings tab for category assignment.
	 *
	 * @since 1.0.0
	 * @param array $field_group The field group data.
	 * @return void
	 */
	public function render_acf_group_setting( $field_group ) {
		// Get all terms from our custom taxonomy
		$categories = get_terms( [
			'taxonomy'   => 'codesoup_acf_admin_tax',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC'
		] );

		// Get currently assigned categories for this field group
		$assigned_categories = get_post_meta( $field_group['ID'], '_acf_field_categories', true );
		if ( ! is_array( $assigned_categories ) ) {
			$assigned_categories = [];
		}

		// Create nonce for security
		$nonce = wp_create_nonce( 'acf_field_categories_' . $field_group['ID'] ); ?>
		
		<div class="acf-field">
			<div class="acf-label">
				<label for="acf-field-categories"><?php _e( 'Field Categories', 'codesoup-acf-admin-categories' ); ?></label>
				<i tabindex="0" class="acf-icon acf-icon-help acf-js-tooltip" title="<?php _e( 'Assign this field group to one or more categories for better organization.', 'codesoup-acf-admin-categories' ); ?>">?</i>
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
                                        <?php checked( in_array( $category->term_id, $assigned_categories ) ); ?>
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
							__( 'No field categories found. <a href="%s">Create some categories</a> first.', 'codesoup-acf-admin-categories' ),
							esc_url( admin_url( 'edit-tags.php?taxonomy=codesoup_acf_admin_tax' ) )
						);
						?>
					</p>
				<?php endif; ?>
				
				<!-- Hidden field for nonce -->
				<input type="hidden" name="acf_field_categories_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
			</div>
		</div>
		<?php
	}

	/**
	 * Add category filter dropdown to ACF field groups list.
	 *
	 * @since 1.0.0
	 * @param array $views The current view links.
	 * @return array Modified view links.
	 */
	public function edit_view( $views ) {
		// Get all field categories
		$categories = get_terms( [
			'taxonomy'   => 'codesoup_acf_admin_tax',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC'
		] );

		// Get current selected category from URL
		$selected_category = isset( $_GET['field_category'] ) ? intval( $_GET['field_category'] ) : 0;

		// Start building the select dropdown
		$select_html  = '<select name="field_category" id="field-category-filter" onchange="this.form.submit()">';
		$select_html .= '<option value="">' . __( 'All Categories', 'codesoup-acf-admin-categories' ) . '</option>';

		// Add options for each category
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$selected     = selected( $selected_category, $category->term_id, false );
				$count        = $this->get_field_groups_count_by_category( $category->term_id );
				$select_html .= sprintf(
					'<option value="%d" %s>%s (%d)</option>',
					esc_attr( $category->term_id ),
					$selected,
					esc_html( $category->name ),
					$count
				);
			}
		}

		$select_html .= '</select>';

		// Add the filter to views
		$views['category_filter'] = sprintf(
			'<div class="alignleft actions" style="margin: 0 8px 0 0;">
				<form method="get" style="display: inline-block;">
					<input type="hidden" name="post_type" value="acf-field-group" />
					%s
					<noscript><input type="submit" value="%s" class="button" /></noscript>
				</form>
			</div>',
			$select_html,
			__( 'Filter', 'codesoup-acf-admin-categories' )
		);

		return $views;
	}

	/**
	 * Get count of field groups assigned to a specific category.
	 *
	 * @since 1.0.0
	 * @param int $category_id The category term ID.
	 * @return int Number of field groups in this category.
	 */
	private function get_field_groups_count_by_category( int $category_id ): int {
		// Get all field groups
		$field_groups = get_posts( [
			'post_type'      => 'acf-field-group',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_acf_field_categories',
			'fields'         => 'ids'
		] );

		$count = 0;

		foreach ( $field_groups as $field_group_id ) {
			$assigned_categories = get_post_meta( $field_group_id, '_acf_field_categories', true );

			if ( is_array( $assigned_categories ) && in_array( $category_id, $assigned_categories ) ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Add admin menu for ACF field categories.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_menu(): void {
		$parent_slug = 'edit.php?post_type=acf-field-group';
		$capability  = 'manage_options';

		add_submenu_page(
			$parent_slug,
			__( 'ACF Field Categories', 'codesoup-acf-admin-categories' ),
			__( 'Field Categories', 'codesoup-acf-admin-categories' ),
			$capability,
			'edit-tags.php?taxonomy=codesoup_acf_admin_tax',
			'',
			9999,
		);
	}

	/**
	 * Set the parent file to make ACF menu active when viewing taxonomy.
	 *
	 * @since 1.0.0
	 * @param string $parent_file The parent file.
	 * @return string Modified parent file.
	 */
	public function set_parent_file( string $parent_file ): string {
		global $submenu_file;

		// Check if we're on our taxonomy page
		if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] === 'codesoup_acf_admin_tax' ) {
			$parent_file  = 'edit.php?post_type=acf-field-group';
			$submenu_file = 'edit-tags.php?taxonomy=codesoup_acf_admin_tax';
		}

		return $parent_file;
	}

	/**
	 * Register custom taxonomy for ACF admin categories.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_custom_taxonomy(): void {
		// error_log('register_custom_taxonomy');
		$labels = [
			'name'                       => _x( 'ACF Admin Categories', 'Taxonomy General Name', 'codesoup-acf-admin-categories' ),
			'singular_name'              => _x( 'ACF Admin Category', 'Taxonomy Singular Name', 'codesoup-acf-admin-categories' ),
			'menu_name'                  => __( 'ACF Categories', 'codesoup-acf-admin-categories' ),
			'all_items'                  => __( 'All Categories', 'codesoup-acf-admin-categories' ),
			'parent_item'                => __( 'Parent Category', 'codesoup-acf-admin-categories' ),
			'parent_item_colon'          => __( 'Parent Category:', 'codesoup-acf-admin-categories' ),
			'new_item_name'              => __( 'New Category Name', 'codesoup-acf-admin-categories' ),
			'add_new_item'               => __( 'Add New Category', 'codesoup-acf-admin-categories' ),
			'edit_item'                  => __( 'Edit Category', 'codesoup-acf-admin-categories' ),
			'update_item'                => __( 'Update Category', 'codesoup-acf-admin-categories' ),
			'view_item'                  => __( 'View Category', 'codesoup-acf-admin-categories' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'codesoup-acf-admin-categories' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'codesoup-acf-admin-categories' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'codesoup-acf-admin-categories' ),
			'popular_items'              => __( 'Popular Categories', 'codesoup-acf-admin-categories' ),
			'search_items'               => __( 'Search Categories', 'codesoup-acf-admin-categories' ),
			'not_found'                  => __( 'Not Found', 'codesoup-acf-admin-categories' ),
			'no_terms'                   => __( 'No categories', 'codesoup-acf-admin-categories' ),
			'items_list'                 => __( 'Categories list', 'codesoup-acf-admin-categories' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'codesoup-acf-admin-categories' ),
		];

		$args = [
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => false,
			'capabilities'      => [
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'manage_options',
			],
			'meta_box_cb'       => false,
		];

		register_taxonomy( 'codesoup_acf_admin_tax', [], $args );
	}

	/**
	 * Save field group categories when field group is updated.
	 *
	 * @since 1.0.0
	 * @param array $field_group The field group data.
	 * @return void
	 */
	public function save_field_group_categories( $field_group ): void {
		// Verify nonce for security
		if ( ! isset( $_POST['acf_field_categories_nonce'] )
				|| ! wp_verify_nonce( $_POST['acf_field_categories_nonce'], 'acf_field_categories_' . $field_group['ID'] ) ) {
			return;
		}

		// Get selected categories from POST data
		$selected_categories = isset( $_POST['acf_field_categories'] ) ? $_POST['acf_field_categories'] : [];

		// Sanitize the category IDs
		$selected_categories = array_map( 'intval', $selected_categories );
		$selected_categories = array_filter( $selected_categories ); // Remove any zero values

		// Validate that all selected categories exist
		$valid_categories = [];
		foreach ( $selected_categories as $category_id ) {
			$term = get_term( $category_id, 'codesoup_acf_admin_tax' );
			if ( $term && ! is_wp_error( $term ) ) {
				$valid_categories[] = $category_id;
			}
		}

		// Save the categories as post meta
		if ( ! empty( $valid_categories ) ) {
			update_post_meta( $field_group['ID'], '_acf_field_categories', $valid_categories );
		} else {
			// If no categories selected, delete the meta
			delete_post_meta( $field_group['ID'], '_acf_field_categories' );
		}

		// Optional: Log for debugging
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( sprintf(
				'ACF Field Group %d categories updated: %s',
				$field_group['ID'],
				implode( ', ', $valid_categories )
			) );
		}
	}

	/**
	 * Filter field groups by category.
	 *
	 * @since 1.0.0
	 * @param WP_Query $query The WP_Query object.
	 * @return void
	 */
	public function filter_field_groups_by_category( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || ! isset( $_GET['field_category'] ) ) {
			return;
		}

		// Only apply to ACF field group queries
		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'acf-field-group' ) {
			return;
		}

		$selected_category = intval( $_GET['field_category'] );

		if ( $selected_category > 0 ) {
			// Get all field group IDs that have this category assigned
			$field_group_ids = [];

			$all_field_groups = get_posts( [
				'post_type'      => 'acf-field-group',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => '_acf_field_categories',
				'fields'         => 'ids'
			] );

			foreach ( $all_field_groups as $field_group_id ) {
				$assigned_categories = get_post_meta( $field_group_id, '_acf_field_categories', true );

				if ( is_array( $assigned_categories ) && in_array( $selected_category, $assigned_categories ) ) {
					$field_group_ids[] = $field_group_id;
				}
			}

			// If we found field groups with this category, filter to only those
			if ( ! empty( $field_group_ids ) ) {
				$query->set( 'post__in', $field_group_ids );
			} else {
				// No field groups found with this category, show none
				$query->set( 'post__in', [0] );
			}
		}
	}

	/**
	 * Add category column to ACF field groups list.
	 *
	 * @since 1.0.0
	 * @param array $columns The current columns.
	 * @return array Modified columns.
	 */
	public function add_category_column( $columns ) {
		$columns['category'] = __( 'Category', 'codesoup-acf-admin-categories' );
		return $columns;
	}

	/**
	 * Display category column content in ACF field groups list.
	 *
	 * @since 1.0.0
	 * @param string $column The column name.
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function display_category_column( $column, $post_id ) {
		if ( $column === 'category' ) {
			// Get assigned categories from post meta
			$assigned_categories = get_post_meta( $post_id, '_acf_field_categories', true );

			if ( is_array( $assigned_categories ) && ! empty( $assigned_categories ) ) {
				$category_names = [];
				$category_links = [];

				foreach ( $assigned_categories as $category_id ) {
					$term = get_term( $category_id, 'codesoup_acf_admin_tax' );

					if ( $term && ! is_wp_error( $term ) ) {
						// Create a link to filter by this category
						$filter_url = add_query_arg( [
							'post_type'      => 'acf-field-group',
							'field_category' => $category_id
						], admin_url( 'edit.php' ) );

						$category_links[] = sprintf(
							'<a href="%s" title="%s">%s</a>',
							esc_url( $filter_url ),
							esc_attr( sprintf( __( 'Filter by %s', 'codesoup-acf-admin-categories' ), $term->name ) ),
							esc_html( $term->name )
						);
					}
				}

				if ( ! empty( $category_links ) ) {
					echo implode( ', ', $category_links );
				} else {
					echo '<span class="na">—</span>';
				}
			} else {
				echo '<span class="na">—</span>';
			}
		}
	}

	/**
	 * Make category column sortable.
	 *
	 * @since 1.0.0
	 * @param array $columns The current columns.
	 * @return array Modified columns.
	 */
	public function make_category_column_sortable( $columns ) {
		$columns['category'] = 'category';
		return $columns;
	}
}
