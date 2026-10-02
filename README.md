<div align="center">

<img src="docs/img/social-preview.png" alt="QX Media: Arabic-first, bilingual WordPress theme with a scroll-driven 3D hero" width="100%">

# QX Media

**An Arabic-first, bilingual WordPress theme with a scroll-driven 3D hero.**<br>
Designed and built by [Tarek Okasha](https://github.com/tarekokashha) for QX Media, a marketing house in Riyadh.

[![CI](https://github.com/tarekokashha/qx-media-brand/actions/workflows/ci.yml/badge.svg)](https://github.com/tarekokashha/qx-media-brand/actions/workflows/ci.yml)
[![License: GPL v2+](https://img.shields.io/badge/code-GPL--2.0--or--later-blue.svg)](LICENSE)
[![Docs: CC BY 4.0](https://img.shields.io/badge/docs-CC%20BY%204.0-lightgrey.svg)](NOTICE.md)
![WordPress 6.6+](https://img.shields.io/badge/WordPress-6.6%2B-21759b.svg)
![PHP 8.0+](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg)
![RTL first](https://img.shields.io/badge/RTL-first-9c1b39.svg)

[**Live site**](https://qxmedia.sa) · [العربية](README.ar.md) · [Documentation](docs/) · [Install](docs/install.md) · [Portfolio](https://tarek-portfolio-phi.vercel.app)

</div>

---

![The live QX Media homepage, mid-way through its scroll-driven transformation](docs/img/live-hero-en-p48.png)

<sub>The live site at [qxmedia.sa](https://qxmedia.sa), captured on 2 October 2026. The mark transforms as the visitor scrolls.</sub>

![The hero's five-phase transformation, from first load to the final frame](docs/img/hero-filmstrip.png)

## The project

QX Media is a marketing house in Riyadh. I built its web presence: the **bilingual Arabic and English site on a custom WordPress theme**, the **brand system** behind it, and the **tone of voice** that runs through everything it publishes. It is an ongoing partnership, not a hand-off.

The brief was a site that looks like the brand sells, restrained and editorial, not a stock-agency template. It had to be Arabic first, with English alongside; fast; and not dependent on a page builder for the pages the design covers.

This repository is the theme and its tooling, and the write-up of how it works.

## At a glance

| | |
|---|---|
| **Role** | Brand system, design implementation, front-end and theme engineering, tooling: [Tarek Okasha](https://github.com/tarekokashha) |
| **Client** | QX Media, Riyadh, Saudi Arabia |
| **Live** | [qxmedia.sa](https://qxmedia.sa) (theme 2.2.1). This repository is 2.3.0 |
| **Languages** | Arabic by default (right to left), English under `/en/`, through Polylang |
| **Stack** | WordPress 6.6+, PHP 8.0+, vanilla JavaScript, Three.js r169 (tree-shaken) |
| **The hero** | WebGL, five phases, 141 KB gzipped, loaded on the homepage only, after the `load` event |
| **Measured** | Mobile Lighthouse: accessibility **100**, SEO **100**, best practices 96. LCP 1.57 s, CLS 0. See [performance](docs/performance.md) |

## What is in it

### A 3D hero that is optional by construction
A pinned, scroll-driven WebGL scene transforms the brand mark through five phases. Every word a visitor needs is semantic HTML above the canvas, so with no WebGL, no JavaScript or a preference for reduced motion, the hero is a single readable screen. The headline is the largest contentful paint, never an image. The engine is bundled from a tree-shaken Three.js at **141 KB gzipped, down from 167 KB**, and it never reaches a visitor reading an article. → [3D hero](docs/3d-hero.md)

### Arabic engineered, not translated
Logical CSS properties everywhere, enforced by a gate that fails the build on a stray `margin-left`. Figures inside Arabic prose are bidi-isolated, so "−44%" keeps its sign on the correct side. One `dir` attribute, following the page language. No tracking on Arabic letters. → [Arabic and RTL](docs/rtl-and-arabic.md)

### Zero third-party requests from theme code
Fonts are self-hosted and the theme calls no CDN. That is not a promise: `scripts/check-repo.mjs` fails the build if any theme file references a third-party origin. → [Decisions](docs/decisions.md)

### A design system with rules that are checked
Square edges, no shadows, no `backdrop-filter`, a fixed palette. Each rule is something a later edit could quietly break, so each is enforced. → [Design system](docs/design-system.md)

### Pages by slug, content as data
The Arabic and English About, Services and Contact pages take over their layouts by slug, with an explicit template override, and every string on the site lives in one file. A checker proves every key a template reads exists in **both** languages. → [Architecture](docs/architecture.md)

### Tested like software
PHP 8.0 to 8.4 lint, content-key parity in both languages, an RTL and performance gate and a repository gate, all in CI.

## Screens

| Arabic, right to left | Mobile |
|---|---|
| ![Arabic hero on desktop](docs/img/live-hero-ar-p00.png) | ![English hero on mobile](docs/img/live-hero-en-mobile-p48.png) |
| ![Services section, Arabic](docs/img/live-services-ar-1440.png) | ![Process section, English](docs/img/live-process-en-1440.png) |

## Quick start

You need Docker. The stack binds to `127.0.0.1` and uses throwaway credentials.

```bash
git clone https://github.com/tarekokashha/qx-media-brand.git
cd qx-media-brand
docker compose up -d
./scripts/wp-setup.sh      # Git Bash, WSL, macOS or Linux
```

Open <http://localhost:8088>. The script installs WordPress, activates the theme and creates the six interior pages. Edits to `qx-theme/` appear on refresh.

To install on a real site, and to add your own fonts, logo and contact details, read **[docs/install.md](docs/install.md)**.

## Make it yours

Contact details are constants in `wp-config.php`, not code:

```php
define( 'QX_WHATSAPP', '9665XXXXXXXX' );
define( 'QX_EMAIL',    'hello@your-domain.example' );
define( 'QX_CITY_AR',  'الرياض' );
define( 'QX_CITY_EN',  'Riyadh' );
```

All text is in `qx-theme/inc/content.php`, in both languages. It ships with **neutral demo copy**, and every figure, client, quote and credential in it is marked `QX_PLACEHOLDER`.

## Quality gates

```bash
node scripts/check-repo.mjs           # no leaked credentials, personal data, forbidden files, broken links, third-party origins
node scripts/gate-rtl.mjs             # logical properties only; no shadows or backdrop-filter
php  scripts/check-content-keys.php   # every content key a template reads exists, in both languages
npm run build:hero                    # rebuild the 3D engine bundle (needs network and npx)
```

CI runs the PHP lint on 8.0, 8.1, 8.2, 8.3 and 8.4, plus the three checks above.

## Repository map

```text
qx-theme/                 the WordPress theme
  functions.php           bootstrap: constants, language, assets, clean-up, structured data
  inc/content.php         all copy, both languages
  inc/brand.php           logo helpers with a text fallback, and the menu walker
  front-page.php          the homepage (eight sections)
  template-parts/         the 3D hero and the About / Services / Contact layouts
  assets/css/             fonts, components, hero, editor styles
  assets/js/              the site runtime (about 1.5 KB) and the 3D hero
scripts/                  build, gates, WordPress setup
docs/                     architecture, 3D hero, Arabic and RTL, performance, design system, install, decisions
docker-compose.yml        a throwaway WordPress for trying the theme
```

## What is not in this repository, and why

| Not included | Why |
|---|---|
| QX Media's typefaces, logo artwork and photography | They belong to the brand. The theme falls back to system serifs and a text wordmark, and picks your files up by name |
| The traced logo geometry the live hero extrudes | It is the logo. A neutral demo mark ships instead |
| QX Media's real copy, figures and testimonials | Copy belongs to the client, and a business's claims about itself need their own sources |
| Contact numbers and addresses | They live in `wp-config.php` |

## Documentation

| | |
|---|---|
| [Architecture](docs/architecture.md) | Request flow, language handling, assets, plugin interplay |
| [The 3D hero](docs/3d-hero.md) | Timeline, boot sequence, quality tiers, reduced motion, the bundle |
| [Arabic and RTL](docs/rtl-and-arabic.md) | Bidi numerals, logical properties, typography, the `dir` attribute |
| [Performance](docs/performance.md) | What was measured, how, and what it does not say |
| [Design system](docs/design-system.md) | Tokens, rules, components |
| [Install](docs/install.md) | Local demo, real install, brand assets |
| [Decisions](docs/decisions.md) | The reasoning behind the non-obvious choices |

## License and credit

- **Code** is [GPL-2.0-or-later](LICENSE).
- **Documentation** is [CC BY 4.0](LICENSES/CC-BY-4.0.txt). Reuse must credit **Tarek Okasha** and link to this repository.
- QX Media's name, logos, typefaces and copy are **not** licensed here. See [NOTICE](NOTICE.md).
- [Three.js](https://threejs.org) (MIT) is bundled in the 3D engine.

Copyright (c) 2026 Tarek Okasha.

## About the author

I am **Tarek Okasha**, a robotics and automation engineer in Cairo who builds systems that run without supervision: six-axis robots, AI automations, and the custom software and brand presences that companies actually operate on. More of my work is in my [portfolio](https://tarek-portfolio-phi.vercel.app) and on [GitHub](https://github.com/tarekokashha).
