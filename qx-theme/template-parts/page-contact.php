<?php
/**
 * The Contact page, built to the design handoff (Contact.dc.html).
 *
 * Hero headline, then two columns: intro copy plus the WhatsApp / email / location / hours
 * list, against the form.
 *
 * The form's `action` is a real mailto so a lead is never lost when JavaScript fails; qx.js
 * intercepts the submit and opens wa.me with the answers pre-filled, which is the behaviour
 * the handoff specifies.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx     = qx_content();
$c      = $qx['contact'];
$f      = $c['fields'];
$lbl    = 'qx-label' . ( qx_is_ar() ? ' qx-label--ar' : '' );
?>

<section class="qx-phero">
	<?php echo qx_watermark( 'qx-watermark--page', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

	<div class="qx-phero__body" style="max-inline-size:var(--qx-max);">
		<p class="<?php echo esc_attr( $lbl ); ?> qx-gap-label"><?php echo esc_html( $c['label'] ); ?></p>
		<h1 class="qx-display qx-display--sm qx-gap-head qx-reveal"><?php echo esc_html( $c['title'] ); ?></h1>

		<div class="qx-contact">
			<div class="qx-reveal">
				<p class="qx-body" style="max-inline-size:420px;"><?php echo esc_html( $c['body'] ); ?></p>

				<div class="qx-detail">
					<div class="qx-detail__row">
						<p class="qx-label"><?php echo esc_html( $c['waLabel'] ); ?></p>
						<a href="<?php echo esc_url( qx_whatsapp_url( 'contact' ) ); ?>" target="_blank" rel="noopener">
							<span dir="ltr"><?php echo esc_html( $qx['footer']['phone'] ); ?></span>
						</a>
					</div>
					<div class="qx-detail__row">
						<p class="qx-label"><?php echo esc_html( $c['emailLabel'] ); ?></p>
						<a href="mailto:<?php echo esc_attr( QX_EMAIL ); ?>">
							<span dir="ltr"><?php echo esc_html( QX_EMAIL ); ?></span>
						</a>
					</div>
					<div class="qx-detail__row">
						<p class="<?php echo esc_attr( $lbl ); ?>"><?php echo esc_html( $c['locLabel'] ); ?></p>
						<p><?php echo esc_html( $c['location'] ); ?></p>
					</div>
					<div class="qx-detail__row">
						<p class="<?php echo esc_attr( $lbl ); ?>"><?php echo esc_html( $c['hoursLabel'] ); ?></p>
						<p><?php echo wp_kses_post( qx_ltr_nums( $c['hours'] ) ); ?></p>
					</div>
				</div>
			</div>

			<form class="qx-form qx-reveal" data-qx-waform data-phone="<?php echo esc_attr( QX_WHATSAPP ); ?>"
				method="post" action="mailto:<?php echo esc_attr( QX_EMAIL ); ?>" enctype="text/plain">
				<div class="qx-field">
					<label for="qx-name"><?php echo esc_html( $f['name'] ); ?></label>
					<input id="qx-name" name="<?php echo esc_attr( $f['name'] ); ?>" type="text" required>
				</div>
				<div class="qx-field">
					<label for="qx-phone"><?php echo esc_html( $f['phone'] ); ?></label>
					<input id="qx-phone" name="<?php echo esc_attr( $f['phone'] ); ?>" type="tel" dir="ltr" inputmode="tel">
				</div>
				<div class="qx-field">
					<label for="qx-topic"><?php echo esc_html( $f['topic'] ); ?></label>
					<select id="qx-topic" name="<?php echo esc_attr( $f['topic'] ); ?>">
						<?php foreach ( $c['topics'] as $qx_t ) : ?>
							<option value="<?php echo esc_attr( $qx_t ); ?>"><?php echo esc_html( $qx_t ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="qx-field">
					<label for="qx-msg"><?php echo esc_html( $f['message'] ); ?></label>
					<textarea id="qx-msg" name="<?php echo esc_attr( $f['message'] ); ?>" rows="4"></textarea>
				</div>
				<button class="qx-btn" type="submit"><?php echo esc_html( $f['submit'] ); ?></button>
			</form>
		</div>
	</div>
</section>
