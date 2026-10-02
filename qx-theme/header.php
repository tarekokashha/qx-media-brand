<?php
/**
 * Site header — the fixed nav from the design handoff (Nav.dc.html).
 *
 * 80px tall, transparent over the hero, crossfading to solid cream after ~40px of scroll.
 * The wide QX mark sits at the inline start at 30px tall, four links centre, then the
 * language toggle and the consultation button.
 *
 * Below 920px the links and the button are hidden and a 44x44 hamburger opens the
 * full-screen menu panel: mark top-start, close top-end, four 30px links on hairlines,
 * and the solid consultation button plus the email pinned to the bottom.
 *
 * @package QX
 */

declare( strict_types = 1 );

$qx     = qx_content();
$qx_nav = $qx['nav'];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="qx-skip-link" href="#qx-main"><?php echo esc_html( qx_t( 'تجاوز إلى المحتوى', 'Skip to content' ) ); ?></a>

<header class="qx-nav" id="qx-nav" data-qx-nav>
	<div class="qx-nav__inner">
		<a class="qx-nav__logo" href="<?php echo esc_url( home_url( qx_is_ar() ? '/' : '/en/' ) ); ?>" rel="home"
			aria-label="<?php echo esc_attr( qx_t( 'الصفحة الرئيسية', 'Home' ) ); ?>">
			<?php
			/* Two marks, one shown at a time by CSS. The homepage hero is near-black, so
			   the gradient mark disappears against it and the cream one takes over while
			   the nav is in its dark state. Swapping via CSS rather than JS means no
			   flash and no second request at the moment of the swap. */
			?>
			<?php echo qx_mark( 'logo-mark-nav.png', 'qx-nav__mark', 'QX', 56, 30 ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside qx_mark(). ?>
			<?php echo qx_mark( 'logo-mark-cream-nav.png', 'qx-nav__mark qx-nav__mark--cream', '', 56, 30, true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>

		<nav class="qx-nav__links" aria-label="<?php echo esc_attr( qx_t( 'التنقل الرئيسي', 'Primary navigation' ) ); ?>">
			<?php qx_nav_links(); ?>
		</nav>

		<div class="qx-nav__end">
			<?php
			// The toggle always appears in the language it switches TO, set in that
			// language's own typeface — per the handoff.
			if ( function_exists( 'pll_the_languages' ) ) {
				$qx_langs = pll_the_languages(
					array( 'display_names_as' => 'slug', 'hide_current' => true, 'raw' => true )
				);
				if ( is_array( $qx_langs ) ) {
					foreach ( $qx_langs as $qx_l ) {
						printf(
							'<a class="qx-lang" href="%s" lang="%s" hreflang="%s">%s</a>',
							esc_url( $qx_l['url'] ),
							esc_attr( $qx_l['slug'] ),
							esc_attr( $qx_l['slug'] ),
							esc_html( 'ar' === $qx_l['slug'] ? 'عربي' : 'EN' )
						);
					}
				}
			} else {
				printf(
					'<a class="qx-lang" href="%s" lang="%s" hreflang="%s">%s</a>',
					esc_url( home_url( qx_is_ar() ? '/en/' : '/' ) ),
					esc_attr( qx_is_ar() ? 'en' : 'ar' ),
					esc_attr( qx_is_ar() ? 'en' : 'ar' ),
					esc_html( qx_is_ar() ? 'EN' : 'عربي' )
				);
			}
			?>

			<a class="qx-btn qx-btn--ink qx-nav__cta" href="<?php echo esc_url( qx_whatsapp_url( 'nav' ) ); ?>"
				target="_blank" rel="noopener"><?php echo esc_html( $qx_nav['cta'] ); ?></a>

			<button class="qx-burger" type="button" data-qx-burger aria-expanded="false" aria-controls="qx-panel">
				<span class="qx-sr-only"><?php echo esc_html( qx_t( 'القائمة', 'Menu' ) ); ?></span>
				<span class="qx-burger__bar" aria-hidden="true"></span>
				<span class="qx-burger__bar" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>

<?php /* Full-screen mobile menu panel (handoff: Nav.dc.html, the openAr/openEn branch). */ ?>
<div class="qx-panel" id="qx-panel" data-qx-panel hidden>
	<div class="qx-panel__top">
		<?php echo qx_mark( 'logo-mark-nav.png', 'qx-panel__logo', 'QX', 52, 28 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<button class="qx-panel__close" type="button" data-qx-panel-close
			aria-label="<?php echo esc_attr( qx_t( 'إغلاق', 'Close' ) ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>

	<nav class="qx-panel__nav" aria-label="<?php echo esc_attr( qx_t( 'قائمة الجوال', 'Mobile menu' ) ); ?>">
		<?php qx_nav_links(); ?>
	</nav>

	<div class="qx-panel__foot">
		<a class="qx-btn qx-btn--block" href="<?php echo esc_url( qx_whatsapp_url( 'menu' ) ); ?>"
			target="_blank" rel="noopener"><?php echo esc_html( $qx['hero']['cta'] ); ?></a>
		<a class="qx-panel__mail" href="mailto:<?php echo esc_attr( QX_EMAIL ); ?>">
			<span dir="ltr"><?php echo esc_html( QX_EMAIL ); ?></span>
		</a>
	</div>
</div>

<main id="qx-main">
