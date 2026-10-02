<?php
/**
 * QX Media — theme bootstrap.
 *
 * Design goals that drive every decision in this file:
 *   1. Zero external requests. No Google Fonts CDN, no icon CDN, no analytics by default.
 *      Fonts are self-hosted (see /assets/fonts) — this also keeps the site clean under
 *      Saudi PDPL, since a Google Fonts request leaks every visitor's IP.
 *   2. Only the CSS a page actually uses is sent.
 *   3. The LCP element is always text, never an image.
 *
 * @package QX
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QX_VERSION', '2.3.0' );

/** All homepage copy, both languages. Edit that file to change any text on the homepage. */
require_once get_template_directory() . '/inc/content.php';

/** Logo and watermark helpers, plus the bare-anchor navigation walker. */
require_once get_template_directory() . '/inc/brand.php';

/**
 * Contact details, in one place. Each is a constant so that one value feeds every page: the
 * footer, the contact page, the mobile menu, the WhatsApp links and the structured data.
 *
 * The values below are placeholders. Set the real ones in wp-config.php, above the line that
 * says "That's all, stop editing", and these defaults are skipped:
 *
 *     define( 'QX_WHATSAPP', '9665XXXXXXXX' );   // digits only, with country code
 *     define( 'QX_EMAIL',    'hello@example.com' );
 *     define( 'QX_CITY_AR',  'الرياض' );
 *     define( 'QX_CITY_EN',  'Riyadh' );
 *
 * Contact details live in configuration, not in the repository, so the code can be public
 * while the business's numbers stay in the business's own wp-config.php.
 */
foreach (
	array(
		'QX_WHATSAPP' => '966500000000',
		'QX_EMAIL'    => 'hello@example.com',
		'QX_CITY_AR'  => 'الرياض',
		'QX_CITY_EN'  => 'Riyadh',
	) as $qx_const => $qx_default
) {
	if ( ! defined( $qx_const ) ) {
		define( $qx_const, $qx_default );
	}
}
unset( $qx_const, $qx_default );

/* -------------------------------------------------------------------------
 * Locale helpers. Polylang serves Arabic as the default language and English
 * under /en/, so the theme must not assume a single direction anywhere.
 * ---------------------------------------------------------------------- */

/**
 * Current site language, reduced to 'ar' or 'en'.
 */
function qx_lang(): string {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( is_string( $lang ) && '' !== $lang ) {
			return str_starts_with( $lang, 'ar' ) ? 'ar' : 'en';
		}
	}
	return str_starts_with( get_locale(), 'ar' ) ? 'ar' : 'en';
}

/**
 * True when the current page is Arabic.
 */
function qx_is_ar(): bool {
	return 'ar' === qx_lang();
}

/**
 * Pick between an Arabic and an English string.
 */
function qx_t( string $ar, string $en ): string {
	return qx_is_ar() ? $ar : $en;
}

/**
 * Escape a string for output, isolating every numeric token as its own LTR run.
 *
 * Needed because figures inside Arabic prose reorder. "تكلفة طلب −44%" is one string mixing
 * an Arabic phrase with a signed number: the minus sign is bidi-neutral, so in an RTL
 * paragraph it resolves to the paragraph direction and lands on the wrong side of the
 * digits. The same class of bug produced "+120" instead of "120+" in the stat row.
 *
 * Wrapping each numeral group in `<span dir="ltr">` isolates it — HTML's UA stylesheet gives
 * any element with a `dir` attribute `unicode-bidi: isolate` — so the sign, the digits and
 * the unit stay together and in order, while the Arabic around them still reads right to
 * left. The copy itself is untouched.
 *
 * Returns escaped HTML, so callers must NOT escape again.
 */
