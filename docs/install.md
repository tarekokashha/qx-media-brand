# Install and make it yours

## Requirements

| | |
|---|---|
| WordPress | 6.6 or newer |
| PHP | 8.0 or newer |
| Polylang | Optional. Needed for two languages (Arabic default, English under `/en/`). Without it the theme still works in one |
| Elementor | Optional. Only if you have pages built with it; the theme steps aside for them |

## Try it locally first (recommended)

The Docker stack exists so the theme is never activated on a live, ranking site without having been seen working first.

```bash
git clone https://github.com/tarekokashha/qx-media-brand.git
cd qx-media-brand
docker compose up -d
./scripts/wp-setup.sh          # Git Bash, WSL, macOS or Linux
```

Then open <http://localhost:8088>. The setup script installs WordPress, activates the theme, and creates the six interior pages (three roles, two languages) so the designed layouts appear. The admin login is `admin` / `admin`. Everything is bound to `127.0.0.1` and meant to be thrown away.

Edits to `qx-theme/` show on refresh, because the folder is mounted into the container.

## Install on a real site

**Back up first.** If the site ranks for real keywords it is not a test site. Take a full backup (database and files) with whatever you use, and wait for it to finish.

1. Zip the theme folder so the zip contains `qx-theme/` at its root:

   ```bash
   zip -r qx-theme.zip qx-theme -x '*.gitkeep'
   ```

2. **Appearance → Themes → Add New → Upload Theme**, choose the zip, **Install Now**. If WordPress says the theme already exists, choose *Replace installed with uploaded* and check that the version table says you are going **up**, not down.
3. **Activate.**
4. **Purge every cache** (page cache, CDN, LiteSpeed or similar). A theme update does not always purge, and you will be served the old CSS and conclude nothing happened.
5. Check both languages, `/` and `/en/`.

## Configure it

Set your contact details in `wp-config.php`, above the line that says *That's all, stop editing*:

```php
define( 'QX_WHATSAPP', '9665XXXXXXXX' );   // digits only, with country code
define( 'QX_EMAIL',    'hello@your-domain.example' );
define( 'QX_CITY_AR',  'الرياض' );
define( 'QX_CITY_EN',  'Riyadh' );

// Optional legal identifiers, shown in the footer when set
define( 'QX_CR_NUMBER',  '...' );
define( 'QX_VAT_NUMBER', '...' );
```

Then replace the demo copy. **All text is in `qx-theme/inc/content.php`**, Arabic first and English second. Search it for `QX_PLACEHOLDER`: every figure, client, quote and credential in the demo is marked, and each needs a verified, sourced value and written permission, or needs deleting.

### Pages the theme takes over

These are matched **by slug**, so creating them is enough:

| Role | Arabic slug | English slug | Layout |
|---|---|---|---|
| About | `من-نحن` | `about-us` | `template-parts/page-about.php` |
| Services | `خدماتنا` | `services` | `template-parts/page-services.php` |
| Contact | `تواصل-معنا` | `contact-us` | `template-parts/page-contact.php` |

If you rename a slug, assign the layout explicitly instead: edit the page, then **Page Attributes → Template → "QX: About"** (or Services, or Contact). An assigned template always wins.

Any other page built with Elementor is left alone. Anything else gets the prose layout.

### Polylang

Set Arabic as the default language and English as the second, with English under `/en/`. The theme draws its own language switcher in the header, and filters Polylang's injected menu items so it does not appear twice.

## Bring your own brand assets

The repository ships no fonts, no logos and no images, because those belong to the brand. The theme works without them (system serifs, a text wordmark) and picks them up automatically when you add files with these names.

### Fonts: `qx-theme/assets/fonts/`

| File | Used for |
|---|---|
| `prole-derose.woff2` | The Latin face (one weight, 400) |
| `camel-light.woff2` | Arabic, weight 300 (body) |
| `camel-regular.woff2` | Arabic, weight 400 |
| `camel-medium.woff2` | Arabic, weight 500 |
| `camel-bold.woff2` | Arabic, weight 700 |
| `camel-extrabold.woff2` | Arabic, weight 800 (display) |

These are the names the `@font-face` rules in `assets/css/fonts.css` expect. To use different typefaces, edit that file and the two families in `style.css` (`--qx-latin`, `--qx-arabic`). Convert and subset to WOFF2, and keep the Arabic shaping tables when subsetting.

### Images: `qx-theme/assets/img/`

| File | Used for |
|---|---|
| `logo-mark-nav.png` | The header and mobile-menu mark, for light backgrounds |
| `logo-mark-cream-nav.png` | The same mark for dark backgrounds (the hero state, and the footer) |
| `logo-mark-terracotta.png` | The large faint watermark behind the call to action and the interior page heroes (859 by 463) |
| `favicon-32.png`, `favicon-192.png`, `favicon-512.png` | Used only if you have not set a Site Icon under Settings → General |
| `og-image.png` | The social share card (1200 by 630); skipped when Rank Math or Yoast is active |

### The hero's mark: `qx-theme/assets/js/hero/qx-shape.json`

The 3D hero extrudes this geometry. The shipped file is a neutral demo mark. See [3d-hero.md](3d-hero.md#using-your-own-mark).

## Verify

- [ ] `/` and `/en/` both load, and the direction is right in each
- [ ] The hero plays through its phases when you scroll, and the copy is readable at the start
- [ ] The three interior pages show the designed layouts in both languages
- [ ] The language switcher appears once
- [ ] The WhatsApp links open with a pre-filled message in the visitor's language
- [ ] With "reduce motion" switched on in your operating system, the hero is a single readable screen
- [ ] Lighthouse accessibility is healthy in both directions

## Reverting

The theme does not delete content. Elementor data for any page the theme took over stays in the database, so reverting a page is a matter of removing its branch in `page.php`, and reverting the whole theme is a matter of switching back to your previous one.
