/**
 * Boots the 3D hero.
 *
 * A classic deferred script, not a module, on purpose. The engine is a real ES module, but it
 * is reached through a dynamic import() rather than a <script type="module"> tag, which keeps
 * it out of reach of LiteSpeed's JS combine/minify pass. Combining an ES module into a
 * concatenated bundle breaks its import statements, and that failure mode is silent until
 * someone loads the homepage.
 *
 * The module it pulls is engine.min.js: engine.js, modules.js and the reachable part of Three
 * tree-shaken into one file by scripts/build-hero.mjs. One request, and the two thirds of the
 * library this scene never touches never ship.
 *
 * Nothing here blocks first paint: the hero's poster and copy are already on screen, and this
 * runs after load. If the import fails, or WebGL is unavailable, the section is marked
 * data-webgl="off" and the CSS collapses it to one screen with the poster and the copy intact.
 */
(function () {
  'use strict'

  var section = document.querySelector('[data-qx-hero]')
  if (!section) return

  var canvas = section.querySelector('[data-qx-canvas]')
  var rawSrc = section.getAttribute('data-qx-hero-src')
  if (!canvas || !rawSrc) return

  /**
   * Resolve against the document, not against this file.
   *
   * A dynamic import() inside a CLASSIC script resolves a relative specifier against the
   * script's own URL, so "../qx-theme/assets/js/hero/engine.js" became
   * "/qx-theme/assets/js/qx-theme/assets/js/hero/engine.js" and aborted. WordPress happens
   * to emit an absolute URL here, which hid the bug on the real site and only broke the
   * local preview, so resolving explicitly is what keeps the two honest with each other.
   */
  var src = new URL(rawSrc, document.baseURI).href
  var shape = new URL(section.getAttribute('data-qx-hero-shape'), document.baseURI).href

  var off = function () {
    section.setAttribute('data-webgl', 'off')
  }

  // Cheap capability probe before pulling 167 KB of Three over the wire.
  var ok = false
  try {
    var probe = document.createElement('canvas')
    ok = !!(window.WebGLRenderingContext && (probe.getContext('webgl2') || probe.getContext('webgl')))
  } catch (e) {
    ok = false
  }
  if (!ok) {
    off()
    return
  }

  var start = function () {
    import(src)
      .then(function (mod) {
        return mod.mountHero({ canvas: canvas, section: section, shapeUrl: shape })
      })
      .catch(off)
  }

  // Wait for load so the hero never competes with the fonts and the LCP text.
  if (document.readyState === 'complete') start()
  else window.addEventListener('load', start, { once: true })
})()
