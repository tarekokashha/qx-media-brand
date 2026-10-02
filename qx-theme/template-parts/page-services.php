<?php
/**
 * The Services page, built to the design handoff (Services.dc.html).
 *
 * Hero, then six full-width rows (number + name against a paragraph and a four-item
 * sub-list, separated by hairlines), then a dark closing CTA.
 *
 * That closing button stays a cream outline rather than becoming solid burgundy: it sits on
 * charcoal, where burgundy has almost no contrast. The round-2 brief calls this out
 * explicitly.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx     = qx_content();
$s      = $qx['servicesPage'];
$lbl    = 'qx-label' . ( qx_is_ar() ? ' qx-label--ar' : '' );

$qx_lines = static function ( array $lines ): string {
	return implode( '<br>', array_map( 'esc_html', $lines ) );
};
?>

<?php /* ─── HERO ─────────────────────────────────────────────────────────── */ ?>
<section class="qx-phero">
	<?php echo qx_watermark( 'qx-watermark--page', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="qx-phero__body">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label"><?php echo esc_html( $s['label'] ); ?></p>
		<h1 class="qx-display qx-display--sm qx-reveal"><?php echo wp_kses_post( $qx_lines( $s['lines'] ) ); ?></h1>
		<p class="qx-body qx-phero__lead qx-reveal"><?php echo esc_html( $s['lead'] ); ?></p>
	</div>
</section>

<?php /* ─── THE SIX SERVICES ─────────────────────────────────────────────── */ ?>
<section class="qx-section" style="padding-block-start:0;">
	<div class="qx-shell">
		<?php foreach ( $s['items'] as $qx_s ) : ?>
			<div class="qx-srow qx-reveal">
				<div>
					<span class="qx-srow__n"><?php echo esc_html( $qx_s['n'] ); ?></span>
					<h2 class="qx-h2 qx-h2--sm qx-srow__name"><?php echo esc_html( $qx_s['name'] ); ?></h2>
				</div>
				<div>
					<p class="qx-srow__body"><?php echo esc_html( $qx_s['body'] ); ?></p>
					<ul class="qx-srow__list">
						<?php foreach ( $qx_s['points'] as $qx_p ) : ?>
							<li><?php echo esc_html( $qx_p ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php /* ─── CLOSING CTA ──────────────────────────────────────────────────── */ ?>
<section class="qx-section qx-dark qx-close">
	<div class="qx-close__inner">
		<h2 class="qx-h2 qx-reveal"><?php echo esc_html( $s['closing']['title'] ); ?></h2>
		<p class="qx-body qx-body--onDark qx-close__lead qx-reveal">
			<?php echo wp_kses_post( qx_ltr_nums( $s['closing']['body'] ) ); ?>
		</p>
		<div class="qx-close__action qx-reveal">
			<a class="qx-btn qx-btn--onDark" href="<?php echo esc_url( qx_whatsapp_url( 'services' ) ); ?>"
				target="_blank" rel="noopener"><?php echo esc_html( $qx['hero']['cta'] ); ?></a>
		</div>
	</div>
</section>