function qx_ltr_nums( string $text ): string {
	/**
	 * An optional leading sign, digits with an optional decimal part, and an optional
	 * trailing unit. U+2212 is the real minus sign the design copy uses.
	 *
	 * No whitespace is allowed inside the token. Permitting it swallowed the space in
	 * "خلال 120 يومًا" into the isolated run, where it rendered on the wrong side and the
	 * words collided.
	 */
	$re  = '/[+\x{2212}\-]?\d+(?:[.,]\d+)?(?:%|x|M|K|★)?/u';
	$out = '';
	$pos = 0;

	if ( preg_match_all( $re, $text, $matches, PREG_OFFSET_CAPTURE ) ) {
		foreach ( $matches[0] as $hit ) {
			[ $token, $offset ] = $hit;
			$out .= esc_html( substr( $text, $pos, $offset - $pos ) );
			$out .= '<span dir="ltr">' . esc_html( $token ) . '</span>';
			$pos  = $offset + strlen( $token );
		}
	}

	return $out . esc_html( substr( $text, $pos ) );
}

/**
 * A WhatsApp deep link carrying context, so the first message already says which page
 * the enquiry came from. The old site sent a bare wa.me link with no context at all.
 */
function qx_whatsapp_url( string $context = '' ): string {
	$text = qx_is_ar()
		? 'السلام عليكم، أرغب في تشخيص مجاني لعلامتي.'
		: 'Hello, I would like a free brand diagnosis.';

	if ( '' !== $context ) {
		$text .= qx_is_ar() ? ' (من: ' . $context . ')' : ' (from: ' . $context . ')';
	}

	return 'https://wa.me/' . QX_WHATSAPP . '?text=' . rawurlencode( $text );
}

/**
 * The three interior pages, keyed by role, with the slugs each already uses in WordPress.
 *
 * These are the live URLs and must not change — the round-2 brief says to keep the existing
 * URL structure intact. The map is used for two things: resolving links from the homepage,
 * and deciding which template a page should render (see qx_page_role()).
 *
 * @return array<string, array{ar: string, en: string}>
 */
function qx_page_map(): array {
	return array(
		'about'    => array( 'ar' => 'من-نحن', 'en' => 'about-us' ),
		'services' => array( 'ar' => 'خدماتنا', 'en' => 'services' ),
		'contact'  => array( 'ar' => 'تواصل-معنا', 'en' => 'contact-us' ),
	);
}

/**
 * The URL of one of the interior pages, in the current language.
 */
function qx_page_url( string $role ): string {
	$map = qx_page_map();
	if ( ! isset( $map[ $role ] ) ) {
		return home_url( '/' );
	}

	return qx_is_ar()
		? home_url( '/' . $map[ $role ]['ar'] . '/' )
		: home_url( '/en/' . $map[ $role ]['en'] . '/' );
}

/**
 * Which of the three designed interior pages, if any, the current page is.
 *
 * Resolution order:
 *   1. An explicitly assigned page template wins, so the client can always override.
 *   2. Otherwise the slug is matched against qx_page_map().
 *
 * Step 2 is what makes the new designs appear without anyone having to open each of the six
 * pages (three roles x two languages) in wp-admin and assign a template by hand.
 */
function qx_page_role(): ?string {
	if ( ! is_page() ) {
		return null;
	}

	$id = get_queried_object_id();

	$assigned = get_page_template_slug( $id );
	if ( is_string( $assigned ) && '' !== $assigned ) {
		foreach ( array_keys( qx_page_map() ) as $role ) {
			if ( 'page-templates/' . $role . '.php' === $assigned ) {
				return $role;
			}
		}
	}

	$post = get_post( $id );
	if ( ! $post ) {
		return null;
	}

	// Slugs arrive percent-decoded from WordPress, but be defensive: the Arabic slugs are
	// stored percent-encoded in some installs.
	$slug = rawurldecode( (string) $post->post_name );

	foreach ( qx_page_map() as $role => $slugs ) {
		if ( $slug === $slugs['ar'] || $slug === $slugs['en'] ) {
			return $role;
		}
	}

	return null;
}

/**
 * Print the four primary nav links as bare anchors.
 *
 * Used twice per page — the desktop bar and the full-screen mobile panel — so both always
 * show the same set. If the client has assigned a WordPress menu to the `primary` location
 * it wins; otherwise the handoff's own four links from inc/content.php are used.
 */
function qx_nav_links(): void {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'depth'          => 1,
				'fallback_cb'    => false,
				'walker'         => new QX_Bare_Link_Walker(),
			)
		);
		return;
	}

	foreach ( qx_content()['nav']['menu'] as $text => $href ) {
		printf(
			'<a href="%s">%s</a>',
			esc_url( home_url( $href ) ),
			esc_html( $text )
		);
	}
}

