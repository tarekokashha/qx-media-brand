<?php
/**
 * A single article.
 *
 * Published articles keep their URLs untouched and gain the brand's typography: the Arabic
 * face at a proper reading measure (58ch) with Naskh leading, which is most of what makes
 * long Arabic text readable.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();
?>

<article class="qx-article">
	<div class="qx-shell qx-shell--narrow">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<header style="margin-block-end:56px;">
				<div style="display:flex;flex-wrap:wrap;gap:24px;align-items:baseline;margin-block-end:28px;">
					<?php
					$qx_cats = get_the_category();
					if ( ! empty( $qx_cats ) ) :
						?>
						<a class="qx-label<?php echo qx_is_ar() ? ' qx-label--ar' : ''; ?>"
							href="<?php echo esc_url( (string) get_category_link( $qx_cats[0]->term_id ) ); ?>">
							<?php echo esc_html( $qx_cats[0]->name ); ?>
						</a>
					<?php endif; ?>
					<time class="qx-body qx-body--sm" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" dir="ltr">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
				</div>

				<h1 class="qx-h2 qx-h2--lg"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure style="margin:0 0 56px;">
					<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="qx-prose">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>

		<aside style="margin-block-start:80px;padding-block-start:44px;border-block-start:1px solid var(--qx-ink-16);">
			<a class="qx-btn" href="<?php echo esc_url( qx_whatsapp_url( 'article' ) ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( qx_t( 'احجز استشارة مجانية', 'Book a free consultation' ) ); ?>
			</a>
		</aside>
	</div>
</article>

<?php
get_footer();
