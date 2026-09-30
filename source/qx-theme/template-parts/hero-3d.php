<?php
/**
 * The homepage hero: "The Impact Engine".
 *
 * A pinned section with a sticky WebGL canvas. Scroll progress from 0 to 1 drives one
 * continuous transformation of the QX mark through five phases (potential, discover, shape,
 * launch, impact). Ported from the approved prototype in the handoff
 * (QXHero.dc.html + hero-engine.js + hero-modules.js).
 *
 * Everything a visitor actually needs is semantic HTML above the canvas, never inside WebGL:
 * the headline, the lead, both CTAs and the four metrics. The canvas is decorative and
 * aria-hidden. If WebGL is missing, if JavaScript never runs, or if the visitor prefers
 * reduced motion, the CSS poster stays and this markup is the hero on its own, at 100vh
 * instead of a pointless five-screen pin.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx      = qx_content();
$qx_img  = get_template_directory_uri() . '/assets/img';
$qx_hero = get_template_directory_uri() . '/assets/js/hero';
$qx_ar   = qx_is_ar();

$qx_lines = static function ( array $lines ): string {
	return implode( '<br>', array_map( 'esc_html', $lines ) );
};
?>

<section class="qx-hero3d" data-qx-hero
	data-qx-hero-src="<?php echo esc_url( $qx_hero . '/engine.min.js' ); ?>"
	data-qx-hero-shape="<?php echo esc_url( $qx_hero . '/qx-shape.json' ); ?>">
	<div class="qx-hero3d__stage">

		<?php /* Decorative. Every word a visitor needs is in the overlay below, not in here. */ ?>
		<canvas class="qx-hero3d__canvas" data-qx-canvas aria-hidden="true"></canvas>

		<?php
		/* Paints before WebGL initialises, and stays if WebGL never arrives. Pure CSS:
		   zero bytes above the fold, no banding, and correct at any viewport ratio. */
		?>
		<div class="qx-hero3d__poster" aria-hidden="true"></div>

		<div class="qx-hero3d__scrim" aria-hidden="true"></div>

		<div class="qx-hero3d__overlay">
			<div class="qx-hero3d__intro" data-hero-in="-0.05,0.005" data-hero-out="0.05,0.15" data-hero-shift="0">
				<p class="qx-label qx-hero3d__eyebrow"><?php echo esc_html( $qx['hero']['label'] ); ?></p>

				<h1 class="qx-hero3d__h1"><?php echo wp_kses_post( $qx_lines( $qx['hero']['lines'] ) ); ?></h1>

				<p class="qx-hero3d__lead"><?php echo esc_html( $qx['hero']['lead'] ); ?></p>

				<div class="qx-hero3d__actions">
					<a class="qx-btn qx-btn--ember" href="<?php echo esc_url( qx_whatsapp_url( 'hero' ) ); ?>"
						target="_blank" rel="noopener"><?php echo esc_html( $qx['hero']['cta'] ); ?></a>
					<a class="qx-tlink qx-tlink--onDark" href="#work"><?php echo esc_html( $qx['hero']['cta2'] ); ?></a>
				</div>
			</div>

			<?php /* Three stage labels occupying the same spot, cross-fading with progress. */ ?>
			<div class="qx-hero3d__phases" aria-hidden="true">
				<?php
				$qx_bands = array(
					array( 'in' => '0.17,0.22', 'out' => '0.32,0.37' ),
					array( 'in' => '0.38,0.43', 'out' => '0.52,0.57' ),
					array( 'in' => '0.58,0.63', 'out' => '0.73,0.78' ),
				);
				foreach ( $qx['heroPhases'] as $qx_i => $qx_phase ) :
					?>
					<p class="qx-hero3d__phase"
						data-hero-in="<?php echo esc_attr( $qx_bands[ $qx_i ]['in'] ); ?>"
						data-hero-out="<?php echo esc_attr( $qx_bands[ $qx_i ]['out'] ); ?>">
						<span class="qx-hero3d__phase-n"><?php echo esc_html( $qx_phase['n'] ); ?></span>
						<?php echo esc_html( $qx_phase['label'] ); ?>
					</p>
				<?php endforeach; ?>
			</div>

			<div class="qx-hero3d__metrics" data-hero-in="0.8,0.93">
				<?php foreach ( $qx['stats'] as $qx_s ) : ?>
					<div>
						<span dir="ltr" class="qx-hero3d__n<?php echo empty( $qx_s['accent'] ) ? '' : ' is-accent'; ?>">
							<?php echo esc_html( $qx_s['n'] ); ?>
						</span>
						<span class="qx-hero3d__cap"><?php echo esc_html( $qx_s['cap'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="qx-hero3d__cue" data-hero-in="-0.05,0.005" data-hero-out="0.02,0.06" aria-hidden="true">
			<span class="qx-hero3d__cue-word"><?php echo esc_html( $qx_ar ? 'مرّر' : 'SCROLL' ); ?></span>
			<span class="qx-hero3d__cue-line"></span>
		</div>
	</div>
</section>
