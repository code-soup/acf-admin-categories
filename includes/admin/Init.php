<?php
/**
 * Admin functionality class.
 *
 * @package CodeSoup\ACFAdminCategories
 */

declare(strict_types=1);

namespace CodeSoup\ACFAdminCategories\Admin;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Init {

	use \CodeSoup\ACFAdminCategories\Traits\HelpersTrait;

	/**
	 * Taxonomy name for ACF field categories
	 *
	 * @since 1.0.0
	 */
	private const TAXONOMY_NAME = 'codesoup_acf_admin_tax';

	/**
	 * Meta key for storing field group categories
	 *
	 * @since 1.0.0
	 */
	private const FIELD_CATEGORIES_META_KEY = '_acf_field_categories';

	/**
	 * ACF field group post type
	 *
	 * @since 1.0.0
	 */
	private const ACF_POST_TYPE = 'acf-field-group';

	/**
	 * Assets loader class.
	 *
	 * @var \CodeSoup\ACFAdminCategories\Core\Assets
	 * @since 1.0.0
	 */
	protected \CodeSoup\ACFAdminCategories\Core\Assets $assets;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param \CodeSoup\ACFAdminCategories\Core\Assets $assets Assets service.
	 */
	public function __construct( \CodeSoup\ACFAdminCategories\Core\Assets $assets ) {
		$this->assets = $assets;

		// Admin hooks.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init', array( $this, 'register_custom_taxonomy' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'parent_file', array( $this, 'set_parent_file' ) );
		add_action( 'acf/field_group/additional_group_settings_tabs', array( $this, 'additional_group_settings_tabs' ) );
		add_action( 'acf/field_group/render_group_settings_tab/codesoup_acf_field_taxonomy', array( $this, 'render_acf_group_setting' ) );
		add_action( 'acf/update_field_group', array( $this, 'save_field_group_categories' ) );
		add_action( 'views_edit-acf-field-group', array( $this, 'edit_view' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_field_groups_by_category' ) );
		add_filter( 'manage_acf-field-group_posts_columns', array( $this, 'add_category_column' ), 20 );
		add_action( 'manage_acf-field-group_posts_custom_column', array( $this, 'display_category_column' ), 20, 2 );
		add_filter( 'manage_edit-acf-field-group_sortable_columns', array( $this, 'make_category_column_sortable' ), 20 );
	}

	/**
	 * Get all categories from the taxonomy.
	 *
	 * @since 1.0.0
	 * @return array|\WP_Error Array of term objects or WP_Error on failure.
	 */
	private function get_all_categories() {
		return get_terms(
			array(
				'taxonomy'   => self::TAXONOMY_NAME,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
	}

	/**
	 * Get all ACF field group IDs that have category assignments.
	 *
	 * @since 1.0.0
	 * @return array Array of field group post IDs.
	 */
	private function get_all_field_group_ids(): array {
		return get_posts(
			array(
				'post_type'      => self::ACF_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => self::FIELD_CATEGORIES_META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'fields'         => 'ids',
			)
		);
	}

	/**
	 * Get assigned categories for a field group.
	 *
	 * @since 1.0.0
	 * @param int $field_group_id The field group post ID.
	 * @return array Array of category term IDs.
	 */
	private function get_assigned_categories( int $field_group_id ): array {
		$assigned = get_post_meta( $field_group_id, self::FIELD_CATEGORIES_META_KEY, true );

		if ( ! is_array( $assigned ) ) {
			return array();
		}

		return $assigned;
	}

	/**
	 * Get field group IDs that belong to a specific category.
	 *
	 * @since 1.0.0
	 * @param int $category_id The category term ID.
	 * @return array Array of field group post IDs.
	 */
	private function get_field_group_ids_by_category( int $category_id ): array {
		$all_field_groups = $this->get_all_field_group_ids();
		$field_group_ids  = array();

		foreach ( $all_field_groups as $field_group_id ) {
			$assigned_categories = $this->get_assigned_categories( $field_group_id );

			if ( in_array( $category_id, $assigned_categories, true ) ) {
				$field_group_ids[] = $field_group_id;
			}
		}

		return $field_group_ids;
	}

	/**
	 * Register the CSS/JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
		wp_enqueue_style(
			$this->get_plugin_id( '/wp/css' ),
			$this->assets->get( 'admin.css' ),
			array(),
			$this->get_plugin_version(),
			'all'
		);
	}

	/**
	 * Add ACF field group settings tab.
	 *
	 * @since 1.0.0
	 * @param array $tabs The current tabs.
	 * @return array Modified tabs.
	 */
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
		// Get all terms from our custom taxonomy.
		$categories = $this->get_all_categories();

		// Get currently assigned categories for this field group.
		$assigned_categories = $this->get_assigned_categories( $field_group['ID'] );

		// Create nonce for security.
		$nonce = wp_create_nonce( 'acf_field_categories_' . $field_group['ID'] ); ?>

		<div class="acf-field">
			<div class="acf-label">
				<label for="acf-field-categories"><?php esc_html_e( 'Field Categories', 'codesoup-acf-admin-categories' ); ?></label>
				<p class="description"><?php esc_html_e( 'Assign this field group to one or more categories for better organization.', 'codesoup-acf-admin-categories' ); ?></p>
			</div>
			<div class="acf-input">
				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<div class="acf-checkbox-list">
						<?php foreach ( $categories as $category ) : ?>
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
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="acf-no-categories">
						<?php
						printf(
							/* translators: %s: URL to create categories */
							wp_kses(
								__( 'No field categories found. <a href="%s">Create some categories</a> first.', 'codesoup-acf-admin-categories' ),
								array( 'a' => array( 'href' => array() ) )
							),
							esc_url( admin_url( 'edit-tags.php?taxonomy=' . self::TAXONOMY_NAME ) )
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
		// Get all field categories.
		$categories = $this->get_all_categories();

		// Get current selected category from URL (sanitized, read-only operation).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for filtering.
		$selected_category = isset( $_GET['field_category'] ) ? absint( $_GET['field_category'] ) : 0;

		// Start building the select dropdown.
		$select_html  = '<select name="field_category" id="field-category-filter" onchange="this.form.submit()">';
		$select_html .= '<option value="">' . esc_html__( 'All Categories', 'codesoup-acf-admin-categories' ) . '</option>';

		// Add options for each category.
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

		// Add the filter to views.
		$views['category_filter'] = sprintf(
			'<div class="alignleft actions" style="margin: 0 8px 0 0;">
				<form method="get" style="display: inline-block;">
					<input type="hidden" name="post_type" value="%s" />
					%s
					<noscript><input type="submit" value="%s" class="button" /></noscript>
				</form>
			</div>',
			esc_attr( self::ACF_POST_TYPE ),
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
		return count( $this->get_field_group_ids_by_category( $category_id ) );
	}

	/**
	 * Add admin menu for ACF field categories.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_menu(): void {
		$parent_slug = 'edit.php?post_type=' . self::ACF_POST_TYPE;
		$capability  = 'manage_options';

		add_submenu_page(
			$parent_slug,
			__( 'CodeSoup ACF Field Categories', 'codesoup-acf-admin-categories' ),
			__( 'Field Categories', 'codesoup-acf-admin-categories' ),
			$capability,
			'edit-tags.php?taxonomy=' . self::TAXONOMY_NAME,
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

		// Check if we're on our taxonomy page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter check.
		if ( isset( $_GET['taxonomy'] ) && self::TAXONOMY_NAME === $_GET['taxonomy'] ) {
			$parent_file = 'edit.php?post_type=' . self::ACF_POST_TYPE;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Intentional override for menu highlighting.
			$submenu_file = 'edit-tags.php?taxonomy=' . self::TAXONOMY_NAME;
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
		$labels = array(
			'name'                       => _x( 'CodeSoup ACF Admin Categories', 'Taxonomy General Name', 'codesoup-acf-admin-categories' ),
			'singular_name'              => _x( 'CodeSoup ACF Admin Category', 'Taxonomy Singular Name', 'codesoup-acf-admin-categories' ),
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
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => false,
			'capabilities'      => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'manage_options',
			),
			'meta_box_cb'       => false,
		);

		register_taxonomy( self::TAXONOMY_NAME, array(), $args );
	}

	/**
	 * Save field group categories when field group is updated.
	 *
	 * @since 1.0.0
	 * @param array $field_group The field group data.
	 * @return void
	 */
	public function save_field_group_categories( $field_group ): void {
		// Verify nonce for security.
		if ( ! isset( $_POST['acf_field_categories_nonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['acf_field_categories_nonce'] ) ), 'acf_field_categories_' . $field_group['ID'] ) ) {
			return;
		}

		// Get selected categories from POST data.
		$selected_categories = isset( $_POST['acf_field_categories'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['acf_field_categories'] ) ) : array();

		// Sanitize the category IDs.
		$selected_categories = array_map( 'intval', $selected_categories );
		$selected_categories = array_filter( $selected_categories ); // Remove any zero values.

		// Validate that all selected categories exist.
		$valid_categories = array();
		foreach ( $selected_categories as $category_id ) {
			$term = get_term( $category_id, self::TAXONOMY_NAME );
			if ( $term && ! is_wp_error( $term ) ) {
				$valid_categories[] = $category_id;
			}
		}

		// Save the categories as post meta.
		if ( ! empty( $valid_categories ) ) {
			update_post_meta( $field_group['ID'], self::FIELD_CATEGORIES_META_KEY, $valid_categories );
		} else {
			// If no categories selected, delete the meta.
			delete_post_meta( $field_group['ID'], self::FIELD_CATEGORIES_META_KEY );
		}

		// Optional: Log for debugging.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log(
				sprintf(
					'ACF Field Group %d categories updated: %s',
					$field_group['ID'],
					implode( ', ', $valid_categories )
				)
			);
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
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for filtering.
		if ( ! is_admin() || ! $query->is_main_query() || ! isset( $_GET['field_category'] ) ) {
			return;
		}

		// Only apply to ACF field group queries.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter check.
		if ( ! isset( $_GET['post_type'] ) || self::ACF_POST_TYPE !== $_GET['post_type'] ) {
			return;
		}

		// Sanitize the category ID (read-only operation, no nonce needed for GET filtering).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for filtering.
		$selected_category = absint( $_GET['field_category'] );

		if ( $selected_category > 0 ) {
			// Get all field group IDs that have this category assigned.
			$field_group_ids = $this->get_field_group_ids_by_category( $selected_category );

			// If we found field groups with this category, filter to only those.
			if ( ! empty( $field_group_ids ) ) {
				$query->set( 'post__in', $field_group_ids );
			} else {
				// No field groups found with this category, show none.
				$query->set( 'post__in', array( 0 ) );
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
	 * @param int    $post_id The post ID.
	 * @return void
	 */
	public function display_category_column( $column, $post_id ) {
		if ( 'category' === $column ) {
			// Get assigned categories from post meta.
			$assigned_categories = $this->get_assigned_categories( $post_id );

			if ( ! empty( $assigned_categories ) ) {
				$category_links = array();

				foreach ( $assigned_categories as $category_id ) {
					$term = get_term( $category_id, self::TAXONOMY_NAME );

					if ( $term && ! is_wp_error( $term ) ) {
						// Create a link to filter by this category.
						$filter_url = add_query_arg(
							array(
								'post_type'      => self::ACF_POST_TYPE,
								'field_category' => $category_id,
							),
							admin_url( 'edit.php' )
						);

						$category_links[] = sprintf(
							'<a href="%s" title="%s">%s</a>',
							esc_url( $filter_url ),
							/* translators: %s: Category name */
							esc_attr( sprintf( __( 'Filter by %s', 'codesoup-acf-admin-categories' ), $term->name ) ),
							esc_html( $term->name )
						);
					}
				}

				if ( ! empty( $category_links ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped in sprintf above.
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
