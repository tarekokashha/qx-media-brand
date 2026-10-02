<?php
/**
 * Site footer — from the design handoff (Footer.dc.html).
 *
 * Charcoal ground, three columns: cream monogram + tagline, menu links, contact details.
 * A hairline rule then the copyright line.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx     = qx_content();
$qxf    = $qx['footer'];

// Optional legal identifiers, set in wp-config.php. Left blank rather than invented: a wrong registration number is worse than none.
$qx_cr  = defined( 'QX_CR_NUMBER' ) ? QX_CR_NUMBER : '';
$qx_vat = defined( 'QX_VAT_NUMBER' ) ? QX_VAT_NUMBER : '';
?>
</main>

<footer class="qx-footer">
	<div class="qx-footer__grid">
		<div>
			<a class="qx-footer__logo" href="<?php echo esc_url( home_url( qx_is_ar() ? '/' : '/en/' ) ); ?>" rel="home"
				aria-label="<?php echo esc_attr( qx_t( 'الصفحة الرئيسية', 'Home' ) ); ?>">
				<?php echo qx_mark( 'logo-mark-cream-nav.png', 'qx-wordmark--cream', 'QX', 74, 40, false, true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<p class="qx-footer__tag"><?php echo esc_html( $qxf['tag'] ); ?></p>
		</div>

		<div class="qx-footer__col">
			<h2 class="qx-label<?php echo qx_is_ar() ? ' qx-label--ar' : ''; ?>"><?php echo esc_html( $qxf['menuLabel'] ); ?></h2>
			<ul class="qx-footer__list">
				<?php
				foreach ( $qx['nav']['menu'] as $qx_text => $qx_href ) {
					printf(
						'<li><a href="%s">%s</a></li>',
						esc_url( home_url( $qx_href ) ),
						esc_html( $qx_text )
					);
				}
				?>
			</ul>
		</div>

		<div class="qx-footer__col">
			<h2 class="qx-label<?php echo qx_is_ar() ? ' qx-label--ar' : ''; ?>"><?php echo esc_html( $qxf['contactLabel'] ); ?></h2>
			<ul class="qx-footer__list">
				<li>
					<a href="mailto:<?php echo esc_attr( QX_EMAIL ); ?>">
						<span dir="ltr"><?php echo esc_html( QX_EMAIL ); ?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( qx_whatsapp_url( 'footer' ) ); ?>" target="_blank" rel="noopener">
						<span dir="ltr"><?php echo esc_html( $qxf['phone'] ); ?></span>
					</a>
				</li>
				<li><?php echo esc_html( $qx['contact']['location'] ); ?></li>
			</ul>
		</div>
	</div>

	<div class="qx-footer__base">
		<p>
			&copy; <span dir="ltr"><?php echo esc_html( (string) gmdate( 'Y' ) ); ?></span>
			<?php echo esc_html( $qxf['name'] ); ?>
		</p>
		<?php if ( '' !== $qx_cr || '' !== $qx_vat ) : ?>
			<p>
				<?php if ( '' !== $qx_cr ) : ?>
					<?php echo esc_html( qx_t( 'سجل تجاري', 'CR' ) ); ?>
					<span dir="ltr"><?php echo esc_html( $qx_cr ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $qx_vat ) : ?>
					· <?php echo esc_html( qx_t( 'الرقم الضريبي', 'VAT' ) ); ?>
					<span dir="ltr"><?php echo esc_html( $qx_vat ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
