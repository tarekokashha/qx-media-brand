/**
 * RTL / performance lint for the QX theme CSS.
 *
 * The RTL strategy is "logical properties everywhere, no second stylesheet". That only
 * holds if it is enforced — one `margin-left` is invisible in English and obviously wrong
 * in Arabic, and it is exactly the kind of thing that creeps in during later edits.
 *
 * Also fails on two performance rules the design commits to: no `box-shadow` and no
 * `backdrop-filter`. Both re-rasterise when composited over the animating gradient field.
 *
 *   node scripts/gate-rtl.mjs
 */
import { readFile, readdir } from 'node:fs/promises'
import path from 'node:path'

const FILES = [
  'qx-theme/style.css',
  'qx-theme/assets/css/components.css',
  'qx-theme/assets/css/fonts.css',
  'qx-theme/assets/css/editor.css',
]

const RULES = [
  {
    name: 'physical margin/padding',
    re: /(^|[;{\s])(margin|padding)-(left|right)\s*:/gi,
    fix: 'use margin-inline-start / padding-inline-end etc.',
  },
  {
    name: 'physical border side',
    re: /(^|[;{\s])border-(left|right)(-[a-z]+)?\s*:/gi,
    fix: 'use border-inline-start / border-inline-end',
  },
  {
    name: 'physical text-align',
    re: /text-align\s*:\s*(left|right)\b/gi,
    fix: 'use text-align: start / end',
  },
  {
    name: 'physical inset',
    re: /(^|[;{\s])(left|right)\s*:\s*(?!auto)/gi,
    fix: 'use inset-inline-start / inset-inline-end',
  },
  {
    name: 'box-shadow',
    re: /box-shadow\s*:/gi,
    fix: 'the design uses ground changes and hairlines for depth, not shadows',
  },
  {
    name: 'backdrop-filter',
    re: /backdrop-filter\s*:/gi,
    fix: 're-rasterises every frame over the animating field; use an opaque surface',
  },
  {
    /**
     * Only `ease-in-out` is flagged now.
     *
     * The previous pattern also matched bare `ease`, which produced two kinds of false
     * positive: it matched the `ease)` inside `var(--qx-ease)`, and it flagged the two
     * `0.4s ease` hover transitions that the design handoff specifies verbatim. A gate
     * that cries wolf on correct code gets ignored, which is worse than not having it.
     */
    name: 'ease-in-out',
    re: /transition[^;{}]*\bease-in-out\b/gi,
    fix: 'use var(--qx-ease), or the exact easing the handoff specifies',
  },
]

/**
 * Lines that are deliberate and documented get an allowance, keyed by the exact
 * substring. Each one must be justified in a comment here — an allowlist without
 * reasons is just a disabled test.
 */
const ALLOW = [
  // The logo swash and the service arrow are mirrored on purpose; a flipped logo is a
  // broken logo, and a directional arrow must follow the reading direction.
  'transform: scaleX(-1)',
  // transform-origin takes physical keywords only; both have an explicit [dir='rtl'] rule
  // immediately beneath them that flips the origin.
  'transform-origin: left center',
  'transform-origin: right center',
  // Gradient colour-stop positions are visual, not directional, and the seam is symmetric.
  'to right,',
  // background-position for the underline gradient; the RTL case is handled directly below.
  'background-position: 0 100%',
  'background-position: 100% 100%',
  // The QX watermark is artwork and is placed top-left in BOTH locales by the handoff,
  // so it must not mirror. Documented in style.css at .qx-watermark.
  'left: -12vw',
  // flex alignment keywords, not box-model sides.
  'align-items: flex-start',
  'flex-direction: column',
]

let failures = 0
let scanned = 0

for (const rel of FILES) {
  let src
  try {
    src = await readFile(path.resolve(rel), 'utf8')
  } catch {
    console.log(`  SKIP  ${rel} (not found)`)
    continue
  }
  scanned++
  const lines = src.split('\n')

  lines.forEach((line, i) => {
    const trimmed = line.trim()
    // Skip comment-only lines — prose mentioning `box-shadow` is not a violation.
    if (trimmed.startsWith('*') || trimmed.startsWith('/*') || trimmed.startsWith('//')) return
    if (ALLOW.some((a) => line.includes(a))) return

    for (const rule of RULES) {
      rule.re.lastIndex = 0
      if (rule.re.test(line)) {
        console.log(`  FAIL  ${rel}:${i + 1}  [${rule.name}]`)
        console.log(`        ${trimmed}`)
        console.log(`        → ${rule.fix}`)
        failures++
      }
    }
  })
}

console.log(`\n${scanned} stylesheet(s) scanned.`)
if (failures === 0) {
  console.log('RTL + PERF GATE PASSED')
  process.exit(0)
}
console.log(`${failures} violation(s)`)
process.exit(1)
