<?php
/**
 * The About page, built to the design handoff (About.dc.html).
 *
 * Sections: hero, dark "our story" split, team, four numbered values, burgundy stat band,
 * centred CTA with partner badges.
 *
 * The design file carries an empty image drop-zone where the team photograph goes. Shipping
 * a visible placeholder would be worse than shipping nothing, so the figure renders only
 * when the page has a featured image set in wp-admin.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx     = qx_content();
$a      = $qx['aboutPage'];
$lbl    = 'qx-label' . ( qx_is_ar() ? ' qx-label--ar' : '' );

$qx_lines = static function ( array $lines ): string {
	return implode( '<br>', array_map( 'esc_html', $lines ) );
};
?>

<?php /* ─── HERO ─────────────────────────────────────────────────────────── */ ?>
<section class="qx-phero">
	<?php echo qx_watermark( 'qx-watermark--page', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="qx-phero__body">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label"><?php echo esc_html( $a['label'] ); ?></p>
		<h1 class="qx-display qx-display--sm qx-reveal"><?php echo wp_kses_post( $qx_lines( $a['lines'] ) ); ?></h1>
		<p class="qx-body qx-phero__lead qx-reveal"><?php echo esc_html( $a['lead'] ); ?></p>
	</div>
</section>

<?php /* ─── OUR STORY ────────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-dark">
	<div class="qx-shell qx-split">
		<div class="qx-reveal">
			<p class="<?php echo esc_attr( $lbl ); ?> qx-label--onDark qx-gap-label">
				<?php echo esc_html( $a['story']['label'] ); ?>
			</p>
			<h2 class="qx-h2 qx-h2--sm"><?php echo wp_kses_post( $qx_lines( $a['story']['lines'] ) ); ?></h2>
		</div>
		<div class="qx-split__paras qx-reveal">
			<?php foreach ( $a['story']['paras'] as $qx_p ) : ?>
				<p class="qx-body qx-body--onDark"><?php echo esc_html( $qx_p ); ?></p>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── TEAM ─────────────────────────────────────────────────────────── */ ?>
<section class="qx-section">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label qx-reveal">
			<?php echo esc_html( $a['team']['label'] ); ?>
		</p>
		<div class="qx-row qx-reveal">
			<h2 class="qx-h2"><?php echo wp_kses_post( $qx_lines( $a['team']['lines'] ) ); ?></h2>
			<p class="qx-body qx-body--sm" style="max-inline-size:440px;">
				<?php echo esc_html( $a['team']['body'] ); ?>
			</p>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="qx-figure qx-reveal">
				<?php the_post_thumbnail( 'full', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
			</figure>
		<?php endif; ?>
	</div>
</section>

<?php /* ─── VALUES ───────────────────────────────────────────────────────── */ ?>
<section class="qx-section">
	<div class="qx-shell">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-head qx-reveal">
			<?php echo esc_html( $a['values']['label'] ); ?>
		</p>
		<div class="qx-reveal">
			<?php foreach ( $a['values']['items'] as $qx_v ) : ?>
				<div class="qx-val">
					<span class="qx-val__n"><?php echo esc_html( $qx_v['n'] ); ?></span>
					<span class="qx-val__title"><?php echo esc_html( $qx_v['title'] ); ?></span>
					<span class="qx-val__body"><?php echo wp_kses_post( qx_ltr_nums( $qx_v['body'] ) ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php /* ─── STAT BAND ────────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-section--sm qx-burgundy">
	<div class="qx-shell qx-statrow">
		<?php foreach ( $a['stats'] as $qx_s ) : ?>
			<div class="qx-reveal">
				<span dir="ltr" class="qx-stat qx-stat--gold"><?php echo esc_html( $qx_s['n'] ); ?></span>
				<span class="qx-stat__cap"><?php echo esc_html( $qx_s['cap'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php /* ─── CTA ──────────────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-section--lg qx-close">
	<div class="qx-close__inner">
		<div class="qx-badges qx-badges--onLight qx-reveal" dir="ltr">
			<?php foreach ( $qx['testimonial']['badges'] as $qx_b ) : ?>
				<span><?php echo esc_html( $qx_b ); ?></span>
			<?php endforeach; ?>
		</div>
		<h2 class="qx-display qx-display--md qx-gap-body qx-reveal"><?php echo esc_html( $a['cta'] ); ?></h2>
		<div class="qx-close__action qx-reveal">
			<a class="qx-btn" href="<?php echo esc_url( qx_whatsapp_url( 'about' ) ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $qx['hero']['cta'] ); ?>
			</a>
		</div>
	</div>
</section>
