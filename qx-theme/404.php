<?php
/**
 * 404. Still the brand talking — and it offers the two things a lost visitor wants:
 * the way home, and a way to reach a human.
 *
 * @package QX
 */

declare( strict_types = 1 );

get_header();
?>

<section class="qx-section qx-section--lg" style="min-block-size:70vh;display:flex;align-items:center;">
	<div class="qx-shell qx-shell--narrow">
		<p class="qx-label">404</p>
		<h1 class="qx-display" style="margin-block:28px 32px;font-size:clamp(38px,5vw,72px);">
			<?php echo esc_html( qx_t( 'هذه الصفحة غير موجودة.', 'This page doesn’t exist.' ) ); ?>
		</h1>
		<p class="qx-body" style="max-inline-size:560px;">
			<?php
			echo esc_html(
				qx_t(
					'الرابط الذي وصلت منه لم يعد موجودًا. الطريق إلى الصفحة الرئيسية من هنا.',
					'The link you followed no longer exists. Home is this way.'
				)
			);
			?>
		</p>
		<div style="display:flex;flex-wrap:wrap;gap:32px;align-items:center;margin-block-start:44px;">
			<a class="qx-btn" href="<?php echo esc_url( home_url( qx_is_ar() ? '/' : '/en/' ) ); ?>">
				<?php echo esc_html( qx_t( 'الصفحة الرئيسية', 'Home' ) ); ?>
			</a>
			<a class="qx-tlink" href="<?php echo esc_url( qx_whatsapp_url( '404' ) ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( qx_t( 'تواصل على واتساب', 'Message us on WhatsApp' ) ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
