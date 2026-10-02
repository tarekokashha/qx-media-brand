<?php
/**
 * Homepage — built to the design handoff (Home.dc.html).
 *
 * Section order, exactly as specified:
 *   1 Hero (headline, lead, 2 CTAs, 4-stat row)
 *   2 About teaser        — charcoal
 *   3 Services            — 6 numbered rows
 *   4 Case studies        — burgundy, 3 stat cards
 *   5 Client wordmark strip
 *   6 Process             — 3 numbered steps
 *   7 Testimonial         — charcoal, + partner badges
 *   8 Final CTA
 *
 * Spacing is expressed in classes, not inline styles, because the mobile block has to
 * compress it and an inline style would outrank a media query.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();

$qx     = qx_content();
$qx_ar  = qx_is_ar();
$lbl    = 'qx-label' . ( $qx_ar ? ' qx-label--ar' : '' );

/** Render a headline whose line breaks are hand-set per locale. */
$qx_lines = static function ( array $lines ): string {
	return implode( '<br>', array_map( 'esc_html', $lines ) );
};
?>

<?php
/* ─── 1 · HERO ───────────────────────────────────────────────────────
   The scroll-driven 3D "Impact Engine". It carries the headline, the lead, both
   CTAs and the four metrics, so the old static hero and its separate stat row are
   both folded into it. See template-parts/hero-3d.php. */
get_template_part( 'template-parts/hero', '3d' );
?>

<?php /* ─── 2 · ABOUT TEASER ─────────────────────────────────────────────── */ ?>
<section class="qx-section qx-dark">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-label--onDark qx-gap-label qx-reveal">
			<?php echo esc_html( $qx['about']['label'] ); ?>
		</p>
		<h2 class="qx-h2 qx-h2--lg qx-reveal"><?php echo wp_kses_post( $qx_lines( $qx['about']['lines'] ) ); ?></h2>
		<div class="qx-row qx-gap-body qx-reveal">
			<p class="qx-body qx-body--onDark" style="max-inline-size:560px;">
				<?php echo esc_html( $qx['about']['body'] ); ?>
			</p>
			<a class="qx-tlink qx-tlink--gold" href="<?php echo esc_url( qx_page_url( 'about' ) ); ?>">
				<?php echo esc_html( $qx['about']['link'] ); ?>
			</a>
		</div>
	</div>
</section>

<?php /* ─── 3 · SERVICES ─────────────────────────────────────────────────── */ ?>
<section class="qx-section">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label qx-reveal">
			<?php echo esc_html( $qx['services']['label'] ); ?>
		</p>
		<div class="qx-row qx-gap-head qx-reveal">
			<h2 class="qx-h2" style="max-inline-size:720px;">
				<?php echo wp_kses_post( $qx_lines( $qx['services']['lines'] ) ); ?>
			</h2>
			<a class="qx-tlink" href="<?php echo esc_url( qx_page_url( 'services' ) ); ?>">
				<?php echo esc_html( $qx['services']['link'] ); ?>
			</a>
		</div>

		<div class="qx-reveal">
			<?php foreach ( $qx['services']['items'] as $qx_svc ) : ?>
				<a class="qx-svc" href="<?php echo esc_url( home_url( $qx_svc['href'] ) ); ?>">
					<span class="qx-svc__n"><?php echo esc_html( $qx_svc['n'] ); ?></span>
					<span>
						<span class="qx-svc__name"><?php echo esc_html( $qx_svc['name'] ); ?></span>
						<span class="qx-svc__meta"><?php echo esc_html( $qx_svc['meta'] ); ?></span>
					</span>
					<span class="qx-svc__arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── 4 · CASE STUDIES ─────────────────────────────────────────────── */ ?>
