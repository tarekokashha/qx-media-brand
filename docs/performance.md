# Performance and quality

What was measured on the live site, how, and what the numbers do and do not say. Everything below was captured on **2 October 2026** against `https://qxmedia.sa`, which runs theme version 2.2.1.

## Lighthouse

Lighthouse 13.4.1, run through Chrome DevTools in navigation mode against the Arabic homepage.

| Category | Mobile | Desktop |
|---|---|---|
| Accessibility | **100** | 95 |
| Best Practices | 96 | 96 |
| SEO | **100** | **100** |

**Not measured here:** the Lighthouse *Performance* category. The run excluded it, and this page does not claim a Performance score. The browser measurements below are the performance evidence.

### The two things that cost points

| Finding | Where it comes from | Status |
|---|---|---|
| Desktop accessibility, "list items are not contained in a list": the header's `<li>` items had no `<ul>` parent | The theme: `qx_nav_links()` used `wp_nav_menu()` with a bare wrapper | **Fixed** in the next theme release (a custom walker prints the anchors directly, with no list wrappers). Not yet deployed to the live site |
| Best Practices, "browser errors were logged to the console" | `elementorFrontendConfig is not defined` from the Elementor plugin, and a `TypeError` thrown by an inline script | Open. The theme prints no inline JavaScript (its only inline script is JSON-LD data), so the second error's source is a plugin and is still being traced |

The `Best Practices` shortfall and the desktop accessibility finding are listed here because a number without its caveats is not evidence.

## In the browser

A real Chromium page load of the English homepage, uncached, from an ordinary home connection, without throttling. Treat the absolute timings as indicative of this machine and network, not as a guarantee for any visitor.

| Metric | Value |
|---|---|
| Largest Contentful Paint | **1.57 s** |
| LCP element | `h1.qx-hero3d__h1`, the hero headline (text, never an image) |
| Cumulative Layout Shift | **0** |
| DOMContentLoaded | 1.73 s |
| Load event | 2.16 s |
| Requests | 36 |
| Transferred | about 360 KB |

The LCP result is the design goal working as intended: the hero's poster is pure CSS and the largest thing painted is text.

### Where the requests go

| Origin | Requests | Source |
|---|---|---|
| The site itself | 33 | Theme, WordPress, Elementor and its add-on |
| `fonts.googleapis.com` | 1 | A plugin (not the theme) |
| `googletagmanager.com` | 1 | A plugin (not the theme) |
| `google-analytics.com` | 1 | A plugin (not the theme) |

**The theme itself requests nothing from a third-party origin.** That is a rule, and it is enforced: an automated check fails the build if any theme file references an origin outside a short allow-list. The three third-party requests on the live site come from plugins installed alongside it, and removing or self-hosting them is a plugin-configuration job.

## What the theme does to stay fast

- **Fonts are self-hosted and preloaded selectively.** One preload per page (the face that sets the largest text above the fold for that script). On an English page the Arabic family is never downloaded, because `unicode-range` splits the families.
- **Only the CSS a page uses is sent**: WordPress's block library loads per block.
- **The LCP element is always text.**
- **About 1.5 KB of JavaScript** is the whole site runtime. Scroll reveals are CSS where `animation-timeline: view()` is supported.
- **The 3D engine never reaches a visitor reading an article or a service page.** On the homepage it loads after the `load` event, only after a WebGL probe succeeds, as one request (141 KB gzipped, tree-shaken from 167 KB).
- **WordPress extras are removed**: emoji, oEmbed, generator tag, dashicons for logged-out visitors, XML-RPC and more. See [architecture.md](architecture.md).
- **No `box-shadow` or `backdrop-filter`**, because both re-rasterise when composited over the animating gradient field. A gate enforces it.

## What the theme cannot control

Plugins decide a lot. On the live site, Elementor and its add-on load jQuery and several scripts on the homepage even though the homepage is the theme's own template; analytics and tag manager come from other plugins. The cleanest further gain is configuration, not code: scope the page-builder assets to the pages that use them, and self-host or defer the analytics tags.

