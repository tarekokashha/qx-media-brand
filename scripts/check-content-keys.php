<?php
/**
 * Verifies that every content key the templates read actually exists in qx_content(),
 * for BOTH languages.
 *
 * `php -l` only proves a file parses. The failure mode that actually takes a WordPress
 * page down is reading an array key that isn't there — under PHP 8 that's a warning plus
 * a null, and in a heading it renders as a silently empty section. This harness stubs the
 * handful of WordPress functions inc/content.php touches, builds the tree in Arabic and
 * English, then greps the templates for `$qx['a']['b']` accesses and asserts each resolves.
 *
 *   php scripts/check-content-keys.php
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ );
define( 'QX_WHATSAPP', '966500000000' );
define( 'QX_EMAIL', 'test@example.com' );
define( 'QX_CITY_AR', 'الرياض' );
define( 'QX_CITY_EN', 'Riyadh' );

$GLOBALS['qx_test_lang'] = 'ar';

function qx_is_ar(): bool {
	return 'ar' === $GLOBALS['qx_test_lang'];
}

function qx_t( string $ar, string $en ): string {
	return qx_is_ar() ? $ar : $en;
}

function get_bloginfo( string $show = '' ): string {
	return 'Demo Agency';
}

require __DIR__ . '/../qx-theme/inc/content.php';

$theme_dir = __DIR__ . '/../qx-theme';
$templates = array(
	'front-page.php',
	'footer.php',
	'index.php',
	'header.php',
	'template-parts/page-about.php',
	'template-parts/page-services.php',
	'template-parts/page-contact.php',
	'template-parts/hero-3d.php',
);

$errors = 0;
$checked = 0;

foreach ( array( 'ar', 'en' ) as $lang ) {
	$GLOBALS['qx_test_lang'] = $lang;
	$qx = qx_content();

	foreach ( $templates as $tpl ) {
		$path = $theme_dir . '/' . $tpl;
		if ( ! is_file( $path ) ) {
			continue;
		}
		$src = (string) file_get_contents( $path );

		// Match $qx['section'] and $qx['section']['key'] accesses.
		preg_match_all( "/\\\$qx\\['([a-z_]+)'\\](?:\\['([a-z_0-9]+)'\\])?/", $src, $m, PREG_SET_ORDER );

		foreach ( $m as $hit ) {
			$section = $hit[1];
			$key     = $hit[2] ?? null;
			$checked++;

			if ( ! array_key_exists( $section, $qx ) ) {
				printf( "  MISSING [%s] %s: \$qx['%s']\n", $lang, $tpl, $section );
				$errors++;
				continue;
			}
			if ( null !== $key && '' !== $key ) {
				if ( ! is_array( $qx[ $section ] ) || ! array_key_exists( $key, $qx[ $section ] ) ) {
					printf( "  MISSING [%s] %s: \$qx['%s']['%s']\n", $lang, $tpl, $section, $key );
					$errors++;
				}
			}
		}
	}
}

// Structural expectations the design depends on.
foreach ( array( 'ar', 'en' ) as $lang ) {
	$GLOBALS['qx_test_lang'] = $lang;
	$qx = qx_content();

	/**
	 * Counts the design handoff fixes. If one of these changes, either the design changed
	 * or something was lost in an edit — both worth failing the build over.
	 */
	$expect = array(
		'hero.lines'              => 2,
		'stats'                   => 4,
		'about.lines'             => 2,
		'services.lines'          => 2,
		'services.items'          => 6,
		'cases.items'             => 3,
		'clients.names'           => 5,
		'process.lines'           => 2,
		'process.steps'           => 3,
		'testimonial.badges'      => 4,
		'contact.topics'          => 7,
		// The 3D hero cross-fades exactly three stage labels; the template pairs each with
		// a hard-coded progress band, so a fourth would silently never appear.
		'heroPhases'              => 3,
		// Round-2 interior pages.
		'aboutPage.lines'         => 2,
		'aboutPage.story.paras'   => 2,
		'aboutPage.story.lines'   => 2,
		'aboutPage.team.lines'    => 2,
		'aboutPage.values.items'  => 4,
		'aboutPage.stats'         => 4,
		'servicesPage.lines'      => 2,
		'servicesPage.items'      => 6,
	);

	foreach ( $expect as $dotted => $count ) {
		// Walk the dotted path to any depth: `stats` is a top-level list, while
		// `aboutPage.story.paras` is three levels down.
		$node = $qx;
		foreach ( explode( '.', $dotted ) as $segment ) {
			$node = is_array( $node ) && array_key_exists( $segment, $node ) ? $node[ $segment ] : null;
		}

		$actual = is_array( $node ) ? count( $node ) : -1;
		if ( $actual !== $count ) {
			printf( "  COUNT   [%s] %s expected %d, got %d\n", $lang, $dotted, $count, $actual );
			$errors++;
		}
	}

	// Every service row on the Services page needs a name, a paragraph and four sub-items.
	foreach ( $qx['servicesPage']['items'] as $i => $svc ) {
		foreach ( array( 'n', 'name', 'body' ) as $field ) {
			if ( empty( $svc[ $field ] ) ) {
				printf( "  SRVPAGE [%s] item %d missing '%s'\n", $lang, $i, $field );
				$errors++;
			}
		}
		if ( ! isset( $svc['points'] ) || 4 !== count( $svc['points'] ) ) {
			printf( "  SRVPAGE [%s] item %d expected 4 sub-items\n", $lang, $i );
			$errors++;
		}
	}

	// Every service must carry a link, or the homepage renders dead rows.
	foreach ( $qx['services']['items'] as $i => $svc ) {
		foreach ( array( 'n', 'name', 'meta', 'href' ) as $field ) {
			if ( empty( $svc[ $field ] ) ) {
				printf( "  SERVICE [%s] item %d missing '%s'\n", $lang, $i, $field );
				$errors++;
			}
		}
	}

	/**
	 * Arabic and English must not leak into each other: the bug the previous site had, with
	 * an Arabic address sitting in its English footer.
	 *
	 * One exception is allowed, matched by exact VALUE rather than by a loose pattern:
	 * WordPress genuinely serves the English SEO service page at a slug that contains
	 * Arabic. Linking to the real URL beats linking to a tidy 404. QX should rename that
	 * slug in wp-admin (Rank Math will issue the redirect), after which this entry can be
	 * deleted and the gate becomes absolute.
	 */
	$allowed_arabic_in_en = array(
		'/en/search-engine-optimization-seo-تحسين-محركات-البحث/',
	);

	$leaks = array();
	$walk  = static function ( array $node, string $path ) use ( &$walk, &$leaks ): void {
		foreach ( $node as $k => $v ) {
			$p = $path . '[' . $k . ']';
			if ( is_array( $v ) ) {
				$walk( $v, $p );
				continue;
			}
			if ( is_string( $v ) && preg_match( '/[\x{0600}-\x{06FF}]/u', $v ) ) {
				$leaks[ $p ] = $v;
			}
		}
	};
	$walk( $qx, '' );

	if ( 'en' === $lang ) {
		foreach ( $leaks as $p => $v ) {
			if ( in_array( $v, $allowed_arabic_in_en, true ) ) {
				continue;
			}
			printf( "  LEAK    [en] Arabic text at %s: %s\n", $p, $v );
			$errors++;
		}
	}

	if ( 'ar' === $lang && empty( $leaks ) ) {
		printf( "  LEAK    [ar] the Arabic content tree contains no Arabic at all\n" );
		$errors++;
	}

	/**
	 * NO DASHES. The round-2 brief: every em dash (—) and en dash (–) is out of the copy,
	 * because a dash joining two clauses is the single strongest tell that a sentence was
	 * machine-written. Clauses take a comma, a colon or a full stop instead.
	 *
	 * This is a gate rather than a one-off sweep because it is exactly the kind of thing a
	 * later copy edit reintroduces without anyone noticing.
	 *
	 * U+2212 MINUS SIGN is deliberately NOT matched: "CAC −38%" is arithmetic, not
	 * punctuation. Nor is the ASCII hyphen, which belongs inside compound words.
	 */
	$dashes = array();
	$walk_d = static function ( array $node, string $path ) use ( &$walk_d, &$dashes ): void {
		foreach ( $node as $k => $v ) {
			$p = $path . '[' . $k . ']';
			if ( is_array( $v ) ) {
				$walk_d( $v, $p );
				continue;
			}
			if ( is_string( $v ) && preg_match( '/[\x{2013}\x{2014}]/u', $v ) ) {
				$dashes[ $p ] = $v;
			}
		}
	};
	$walk_d( $qx, '' );

	foreach ( $dashes as $p => $v ) {
		printf( "  DASH    [%s] em/en dash in copy at %s: %s\n", $lang, $p, $v );
		$errors++;
	}
}

printf( "\n%d key accesses checked across both languages.\n", $checked );
if ( 0 === $errors ) {
	echo "ALL CONTENT KEYS RESOLVE\n";
	exit( 0 );
}
printf( "%d PROBLEM(S) FOUND\n", $errors );
exit( 1 );
