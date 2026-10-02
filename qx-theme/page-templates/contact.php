<?php
/**
 * Template Name: QX — Contact
 * Template Post Type: page
 *
 * An explicit way to assign the designed Contact layout to a page whose slug is not
 * `تواصل-معنا` or `contact-us`.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/page', 'contact' );
}

get_footer();