/* -------------------------------------------------------------------------
 * Theme setup
 * ---------------------------------------------------------------------- */

add_action(
	'after_setup_theme',
	static function (): void {
		load_theme_textdomain( 'qx', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );

		// The editor should look like the front end, or the client edits blind.
		add_editor_style( array( 'style.css', 'assets/css/editor.css' ) );

		// Wide/full alignments come from theme.json layout; no extra image sizes needed
		// beyond a 16:9 card for the journal.
		add_image_size( 'qx-card', 880, 495, true );

		register_nav_menus(
			array(
				'primary' => __( 'Primary navigation', 'qx' ),
				'footer'  => __( 'Footer navigation', 'qx' ),
			)
		);
	}
);

/**
 * Load only the CSS for blocks actually present on the page, instead of the whole
 * block library on every request.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		// Fonts first: the @font-face rules must be parsed before any rule that
		// references the families, or the first paint uses a fallback unnecessarily.
		wp_enqueue_style(
			'qx-fonts',
			get_template_directory_uri() . '/assets/css/fonts.css',
			array(),
			QX_VERSION
		);

		wp_enqueue_style(
			'qx-style',
			get_stylesheet_uri(),
			array( 'qx-fonts' ),
			QX_VERSION
		);

		wp_enqueue_style(
			'qx-components',
			get_template_directory_uri() . '/assets/css/components.css',
			array( 'qx-style' ),
			QX_VERSION
		);

		// ~1 KB: the mobile menu, the header's scrolled state and its dark state over
		// the hero. Every scroll reveal is CSS with no JavaScript at all.
		wp_enqueue_script(
			'qx-ui',
			get_template_directory_uri() . '/assets/js/qx.js',
			array(),
			QX_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		/**
		 * The 3D hero: front page only, and nowhere else on the site.
		 *
		 * Three.js is 167 KB gzipped, so it is never sent to a visitor reading an article or
		 * a service page. Even on the homepage the boot script only reaches for it after the
		 * load event, and only once it has confirmed the browser actually has WebGL.
		 */
		if ( is_front_page() ) {
			wp_enqueue_style(
				'qx-hero',
				get_template_directory_uri() . '/assets/css/hero.css',
				array( 'qx-components' ),
				QX_VERSION
			);

			wp_enqueue_script(
				'qx-hero',
				get_template_directory_uri() . '/assets/js/hero/boot.js',
				array(),
				QX_VERSION,
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);

/**
 * Keep LiteSpeed's JS optimiser away from the hero.
 *
 * Combining or deferring these differently breaks them: `boot.js` must stay a standalone
 * classic script for its dynamic import to resolve, and `engine.js`, `modules.js` and Three
 * are ES modules that cannot survive being concatenated into a bundle. LiteSpeed reads this
 * filter, so the exclusion travels with the theme instead of living in a plugin setting the
 * next person to touch the site would not know about.
 *
 * @param array<int, string> $list Excluded handles/paths.
 * @return array<int, string>
 */
add_filter(
	'litespeed_optimize_js_excludes',
	static function ( $list ): array {
		$list   = is_array( $list ) ? $list : array();
		$list[] = 'assets/js/hero/';
		return $list;
	}
);

add_filter(
	'litespeed_optm_js_defer_exc',
	static function ( $list ): array {
		$list   = is_array( $list ) ? $list : array();
		$list[] = 'assets/js/hero/';
		return $list;
	}
);

/**
 * Preload only the one font file that renders the largest text above the fold —
 * the display face for the current script. Preloading more than one font is how
 * sites accidentally delay their own LCP.
 */
add_action(
	'wp_head',
	static function (): void {
		$dir = get_template_directory_uri() . '/assets/fonts/';

		/**
		 * Preload only the face that sets the largest text above the fold. On Arabic that
		 * is Camel Light (the body weight, also the largest run of text); on English it is
		 * Prole, which is the entire Latin family. Preloading more than one font is how
		 * sites delay their own LCP.
		 *
		 * The Camel display weight (800) is NOT preloaded: it renders the hero headline,
		 * but font-display:swap paints it in the fallback first and the metric difference
		 * is small enough that preloading a second 64 KB file costs more than it saves.
		 */
		$file = qx_is_ar() ? 'camel-light.woff2' : 'prole-derose.woff2';

		// The brand fonts are not distributed with the theme. Preloading a file that is not
		// there is a guaranteed 404 on every page view, so only preload what exists.
		if ( is_readable( get_template_directory() . '/assets/fonts/' . $file ) ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">' . "\n",
				esc_url( $dir . $file )
			);
		}

		// The page ground is cream, so paint it before CSS arrives and there is no flash.
		echo '<meta name="theme-color" content="#F6F0E2">' . "\n";

		$img = get_template_directory_uri() . '/assets/img/';

		/**
		 * Favicon, generated from the new wide mark by scripts/make-icons.py.
		 *
		 * Only emitted when the client has NOT set a Site Icon in Settings > General —
		 * otherwise WordPress emits its own and there would be two competing sets.
		 * The mark sits on cream rather than transparency: its gradient runs black to
		 * burgundy and would all but vanish against a dark browser tab bar.
		 */
		if ( ! has_site_icon() && is_readable( get_template_directory() . '/assets/img/favicon-192.png' ) ) {
			printf(
				'<link rel="icon" href="%s" sizes="32x32">' . "\n"
				. '<link rel="icon" href="%s" sizes="192x192">' . "\n"
				. '<link rel="apple-touch-icon" href="%s">' . "\n",
				esc_url( $img . 'favicon-32.png' ),
				esc_url( $img . 'favicon-192.png' ),
				esc_url( $img . 'favicon-512.png' )
			);
		}

		/**
		 * Social share card, also generated from the new mark.
		 *
		 * Skipped when Rank Math or Yoast is active: both output og:image themselves and a
		 * second tag makes the crawler's choice arbitrary.
		 */
		if ( ! defined( 'RANK_MATH_VERSION' ) && ! defined( 'WPSEO_VERSION' ) && is_readable( get_template_directory() . '/assets/img/og-image.png' ) ) {
			printf(
				'<meta property="og:image" content="%s">' . "\n"
				. '<meta property="og:image:width" content="1200">' . "\n"
				. '<meta property="og:image:height" content="630">' . "\n"
				. '<meta name="twitter:card" content="summary_large_image">' . "\n",
				esc_url( $img . 'og-image.png' )
			);
		}
	},
	1
);

