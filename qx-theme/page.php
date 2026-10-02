<?php
/**
 * A page.
 *
 * Three branches, in order:
 *
 *   1. About / Services / Contact. These are the pages the design handoff covers, and they
 *      were still serving their old Elementor layouts. When qx_page_role() recognises one,
 *      the designed template renders instead and Elementor's stored content is bypassed.
 *      Nothing is deleted: the Elementor data stays in the database, so reverting is a
 *      matter of removing this branch.
 *
 *   2. Any other Elementor-built page (the individual service pages, and anything the
 *      client builds later). Elementor renders its own markup and injects its own CSS
 *      regardless of the active theme, so this template stays out of the way.
 *
 *   3. Everything else gets the QX prose treatment.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

$qx_role = qx_page_role();

if ( null !== $qx_role ) {

	// The designed pages don't use the_content(), but the loop still has to be set up:
	// the About template reads the featured image off the current post.
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/page', $qx_role );
	}

} else {

	$qx_is_elementor = false;
	if ( class_exists( '\Elementor\Plugin' ) ) {
		$qx_doc          = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
		$qx_is_elementor = $qx_doc && $qx_doc->is_built_with_elementor();
	}

	if ( $qx_is_elementor ) {
		?>
		<div class="qx-elementor-page">
			<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
			?>
		</div>
		<?php
	} else {
		?>
		<article class="qx-article">
			<div class="qx-shell">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<header class="qx-gap-head">
						<h1 class="qx-display qx-display--md"><?php the_title(); ?></h1>
					</header>

					<div class="qx-prose">
						<?php the_content(); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</article>
		<?php
	}
}

get_footer();
