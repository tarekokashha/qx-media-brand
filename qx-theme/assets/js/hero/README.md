# The 3D hero, on the server

`engine.min.js` is a build artifact: `engine.js`, `modules.js` and the reachable part of
Three.js r169, tree-shaken and minified into one ES module.

Only three files ship:

| File | What it is |
|---|---|
| `boot.js` | Classic deferred script. Probes for WebGL, then dynamic-imports the bundle after the load event. |
| `engine.min.js` | The bundle. 543 KB raw, 141 KB gzipped. |
| `qx-shape.json` | The QX outline the sculpture is extruded from. |

Do not edit `engine.min.js` by hand.

## To change the animation

The sources live in the project repository at `qx-theme/assets/js/hero/`
(`engine.js` for the scroll mapping, phase boundaries and lighting; `modules.js` for the
sculpture, the ribbon, the particles and each abstract module). Edit those, then:

```bash
node scripts/build-hero.mjs
```

That fetches Three if it is missing, rebuilds `engine.min.js`, and prints the size.

## Two things worth knowing before you touch it

**The pin length is in CSS, not here.** `--h-pin` in `assets/css/hero.css` sets how much
scroll the five phases are spread across: 520vh on desktop, 320vh on phones. Progress is
always normalised 0 to 1 across `(pin height - 100vh)`, so changing it re-times the whole
sequence and nothing else needs touching.

**The geometry is traced, not vector.** `qx-shape.json` was traced from the transparent PNG
because the brand kit has no SVG, AI, EPS or PDF of the mark. It reads correctly, including
the Q's counter, but it is 99 points and slightly softer than the real outline. When the
vector arrives, swap `buildQX()` in `modules.js` to Three's `SVGLoader`, delete the JSON, and
rebuild. That is the one open item on this hero.