/* -------------------------------------------------------------------------
 * Remove what WordPress ships that this site does not use.
 * Every item below is a request or bytes the old site was paying for.
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	static function (): void {
		// Emoji: two files and a DNS lookup, for a feature a luxury brand never uses.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// oEmbed discovery and the embed script: unused, and they expose endpoints.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );

		// Version disclosure.
		remove_action( 'wp_head', 'wp_generator' );

		// RSD / WLW: dead protocols.
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );

		// Adjacent-post prefetch links: noise in <head>, no benefit here.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_dequeue_script( 'wp-embed' );

		// `classic-theme-styles` only exists to prop up pre-block themes.
		wp_dequeue_style( 'classic-theme-styles' );

		// The Dashicons font is 45 KB and is only needed in the admin bar.
		if ( ! is_admin_bar_showing() ) {
			wp_deregister_style( 'dashicons' );
		}
	},
	100
);

/**
 * Strip the inline SVG duotone/filter block WordPress injects into every page.
 * The theme declares no duotone presets, so it is always empty markup.
 */
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles_css_custom_properties' );

/**
 * Disable XML-RPC. It is an attack surface with no use on this site.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * The site has no comments and should not advertise any.
 */
add_action(
	'init',
	static function (): void {
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'post', 'comments' );
	}
);

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/* -------------------------------------------------------------------------
 * Block patterns — the twelve homepage sections and the interior blocks.
 * WordPress auto-registers every PHP file in /patterns/, so this only has to
 * declare the categories they sort into.
 * ---------------------------------------------------------------------- */

