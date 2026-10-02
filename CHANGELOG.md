# Changelog

All notable changes to this project are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.0.0] - 2026-10-02

First public release of the repository. The theme inside is version **2.3.0**; the live site at qxmedia.sa runs 2.2.1.

### Added

- The complete bilingual theme source: Arabic by default, English under `/en/`, with the scroll-driven 3D hero, block-theme design tokens and the editor styles.
- `inc/brand.php`: logo and watermark helpers that fall back to a text wordmark when no artwork is present, so a fresh install renders a clean header.
- Neutral demo content in `inc/content.php`, in both languages, with every figure, client, quote and credential clearly marked `QX_PLACEHOLDER`.
- A Docker development stack (`docker-compose.yml`) and `scripts/wp-setup.sh` that installs WordPress, activates the theme and creates the pages.
- `scripts/check-repo.mjs`, a zero-dependency gate that fails on leaked credentials, personal contact data, forbidden files, broken Markdown links and any third-party origin referenced from theme code.
- Continuous integration: PHP 8.0 to 8.4 lint, content-key parity in both languages, the RTL and performance gate, and the repository gate.
- Documentation: architecture, the 3D hero, Arabic and RTL engineering, performance, the design system, installation and the design decisions.

### Changed

- Contact details moved from hard-coded values to constants (`QX_WHATSAPP`, `QX_EMAIL`, `QX_CITY_AR`, `QX_CITY_EN`) that are set in `wp-config.php`.
- The font preload, favicon and Open Graph tags are only emitted when their files exist, so a missing asset can no longer cause a 404 on every page view.
- Structured data (`ProfessionalService`) reads the site title, tagline and the contact constants instead of fixed strings.

### Fixed

- The primary navigation printed `<li>` items with no `<ul>` parent, which is invalid HTML and failed the Lighthouse list audit on desktop. `QX_Bare_Link_Walker` now emits the anchors directly.

### Removed

- `qx_ar_num()`, an unused helper for Arabic-Indic section numerals. The design uses Western digits throughout.

### Not distributed

- The brand typefaces, the logo artwork, and the traced logo geometry the hero extrudes (replaced by a neutral demo mark). They belong to the brand, not to the code.
