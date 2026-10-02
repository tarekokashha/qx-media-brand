# Design decisions

The non-obvious choices in this theme, the reason for each, and what it costs. Each is the kind of thing a later edit could undo by accident, which is why the reasoning is written down. Decisions by [Tarek Okasha](https://github.com/tarekokashha).

## 1. No page builder for the pages the design covers

**Decision.** The homepage and the three interior pages are PHP templates, block-theme tokens (`theme.json`) and a small set of CSS components. Pages built with Elementor are left to Elementor.
**Why.** A page builder injects its own CSS and JavaScript on every page it renders, and the design depends on a hero that controls its own scroll and a type system that cannot be overridden by a widget. Templates make the output predictable, and they keep the bytes small.
**Cost.** Editing the structure of those pages means editing code. Editing the **text** does not: all copy lives in one file (`inc/content.php`).

## 2. Zero third-party requests from theme code

**Decision.** No CDN fonts, no icon CDN, no analytics by default. Fonts are self-hosted.
**Why.** A request to a font CDN leaks every visitor's IP address to a third party, which matters under Saudi privacy law, and it adds a connection to the critical path.
**Enforced by.** An automated check fails if a theme file references an origin outside a short allow-list.

## 3. The 3D engine is reached by `import()` from a classic script

**Decision.** `boot.js` is a classic deferred script that probes for WebGL, waits for `load`, then dynamically imports the engine.
**Why.** LiteSpeed's JavaScript optimiser combines and minifies scripts, and concatenating an ES module breaks its `import` statements. The failure is silent until someone loads the homepage. A dynamic `import()` from a classic script keeps the engine out of its reach. The exclusion is also declared in the theme, through the optimiser's own filters, so it travels with the code.
**Cost.** One extra hop before the scene starts, which is the point: the hero never competes with the LCP text.

## 4. Tree-shake Three.js instead of hand-listing what is used

**Decision.** A build step bundles the engine entry with esbuild and lets it shake the whole import graph.
**Why.** A hand-written re-export file is one forgotten class away from a runtime crash that shows up only on the live homepage. Bundling the entry makes the compiler find what is reachable. The result is 141 KB gzipped, down from 167 KB for the full library.
**Cost.** The build needs network access to fetch the pinned Three.js release.

## 5. The 3D scene is decorative; the content is HTML

**Decision.** The headline, the lead and both calls to action are semantic HTML. The canvas is `aria-hidden`.
**Why.** It makes WebGL optional by construction, so the failure modes (no WebGL, no JavaScript, reduced motion) all land on a working page, and the LCP element is text.

## 6. Reduced motion shows an early frame, not the final one

**Decision.** The static hero frame is progress 0.14, not 1.0.
**Why.** At the end of the scrub the copy has long faded out and the sculpture is lit. In a still image they share the screen, and the lit sculpture sits directly behind the lead paragraph. The brief wanted a final state *and* readable contrast. Where they disagree, **contrast wins**.

## 7. `overflow-x: clip`, not `hidden`, on the body

**Decision.** The body uses `overflow-x: clip`.
**Why.** `overflow-x: hidden` makes the body a scroll container on the block axis too, which silently breaks `position: sticky` for descendants relative to the viewport. The hero's pinned stage depends on sticky. `clip` prevents the same horizontal overflow without creating a scroll container.

## 8. Logical properties and no RTL stylesheet

**Decision.** `margin-inline-start`, `inset-inline-end` and friends everywhere; no `rtl.css`.
**Why.** One stylesheet that mirrors itself cannot fall out of sync with a second one. It only holds if enforced, so an automated gate fails on any physical-direction property. See [rtl-and-arabic.md](rtl-and-arabic.md).

## 9. Contact details are constants, in configuration

**Decision.** `QX_WHATSAPP`, `QX_EMAIL`, `QX_CITY_AR` and `QX_CITY_EN` are set in `wp-config.php`; the theme only supplies placeholder defaults.
**Why.** One value feeds every page that shows it, and the repository can be public without publishing a business's phone number.

## 10. Pages are matched by slug, with an explicit template override

**Decision.** `qx_page_role()` recognises the About, Services and Contact pages by slug, but an assigned page template always wins.
**Why.** The designs appear without anyone having to open six pages in wp-admin, and the site owner can still override any of them. Existing URLs are part of the contract and do not change.

## 11. Preload exactly one font

**Decision.** One preload per page: the face that sets the largest text above the fold for that script.
**Why.** Preloading more than one is how sites accidentally delay their own LCP. The display weight is not preloaded: it paints in the fallback first, and the metric difference is small enough that a second 64 KB request costs more than it saves.

## 12. No `box-shadow`, no `backdrop-filter`

**Decision.** Depth comes from ground changes and hairlines. Both properties are banned by a gate.
**Why.** They re-rasterise when composited over the animating gradient field. This is a design choice that is also a performance rule.

## 13. The demo ships neutral copy, and claims are marked

**Decision.** `inc/content.php` contains placeholder text, and every figure, client, quote and credential is tagged `QX_PLACEHOLDER` with an unmistakable demo value (`00+`, `CLIENT ONE`).
**Why.** A theme that renders believable statistics can be launched with them. Marking each claim, and making the demo values impossible to mistake for real results, makes the unverified part visible. Anything a business publishes about itself needs a source and, for a client quote, written permission.

## 14. Brand assets stay with the brand

**Decision.** No typefaces, logos, images or the traced logo geometry are committed. `qx_mark()` falls back to a text wordmark, font preloads and favicon and Open Graph tags are emitted only when their files exist, and the hero ships a neutral demo mark.
**Why.** Those belong to QX Media, not to the code. The theme degrades gracefully instead of breaking, and the repository stays safe to publish.