add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'qx-home',
			array( 'label' => __( 'QX: Homepage sections', 'qx' ) )
		);
		register_block_pattern_category(
			'qx-page',
			array( 'label' => __( 'QX: Page sections', 'qx' ) )
		);

		// Core pattern categories add hundreds of irrelevant patterns to the inserter,
		// which is how a client accidentally pastes a stock-photo hero into the site.
		remove_theme_support( 'core-block-patterns' );
	}
);

/**
 * Also disable the remote pattern directory, for the same reason.
 */
add_filter( 'should_load_remote_block_patterns', '__return_false' );

/* -------------------------------------------------------------------------
 * Output polish
 * ---------------------------------------------------------------------- */

/**
 * Strip Polylang's language-switcher items out of the theme's own menus.
 *
 * The header already renders a language switcher of its own, positioned and styled as part
 * of the design. Kadence used the location name `primary` too, so the client's existing menu
 * is inherited automatically — which is what we want — but that menu also carries Polylang's
 * injected "English" item, and the result was the switcher appearing twice side by side.
 *
 * Filtering it out here keeps one switcher, in the right place, without asking the client to
 * go and edit their menu.
 */
add_filter(
	'wp_nav_menu_objects',
	static function ( array $items ): array {
		return array_values(
			array_filter(
				$items,
				static function ( $item ): bool {
					$classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : array();
					foreach ( array( 'lang-item', 'pll-parent-menu-item' ) as $marker ) {
						foreach ( $classes as $class ) {
							if ( is_string( $class ) && str_contains( $class, $marker ) ) {
								return false;
							}
						}
					}
					return true;
				}
			)
		);
	}
);

/**
 * Give the <body> a class naming the current script, so CSS can branch on it
 * without relying on the locale string.
 */
add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'qx-lang-' . qx_lang();
		if ( is_rtl() ) {
			$classes[] = 'qx-rtl';
		}
		return $classes;
	}
);

/**
 * WordPress emits `dir="rtl"` from the locale. Polylang can disagree with the
 * locale on a per-page basis, so force the attribute to follow the page language.
 */
add_filter(
	'language_attributes',
	static function ( string $output ): string {
		$dir = qx_is_ar() ? 'rtl' : 'ltr';
		/**
		 * `(^|\s)` matters. WordPress returns `dir="rtl" lang="ar"` with dir FIRST, so a
		 * pattern requiring leading whitespace never matched it — and appending our own
		 * produced `<html dir="rtl" lang="ar" dir="rtl">`, a duplicate attribute. Browsers
		 * take the first and carry on, so it renders fine and is invalid all the same.
		 */
		$output = preg_replace( '/(^|\s)dir="(rtl|ltr)"/', '', $output );
		return trim( (string) $output ) . ' dir="' . $dir . '"';
	}
);

/**
 * Trim the excerpt to something that reads as a standfirst rather than a truncation.
 */
add_filter( 'excerpt_length', static fn (): int => qx_is_ar() ? 28 : 34, 999 );
add_filter( 'excerpt_more', static fn (): string => '…' );

/**
 * Structured data. `ProfessionalService` is the correct type for a marketing agency,
 * and it is emitted per-locale so Arabic and English are each described in their own
 * language rather than one leaking into the other — which is what the old site did.
 */
add_action(
	'wp_head',
	static function (): void {
		if ( ! is_front_page() ) {
			return;
		}

		$data = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'ProfessionalService',
			'name'      => get_bloginfo( 'name' ),
			'slogan'    => get_bloginfo( 'description' ),
			'url'       => home_url( '/' ),
			'email'     => QX_EMAIL,
			'telephone' => '+' . QX_WHATSAPP,
			'inLanguage' => qx_is_ar() ? 'ar-SA' : 'en',
			'areaServed' => array(
				array( '@type' => 'Country', 'name' => qx_t( 'السعودية', 'Saudi Arabia' ) ),
			),
			'address'   => array(
				'@type'           => 'PostalAddress',
				'addressCountry'  => 'SA',
				'addressLocality' => qx_t( QX_CITY_AR, QX_CITY_EN ),
			),
			'knowsLanguage' => array( 'ar', 'en' ),
		);

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		);
	},
	20
);

