<?php
/**
 * Brand artwork and navigation markup.
 *
 * The repository ships no logo files: a brand's marks belong to the brand, not to the code.
 * Templates therefore never print an <img> for the logo directly. They ask qx_mark(), which
 * prints the artwork when a file with that name exists in assets/img/ and falls back to the
 * site title set as a wordmark when it does not. A fresh install renders a clean, working
 * header instead of a broken-image icon.
 *
 * @package QX
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A logo mark: the image in assets/img/ if it exists, otherwise the site title as text.
 *
 * @param string $file       File name inside assets/img/.
 * @param string $class      CSS class(es) for the element.
 * @param string $alt        Alternative text for the image form. Ignored when decorative.
 * @param int    $width      Intrinsic width, so the image never shifts layout.
 * @param int    $height     Intrinsic height.
 * @param bool   $decorative True for a second copy of the mark that screen readers must skip
 *                           (the nav swaps two marks with CSS; only one should be announced).
 * @param bool   $lazy       Add loading="lazy" for marks below the fold.
 */
function qx_mark( string $file, string $class, string $alt, int $width, int $height, bool $decorative = false, bool $lazy = false ): string {
	$path = get_template_directory() . '/assets/img/' . $file;

	if ( is_readable( $path ) ) {
		return sprintf(
			'<img class="%1$s" src="%2$s" alt="%3$s"%4$s width="%5$d" height="%6$d"%7$s decoding="async">',
			esc_attr( $class ),
			esc_url( get_template_directory_uri() . '/assets/img/' . $file ),
			esc_attr( $decorative ? '' : $alt ),
			$decorative ? ' aria-hidden="true"' : '',
			$width,
			$height,
			$lazy ? ' loading="lazy"' : ''
		);
	}

	return sprintf(
		'<span class="%1$s qx-wordmark"%2$s>%3$s</span>',
		esc_attr( $class ),
		$decorative ? ' aria-hidden="true"' : '',
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * The large faint mark behind a hero or call to action. Purely decorative, so when there is
 * no artwork there is nothing to print.
 *
 * @param string $class    CSS modifier class.
 * @param bool   $priority True for above-the-fold use (fetchpriority low, never lazy).
 */
function qx_watermark( string $class, bool $priority = false ): string {
	$file = 'logo-mark-terracotta.png';

	if ( ! is_readable( get_template_directory() . '/assets/img/' . $file ) ) {
		return '';
	}

	return sprintf(
		'<img class="qx-watermark %1$s" src="%2$s" alt="" width="859" height="463"%3$s decoding="async">',
		esc_attr( $class ),
		esc_url( get_template_directory_uri() . '/assets/img/' . $file ),
		$priority ? ' fetchpriority="low"' : ' loading="lazy"'
	);
}

/**
 * Walker that prints a menu as bare anchors, no <li> wrappers.
 *
 * The header lays its links out as flex children of <nav>, so the design wants plain <a>
 * elements. wp_nav_menu() always wraps items in <li>, and with items_wrap set to '%3$s' (no
 * <ul>) that left list items with no list parent, which is invalid HTML and fails the
 * Lighthouse "list items are contained in a list" audit. Emitting the anchors directly
 * removes the invalid structure instead of papering over it.
 */
class QX_Bare_Link_Walker extends Walker_Nav_Menu {

	/** @param string $output Passed by reference. */
	public function start_lvl( &$output, $depth = 0, $args = null ) {} // phpcs:ignore

	/** @param string $output Passed by reference. */
	public function end_lvl( &$output, $depth = 0, $args = null ) {} // phpcs:ignore

	/**
	 * @param string   $output            Passed by reference.
	 * @param \WP_Post $data_object       The menu item.
	 * @param int      $depth             Depth (always 0, the menu is one level).
	 * @param mixed    $args              wp_nav_menu() arguments.
	 * @param int      $current_object_id Current object ID.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) { // phpcs:ignore
		$current = ! empty( $data_object->current ) || in_array( 'current-menu-item', (array) $data_object->classes, true );

		$output .= sprintf(
			'<a href="%1$s"%2$s>%3$s</a>',
			esc_url( (string) $data_object->url ),
			$current ? ' aria-current="page"' : '',
			esc_html( (string) $data_object->title )
		);
	}

	/** @param string $output Passed by reference. */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {} // phpcs:ignore
}
