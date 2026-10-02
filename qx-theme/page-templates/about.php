<?php
/**
 * Template Name: QX — About
 * Template Post Type: page
 *
 * An explicit way to assign the designed About layout to a page whose slug is not
 * `من-نحن` or `about-us`. Detection by slug already covers the live pages, so this exists
 * only as an override.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/page', 'about' );
}

get_footer();