<section class="qx-section qx-burgundy" id="work">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-label--onBurgundy qx-gap-label qx-reveal">
			<?php echo esc_html( $qx['cases']['label'] ); ?>
		</p>
		<h2 class="qx-h2 qx-gap-head qx-reveal"><?php echo esc_html( $qx['cases']['title'] ); ?></h2>

		<div class="qx-cases qx-reveal">
			<?php foreach ( $qx['cases']['items'] as $qx_c ) : ?>
				<article class="qx-case">
					<div>
						<div class="qx-case__client"><?php echo esc_html( $qx_c['client'] ); ?></div>
						<div class="qx-case__sector"><?php echo esc_html( $qx_c['sector'] ); ?></div>
					</div>
					<div>
						<span dir="ltr" class="qx-stat qx-stat--lg qx-stat--gold"><?php echo esc_html( $qx_c['metric'] ); ?></span>
						<div class="qx-case__metric"><?php echo wp_kses_post( qx_ltr_nums( $qx_c['cap'] ) ); ?></div>
					</div>
					<div class="qx-case__sub">
						<?php foreach ( $qx_c['subs'] as $qx_sub ) : ?>
							<span><?php echo wp_kses_post( qx_ltr_nums( $qx_sub ) ); ?></span>
						<?php endforeach; ?>
					</div>
					<p class="qx-case__note"><?php echo esc_html( $qx_c['note'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="qx-cases__foot qx-reveal">
			<a class="qx-btn qx-btn--onDark" href="<?php echo esc_url( qx_whatsapp_url( 'cases' ) ); ?>"
				target="_blank" rel="noopener"><?php echo esc_html( $qx['cases']['cta'] ); ?></a>
		</div>
	</div>
</section>

<?php /* ─── 5 · CLIENT STRIP ─────────────────────────────────────────────── */ ?>
<section class="qx-section qx-section--sm">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-reveal"><?php echo esc_html( $qx['clients']['label'] ); ?></p>
		<div class="qx-clients qx-reveal">
			<?php foreach ( $qx['clients']['names'] as $qx_name ) : ?>
				<span><?php echo esc_html( $qx_name ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── 6 · PROCESS ──────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-section--rule">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label qx-reveal">
			<?php echo esc_html( $qx['process']['label'] ); ?>
		</p>
		<h2 class="qx-h2 qx-reveal"><?php echo wp_kses_post( $qx_lines( $qx['process']['lines'] ) ); ?></h2>

		<div class="qx-steps qx-reveal">
			<?php foreach ( $qx['process']['steps'] as $qx_step ) : ?>
				<div class="qx-step">
					<span class="qx-step__n"><?php echo esc_html( $qx_step['n'] ); ?></span>
					<h3 class="qx-h3"><?php echo esc_html( $qx_step['title'] ); ?></h3>
					<ul>
						<?php foreach ( $qx_step['points'] as $qx_p ) : ?>
							<li class="qx-body qx-body--sm"><?php echo esc_html( $qx_p ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── 7 · TESTIMONIAL ──────────────────────────────────────────────── */ ?>
<section class="qx-section qx-dark">
	<div class="qx-shell">
		<figure class="qx-quote__fig qx-reveal">
			<span class="qx-quote__mark" aria-hidden="true">&rdquo;</span>
			<blockquote class="qx-quote">
				<?php echo esc_html( $qx['testimonial']['quote'] ); ?>
			</blockquote>
			<figcaption class="qx-quote__by"><?php echo esc_html( $qx['testimonial']['by'] ); ?></figcaption>
		</figure>

		<div class="qx-badges qx-reveal" dir="ltr">
			<?php foreach ( $qx['testimonial']['badges'] as $qx_b ) : ?>
				<span><?php echo esc_html( $qx_b ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── 8 · FINAL CTA ────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-section--lg qx-cta">
	<?php echo qx_watermark( 'qx-watermark--cta' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="qx-cta__inner">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label qx-reveal">
			<?php echo esc_html( $qx['cta']['label'] ); ?>
		</p>
		<h2 class="qx-display qx-display--md qx-reveal">
			<?php echo esc_html( $qx['cta']['title'] ); ?>
		</h2>
		<p class="qx-body qx-cta__lead qx-reveal">
			<?php echo esc_html( $qx['cta']['body'] ); ?>
		</p>
		<div class="qx-cta__actions qx-reveal">
			<a class="qx-btn" href="<?php echo esc_url( qx_whatsapp_url( 'final-cta' ) ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $qx['cta']['wa'] ); ?>
			</a>
			<a class="qx-tlink" href="mailto:<?php echo esc_attr( QX_EMAIL ); ?>">
				<span dir="ltr"><?php echo esc_html( QX_EMAIL ); ?></span>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
