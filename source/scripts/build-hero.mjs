/**
 * Builds the 3D hero's production bundle.
 *
 * The hero imports Three as an ES module. Shipping the whole library costs 671 KB raw and
 * 167 KB gzipped, most of which the scene never touches: no loaders, no controls, no
 * post-processing, no animation system, no raycaster. Bundling engine.js with esbuild and
 * letting it tree-shake across the import graph keeps only the reachable code.
 *
 * Bundling the entry rather than hand-listing the symbols the scene uses matters: a
 * hand-written re-export file is one forgotten class away from a runtime crash that only
 * shows up on the live homepage.
 *
 *   node scripts/build-hero.mjs
 *   -> qx-theme/assets/js/hero/engine.min.js
 *
 * vendor/three.module.min.js is a build input only and is excluded from the shipped theme.
 * It is re-fetched here if missing.
 */
import { execFile } from 'node:child_process'
import { readFile, writeFile, mkdir, stat } from 'node:fs/promises'
import { existsSync } from 'node:fs'
import { gzipSync } from 'node:zlib'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import { promisify } from 'node:util'

const run = promisify(execFile)
const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..')
const HERO = path.join(ROOT, 'qx-theme/assets/js/hero')
const VENDOR = path.join(HERO, 'vendor/three.module.min.js')
const THREE_URL = 'https://unpkg.com/three@0.169.0/build/three.module.min.js'
const ENTRY = path.join(HERO, 'engine.js')
const OUT = path.join(HERO, 'engine.min.js')

if (!existsSync(VENDOR)) {
  console.log('fetching three@0.169.0 ...')
  const res = await fetch(THREE_URL)
  if (!res.ok) throw new Error('three fetch failed: HTTP ' + res.status)
  await mkdir(path.dirname(VENDOR), { recursive: true })
  await writeFile(VENDOR, Buffer.from(await res.arrayBuffer()))
}

await run('npx', ['--yes', 'esbuild', ENTRY,
  '--bundle',
  '--format=esm',
  '--minify',
  '--target=es2020',
  '--legal-comments=none',
  '--outfile=' + OUT,
], { shell: true })

const before = (await stat(VENDOR)).size
const after = (await readFile(OUT)).length
const gz = gzipSync(await readFile(OUT)).length

console.log(`three alone      ${(before / 1024).toFixed(0)} KB raw`)
console.log(`engine.min.js    ${(after / 1024).toFixed(0)} KB raw   ${(gz / 1024).toFixed(0)} KB gz`)
console.log(`saved            ${(((before - after) / before) * 100).toFixed(0)}% of the library`)
