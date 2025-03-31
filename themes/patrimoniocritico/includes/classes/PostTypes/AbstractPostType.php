<?php /* phpcs:ignore WordPress.Files.FileName */
/**
 * AbstractPostType
 *
 * @package PatrimonioCritico
 */

namespace PatrimonioCritico\PostTypes;

use PatrimonioCritico\Module;

/**
 * Abstract class for post types.
 */
abstract class AbstractPostType extends Module {
	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	abstract public function get_name(): string;

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label(): string;

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label(): string;

	/**
	 * Get the menu icon for the post type.
	 *
	 * This can be a base64 encoded SVG, a dashicons class or 'none' to leave it empty so it can be filled with CSS.
	 *
	 * @see https://developer.wordpress.org/resource/dashicons/
	 *
	 * @return string
	 */
	abstract public function get_menu_icon(): string;

	/**
	 * Get the menu position for the post type.
	 *
	 * @return int|null
	 */
	public function get_menu_position(): ?int {
		return null;
	}

	/**
	 * Is the post type hierarchical?
	 *
	 * @return bool
	 */
	public function is_hierarchical(): bool {
		return false;
	}

	/**
	 * Default post type supported feature names.
	 *
	 * @return array
	 */
	public function get_editor_supports(): array {
		return array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'revisions',
		);
	}

	/**
	 * Get the options for the post type.
	 *
	 * @return array
	 */
	public function get_options(): array {
		return array(
			'labels'            => $this->get_labels(),
			'public'            => true,
			'has_archive'       => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'supports'          => $this->get_editor_supports(),
			'menu_icon'         => $this->get_menu_icon(),
			'menu_position'     => $this->get_menu_position(),
			'hierarchical'      => $this->is_hierarchical(),
		);
	}

	/**
	 * Get the labels for the post type.
	 *
	 * @return array
	 */
	public function get_labels(): array {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable -- ignoring template strings without translators placeholder since this is dynamic
		return array(
			'name'                     => $plural_label,
			// Already translated via get_plural_label().
			'singular_name'            => $singular_label,
			// Already translated via get_singular_label().
			'add_new_item'             => sprintf( __( 'Add New %s', 'patrimoniocritico' ), $singular_label ),
			'edit_item'                => sprintf( __( 'Edit %s', 'patrimoniocritico' ), $singular_label ),
			'new_item'                 => sprintf( __( 'New %s', 'patrimoniocritico' ), $singular_label ),
			'view_item'                => sprintf( __( 'View %s', 'patrimoniocritico' ), $singular_label ),
			'view_items'               => sprintf( __( 'View %s', 'patrimoniocritico' ), $plural_label ),
			'search_items'             => sprintf( __( 'Search %s', 'patrimoniocritico' ), $plural_label ),
			'not_found'                => sprintf( __( 'No %s found.', 'patrimoniocritico' ), strtolower( $plural_label ) ),
			'not_found_in_trash'       => sprintf( __( 'No %s found in Trash.', 'patrimoniocritico' ), strtolower( $plural_label ) ),
			'parent_item_colon'        => sprintf( __( 'Parent %s:', 'patrimoniocritico' ), $plural_label ),
			'all_items'                => sprintf( __( 'All %s', 'patrimoniocritico' ), $plural_label ),
			'archives'                 => sprintf( __( '%s Archives', 'patrimoniocritico' ), $singular_label ),
			'attributes'               => sprintf( __( '%s Attributes', 'patrimoniocritico' ), $singular_label ),
			'insert_into_item'         => sprintf( __( 'Insert into %s', 'patrimoniocritico' ), strtolower( $singular_label ) ),
			'uploaded_to_this_item'    => sprintf( __( 'Uploaded to this %s', 'patrimoniocritico' ), strtolower( $singular_label ) ),
			'filter_items_list'        => sprintf( __( 'Filter %s list', 'patrimoniocritico' ), strtolower( $plural_label ) ),
			'items_list_navigation'    => sprintf( __( '%s list navigation', 'patrimoniocritico' ), $plural_label ),
			'items_list'               => sprintf( __( '%s list', 'patrimoniocritico' ), $plural_label ),
			'item_published'           => sprintf( __( '%s published.', 'patrimoniocritico' ), $singular_label ),
			'item_published_privately' => sprintf( __( '%s published privately.', 'patrimoniocritico' ), $singular_label ),
			'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'patrimoniocritico' ), $singular_label ),
			'item_scheduled'           => sprintf( __( '%s scheduled.', 'patrimoniocritico' ), $singular_label ),
			'item_updated'             => sprintf( __( '%s updated.', 'patrimoniocritico' ), $singular_label ),
			'menu_name'                => $plural_label,
			'name_admin_bar'           => $singular_label,
		);
		// phpcs:enable
	}

	/**
	 * Registers a post type and associates its taxonomies.
	 *
	 * @uses $this->get_name() to get the post's type name.
	 *
	 * @return bool Whether this theme has supports for this post type.
	 */
	public function register(): bool {
		$this->register_post_type();
		$this->register_taxonomies();

		$this->after_register();

		return true;
	}

	/**
	 * Registers the current post type with WordPress.
	 */
	public function register_post_type(): void {
		register_post_type(
			$this->get_name(),
			$this->get_options()
		);
	}

	/**
	 * Registers the taxonomies declared with the current post type.
	 */
	public function register_taxonomies(): void {
		$taxonomies = $this->get_supported_taxonomies();

		$object_type = $this->get_name();

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				register_taxonomy_for_object_type(
					$taxonomy,
					$object_type
				);
			}
		}
	}

	/**
	 * Returns the default supported taxonomies. The subclass should declare the
	 * Taxonomies that it supports here if required.
	 *
	 * @return array
	 */
	public function get_supported_taxonomies(): array {
		return array();
	}

	/**
	 * Run any code after the post type has been registered.
	 */
	public function after_register(): void {
		// Do nothing.
	}
}
