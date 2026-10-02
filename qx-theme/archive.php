<?php
/**
 * Category, tag and date archives.
 *
 * The old site had five WordPress categories whose archive pages were indexed but reachable
 * from nowhere. They keep their URLs and now get the brand design.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

$lbl = 'qx-label' . ( qx_is_ar() ? ' qx-label--ar' : '' );
?>

<section class="qx-article">
	<div class="qx-shell">
		<header>
			<p class="<?php echo esc_attr( $lbl ); ?>"><?php echo esc_html( qx_t( 'المجلة', 'JOURNAL' ) ); ?></p>
			<h1 class="qx-h2 qx-h2--lg" style="margin-block:28px 24px;">
				<?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?>
			</h1>
			<?php
			$qx_desc = wp_strip_all_tags( (string) get_the_archive_description() );
			if ( '' !== trim( $qx_desc ) ) :
				?>
				<p class="qx-body" style="max-inline-size:620px;"><?php echo esc_html( $qx_desc ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="qx-cards">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<a class="qx-card" href="<?php the_permalink(); ?>">
						<time class="qx-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
						<h2 class="qx-card__title"><?php the_title(); ?></h2>
						<p class="qx-body qx-body--sm">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), qx_is_ar() ? 20 : 26, '…' ) ); ?>
						</p>
					</a>
					<?php
				endwhile;
				?>
			</div>

			<?php
			echo '<nav class="qx-pagination" aria-label="' . esc_attr( qx_t( 'التنقل بين الصفحات', 'Pagination' ) ) . '">';
			echo wp_kses_post(
				(string) paginate_links(
					array(
						'prev_text' => qx_t( 'السابق', 'Previous' ),
						'next_text' => qx_t( 'التالي', 'Next' ),
					)
				)
			);
			echo '</nav>';
			?>
		<?php else : ?>
			<p class="qx-body" style="margin-block-start:40px;">
				<?php echo esc_html( qx_t( 'لا توجد مقالات هنا بعد.', 'Nothing here yet.' ) ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
