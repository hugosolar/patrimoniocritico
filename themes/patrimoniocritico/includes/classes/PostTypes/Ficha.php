<?php /* phpcs:ignore WordPress.Files.FileName */
/**
 * Promo Banners
 *
 * @package MSXM
 */

namespace PatrimonioCritico\PostTypes;

use WP_Query;
use WP_Term;

/**
 * PromoBanners post type.
 */
class Ficha extends AbstractPostType {
	/**
	 * Post type name.
	 *
	 * @var string
	 */
	public static string $name = 'patrimonio-ficha';

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return self::$name;
	}

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	public function get_singular_label(): string {
		return esc_html__( 'Ficha', 'msxm' );
	}

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	public function get_plural_label(): string {
		return esc_html__( 'Fichas', 'msxm' );
	}

	/**
	 * Get the menu icon for the post type.
	 *
	 * This can be a base64 encoded SVG, a dashicons class or 'none' to leave it empty so it can be filled with CSS.
	 *
	 * @see https://developer.wordpress.org/resource/dashicons/
	 *
	 * @return string
	 */
	public function get_menu_icon(): string {
		return 'dashicons-media-document';
	}

	/**
	 * Can the class be registered?
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Is the post type hierarchical?
	 *
	 * @return bool
	 */
	public function is_hierarchical(): bool {
		return true;
	}

	/**
	 * Add template to the post type.
	 */
	public function get_options(): array {
		// get options from parent class
		$options              = parent::get_options();
		$supported_taxonomies = array(
			'category',
			'post_tag',
		);

		$options['taxonomies']          = $supported_taxonomies;
		$options['exclude_from_search'] = true;

		return $options;
	}
}
