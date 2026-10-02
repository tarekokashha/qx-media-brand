<?php
/**
 * Template Name: QX — Services
 * Template Post Type: page
 *
 * An explicit way to assign the designed Services layout to a page whose slug is not
 * `خدماتنا` or `services`.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/page', 'services' );
}

get_footer();
