import * as THREE from './vendor/three.module.min.js';
import {
  C, clamp, seg, envTexture, backdrop, buildQX, buildFloor, buildDust, buildRibbon,
  buildSignals, buildTrajectory, buildOrbits, buildCompass, buildGridPlanes,
  buildContentFrame, buildTargetRing, buildHaze,
} from './modules.js';

const QUALITY = {
  desktop: { dpr: 2, signals: 1100, dust: 420, traj: 700, shadows: true },
  tablet: { dpr: 1.75, signals: 700, dust: 300, traj: 480, shadows: true },
  mobile: { dpr: 1.5, signals: 420, dust: 170, traj: 260, shadows: false },
};

const tier = () => {
  const w = window.innerWidth;
  if (w < 760) return 'mobile';
  if (w < 1180) return 'tablet';
  return 'desktop';
};

export async function mountHero({ canvas, section, shapeUrl }) {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let gl;
  try {
    gl = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, preserveDrawingBuffer: true, powerPreference: 'high-performance' });
  } catch (e) {
    section.setAttribute('data-webgl', 'off');
    return { destroy() {} };
  }
  if (!gl.capabilities.isWebGL2 && !gl.getContext()) {
    section.setAttribute('data-webgl', 'off');
    return { destroy() {} };
  }

  let q = QUALITY[tier()];
  gl.setPixelRatio(Math.min(window.devicePixelRatio || 1, q.dpr));
  gl.outputColorSpace = THREE.SRGBColorSpace;
  gl.toneMapping = THREE.ACESFilmicToneMapping;
  gl.toneMappingExposure = 1.06;
  gl.shadowMap.enabled = q.shadows;
  gl.shadowMap.type = THREE.PCFSoftShadowMap;

  const scene = new THREE.Scene();
  const env = envTexture();
  scene.environment = env;
  const bg = backdrop();
  scene.background = bg;

  const camera = new THREE.PerspectiveCamera(30, 16 / 9, 0.1, 60);
  const camBase = new THREE.Vector3(0.72, 0.66, 6.05);
  const camTarget = new THREE.Vector3(0.05, 0.20, 0);

  const shapeData = await fetch(shapeUrl).then((r) => r.json());

  const root = new THREE.Group();
  scene.add(root);

  const qx = buildQX(shapeData, { height: 1.52 });
  qx.group.position.set(0.05, 0.06, 0);
  root.add(qx.group);

  const floorY = qx.bottom + qx.group.position.y - 0.005;
  const floor = buildFloor(floorY, env);
  root.add(floor.mesh);
  qx.setFloor(floorY);

  const dust = buildDust(floorY, q.dust);
  const ribbon = buildRibbon(floorY);
  const signals = buildSignals(q.signals);
  const traj = buildTrajectory(q.traj);
  const orbits = buildOrbits();
  const compass = buildCompass();
  const planes = buildGridPlanes();
  const frame = buildContentFrame();
  const target = buildTargetRing();
  const haze = buildHaze();

    dust.group.name = 'dust'; ribbon.mesh.name = 'ribbon'; ribbon.light.name = 'ribbonLight';
  signals.pts.name = 'signals'; traj.pts.name = 'trajectory'; orbits.group.name = 'orbits';
  compass.group.name = 'compass'; planes.group.name = 'planes'; frame.group.name = 'frame';
  target.group.name = 'target'; haze.group.name = 'haze'; floor.mesh.name = 'floor';
  root.add(dust.group, ribbon.mesh, ribbon.light, signals.pts, traj.pts,
    orbits.group, compass.group, planes.group, frame.group, target.group, haze.group);

  const amb = new THREE.HemisphereLight(0x2A1218, 0x070506, 0.55);
  const key = new THREE.SpotLight(new THREE.Color(C.cream), 24, 22, 0.62, 0.6, 1.8);
  key.position.set(3.2, 4.6, 3.4);
  key.target.position.set(0.05, 0.1, 0);
  const rim = new THREE.DirectionalLight(new THREE.Color(C.terra), 3.6);
  rim.position.set(-3.8, 1.4, -2.4);
  const fill = new THREE.PointLight(new THREE.Color(C.crim), 2.2, 8, 2);
  fill.position.set(-1.6, 0.35, 2.1);
  if (q.shadows) {
    key.castShadow = true;
    key.shadow.mapSize.set(1024, 1024);
    key.shadow.bias = -0.0018;
    key.shadow.radius = 4;
  }
  scene.add(amb, key, key.target, rim, fill);

  /* ---------- layout ---------- */
  let mobile = tier() === 'mobile';
  const host = section.closest('[dir]');
  const rtl = !!host && host.getAttribute('dir') === 'rtl';
  let offsetX = 0;
  function layout() {
    const w = section.clientWidth || window.innerWidth;
    const h = window.innerHeight;
    camera.aspect = w / h;
    mobile = tier() === 'mobile';
    const portrait = h > w;
    camera.fov = portrait ? 40 : (w / h < 1.5 ? 34 : 30);
    const push = portrait ? 1.5 : (w / h < 1.5 ? 1.14 : 1);
    camera.position.copy(camBase).multiplyScalar(1).setZ(camBase.z * push);
    camera.position.x = camBase.x * (portrait ? 0.35 : 1);
    camera.lookAt(camTarget.x * (portrait ? 0.4 : 1), camTarget.y + (portrait ? 0.12 : 0), camTarget.z);
    root.scale.setScalar(portrait ? 0.86 : 1);
    offsetX = portrait ? 0 : (rtl ? -0.66 : 0.66);
    gl.setSize(w, h, false);
    gl.setPixelRatio(Math.min(window.devicePixelRatio || 1, QUALITY[tier()].dpr));
  }
  layout();

  /* ---------- overlay ---------- */
  let overlayEls = Array.from(section.querySelectorAll('[data-hero-in]'));
  function overlay(p) {
    for (const el of overlayEls) {
      const [i0, i1] = el.dataset.heroIn.split(',').map(Number);
      const a = seg(p, i0, i1);
      let o = a;
      if (el.dataset.heroOut) {
        const [o0, o1] = el.dataset.heroOut.split(',').map(Number);
        o = a * (1 - seg(p, o0, o1));
      }
      el.style.opacity = o.toFixed(3);
      const dy = el.dataset.heroShift ? Number(el.dataset.heroShift) : 14;
      el.style.transform = `translate3d(0,${((1 - a) * dy).toFixed(2)}px,0)`;
      el.style.pointerEvents = o > 0.6 ? 'auto' : 'none';
      el.setAttribute('aria-hidden', o < 0.05 ? 'true' : 'false');
    }
  }

  /* ---------- scroll ---------- */
  let target_p = 0, cur_p = 0, visible = true, running = true, raf = 0;
  function readProgress() {
    const r = section.getBoundingClientRect();
    const span = r.height - window.innerHeight;
    if (span <= 0) return 0;
    return clamp(-r.top / span);
  }
  const io = new IntersectionObserver((es) => {
    visible = es[0].isIntersecting;
    if (visible && running && !raf && !reduced) raf = requestAnimationFrame(loop);
  }, { rootMargin: '10% 0px' });
  io.observe(section);

  const onVis = () => {
    running = !document.hidden;
    if (running && visible && !raf && !reduced) raf = requestAnimationFrame(loop);
  };
  document.addEventListener('visibilitychange', onVis);

  let rt = 0;
  const onResize = () => { clearTimeout(rt); rt = setTimeout(layout, 140); };
  window.addEventListener('resize', onResize);
  window.addEventListener('orientationchange', onResize);

  const clock = new THREE.Clock();

  function frameRender(p, t, withOverlay) {
    root.position.x = offsetX * (1 - seg(p, 0.04, 0.2));
    qx.update(p);
    dust.update(p, t);
    ribbon.update(p, t);
    signals.update(p, t, mobile ? 0.8 : 1);
    traj.update(p, t, mobile ? 0.8 : 1);
    orbits.update(p, t);
    compass.update(p, t);
    planes.update(p, t);
    frame.update(p, t);
    target.update(p, t);
    haze.update(p, t);

    key.intensity = 22 + seg(p, 0.1, 0.8) * 12;
    rim.intensity = 3.2 + seg(p, 0.2, 0.9) * 2.2;
    fill.intensity = 1.6 + seg(p, 0.35, 1) * 2.6;
    gl.toneMappingExposure = 1.0 + seg(p, 0.5, 1) * 0.14;

    // 1.5% camera breathing, never enough to shift the mobile-safe crop
    const br = Math.sin(t * 0.22) * 0.015;
    camera.position.y = camBase.y + br * 0.6;
    camera.lookAt(camTarget.x * (window.innerHeight > window.innerWidth ? 0.4 : 1), camTarget.y + br * 0.3, camTarget.z);

    gl.render(scene, camera);
    if (withOverlay) overlay(p);
  }

  function loop() {
    raf = 0;
    if (!running || !visible) return;
    const t = clock.getElapsedTime();
    target_p = readProgress();
    cur_p += (target_p - cur_p) * 0.14;
    if (Math.abs(target_p - cur_p) < 0.0004) cur_p = target_p;
    frameRender(cur_p, t, true);
    raf = requestAnimationFrame(loop);
  }

  if (reduced) {
    /**
     * One static frame, and the overlay is left alone.
     *
     * Two departures from the prototype here, both deliberate.
     *
     * It called overlay(1), which is wrong: at p=1 the intro block is past its fade-out
     * window, so the headline, the lead and both CTAs all resolve to opacity 0 and a
     * reduced-motion visitor is left with four numbers and no way in. Not running the
     * overlay at all leaves every element at its CSS default, which is the copy visible
     * and only the decorative stage labels hidden.
     *
     * And it froze at p=0.92. In the scrubbed version the copy has faded out long before
     * the scene brightens, so the two never share the screen. In a still they do, and at
     * 0.92 the lit crimson sculpture sits directly behind the lead paragraph and the CTA.
     * An early frame keeps the sculpture matte and still offset away from the copy column,
     * which is the phase-one composition and reads cleanly. The brief asks for a final
     * state and for readable text contrast; where those two disagree, contrast wins.
     */
    cur_p = 0.14;
    frameRender(cur_p, 0, false);
    section.setAttribute('data-static', '');
  } else {
    cur_p = target_p = readProgress();
    frameRender(cur_p, 0, true);
    raf = requestAnimationFrame(loop);
  }
  section.setAttribute('data-webgl', 'on');

  const api = {
    three: { scene, camera, gl, root },
    renderAt(p) { cur_p = target_p = p; frameRender(p, clock.getElapsedTime(), true); },
    refresh() {
      overlayEls = Array.from(section.querySelectorAll('[data-hero-in]'));
      layout();
      overlay(cur_p);
    },
    destroy() {
      cancelAnimationFrame(raf);
      io.disconnect();
      document.removeEventListener('visibilitychange', onVis);
      window.removeEventListener('resize', onResize);
      window.removeEventListener('orientationchange', onResize);
      [dust, ribbon, signals, traj, orbits, compass, planes, frame, target, haze, floor].forEach((m) => m.dispose && m.dispose());
      qx.mesh.geometry.dispose();
      qx.face.dispose(); qx.side.dispose(); qx.refMat.dispose();
      env.dispose(); bg.dispose();
      gl.dispose();
      delete section.qxHero;
    },
  };
  section.qxHero = api;
  return api;
}
