import * as THREE from './vendor/three.module.min.js';

export const C = {
  char: 0x201D1F, burg: 0x7A1230, crim: 0xA7153C, terra: 0xD87959, cream: 0xF0DFC2,
};

export const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
export const seg = (p, a, b) => { const t = clamp((p - a) / (b - a)); return t * t * (3 - 2 * t); };
export const lin = (p, a, b) => clamp((p - a) / (b - a));

export function rng(seed) {
  let s = seed >>> 0;
  return () => { s = (s * 1664525 + 1013904223) >>> 0; return s / 4294967296; };
}

function gradientCanvas(stops, w = 512, h = 256, radial = true) {
  const cv = document.createElement('canvas'); cv.width = w; cv.height = h;
  const g = cv.getContext('2d');
  const grad = radial
    ? g.createRadialGradient(w * 0.5, h * 0.62, 0, w * 0.5, h * 0.62, w * 0.55)
    : g.createLinearGradient(0, 0, 0, h);
  stops.forEach(([o, col]) => grad.addColorStop(o, col));
  g.fillStyle = grad; g.fillRect(0, 0, w, h);
  return cv;
}

export function envTexture() {
  const cv = gradientCanvas([[0, '#5A0E22'], [0.35, '#2A1218'], [1, '#08060700']], 512, 256, true);
  const g = cv.getContext('2d');
  g.globalCompositeOperation = 'lighter';
  const warm = g.createRadialGradient(400, 70, 0, 400, 70, 190);
  warm.addColorStop(0, 'rgba(240,223,194,0.85)'); warm.addColorStop(1, 'rgba(240,223,194,0)');
  g.fillStyle = warm; g.fillRect(0, 0, 512, 256);
  const rim = g.createRadialGradient(80, 150, 0, 80, 150, 170);
  rim.addColorStop(0, 'rgba(216,121,89,0.7)'); rim.addColorStop(1, 'rgba(216,121,89,0)');
  g.fillStyle = rim; g.fillRect(0, 0, 512, 256);
  const t = new THREE.CanvasTexture(cv);
  t.mapping = THREE.EquirectangularReflectionMapping;
  t.colorSpace = THREE.SRGBColorSpace;
  return t;
}

export function backdrop() {
  const cv = gradientCanvas([[0, '#2C0A16'], [0.34, '#140D10'], [1, '#060405']], 1024, 512, true);
  const t = new THREE.CanvasTexture(cv);
  t.mapping = THREE.EquirectangularReflectionMapping;
  t.colorSpace = THREE.SRGBColorSpace;
  return t;
}

function softSprite(color, size = 128) {
  const cv = document.createElement('canvas'); cv.width = cv.height = size;
  const g = cv.getContext('2d');
  const grad = g.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
  grad.addColorStop(0, color); grad.addColorStop(0.4, color.replace(/[\d.]+\)$/, '0.35)'));
  grad.addColorStop(1, color.replace(/[\d.]+\)$/, '0)'));
  g.fillStyle = grad; g.fillRect(0, 0, size, size);
  const t = new THREE.CanvasTexture(cv);
  t.colorSpace = THREE.SRGBColorSpace;
  return t;
}

/* ---------- QX sculpture (extruded from the traced brand outline) ---------- */
export function buildQX(shapeData, opts = {}) {
  const height = opts.height ?? 1.5;
  const shapes = shapeData.shapes.map((s) => {
    const sh = new THREE.Shape(s.outer.map(([x, y]) => new THREE.Vector2(x, y)));
    (s.holes || []).forEach((h) => sh.holes.push(new THREE.Path(h.map(([x, y]) => new THREE.Vector2(x, y)))));
    return sh;
  });
  const geo = new THREE.ExtrudeGeometry(shapes, {
    depth: 0.15, bevelEnabled: true, bevelThickness: 0.009, bevelSize: 0.007,
    bevelOffset: 0, bevelSegments: 3, curveSegments: 6,
  });
  geo.center();
  geo.computeVertexNormals();

  const glow = { value: 0.0 };
  const ember = { value: 0.25 };
  const glowCol = { value: new THREE.Color(C.cream) };
  const coreCol = { value: new THREE.Color(C.terra) };
  const patch = (m, bandAmp) => {
    const amp = { value: bandAmp };
    m.onBeforeCompile = (s) => {
      s.uniforms.uBandAmp = amp;
      s.uniforms.uGlow = glow;
      s.uniforms.uEmber = ember;
      s.uniforms.uGlowCol = glowCol;
      s.uniforms.uCoreCol = coreCol;
      s.vertexShader = 'varying vec3 vObj;\n' + s.vertexShader.replace(
        '#include <begin_vertex>',
        '#include <begin_vertex>\n  vObj = transformed;'
      );
      s.fragmentShader = 'uniform float uGlow;\nuniform float uEmber;\nuniform float uBandAmp;\nuniform vec3 uGlowCol;\nuniform vec3 uCoreCol;\nvarying vec3 vObj;\n' + s.fragmentShader.replace(
        'vec3 totalEmissiveRadiance = emissive;',
        `vec3 totalEmissiveRadiance = emissive;
        float d = length(vObj - vec3(0.06, -0.10, 0.0));
        float band = smoothstep(uGlow + 0.055, uGlow + 0.005, d) * smoothstep(uGlow - 0.075, uGlow - 0.01, d);
        float core = smoothstep(0.075, 0.0, d);
        totalEmissiveRadiance += uGlowCol * band * uBandAmp + uCoreCol * core * uEmber;`
      );
    };
    m.customProgramCacheKey = () => 'qxglow' + bandAmp;
    return m;
  };

  const face = patch(new THREE.MeshPhysicalMaterial({
    color: new THREE.Color(C.char), roughness: 0.82, metalness: 0.12,
    clearcoat: 0, clearcoatRoughness: 0.35, emissive: new THREE.Color(0x000000),
  }), 0.05);
  const side = patch(new THREE.MeshPhysicalMaterial({
    color: new THREE.Color(C.char), roughness: 0.55, metalness: 0.45,
    clearcoat: 0, emissive: new THREE.Color(0x000000),
  }), 0.55);

  const mesh = new THREE.Mesh(geo, [face, side]);
  mesh.name = 'qx';
  const s = height;
  mesh.scale.setScalar(s);
  mesh.castShadow = true;

  const group = new THREE.Group();
  group.add(mesh);
  group.rotation.y = -0.26;
  group.rotation.x = 0.015;

  geo.computeBoundingBox();
  const bottom = geo.boundingBox.min.y * s;

  // cheap mirrored reflection
  const refMat = new THREE.MeshBasicMaterial({
    color: new THREE.Color(C.char), transparent: true, opacity: 0.16,
    depthWrite: false, side: THREE.BackSide,
  });
  const refl = new THREE.Mesh(geo, refMat);
  refl.scale.set(s, -s, s);

  return {
    group, mesh, refl, face, side, refMat, bottom,
    setFloor(y) { refl.position.y = 2 * (y - group.position.y); group.add(refl); },
    update(p) {
      const shape = seg(p, 0.34, 0.62);
      const done = seg(p, 0.62, 0.95);
      const cFace = new THREE.Color(C.char).lerp(new THREE.Color(C.burg), shape);
      const cSide = new THREE.Color(C.char).lerp(new THREE.Color(C.terra), shape * 0.92);
      face.color.copy(cFace);
      side.color.copy(cSide);
      face.roughness = 0.82 - 0.5 * shape;
      face.metalness = 0.12 + 0.1 * shape;
      face.clearcoat = shape * 0.9 + done * 0.1;
      side.roughness = 0.55 - 0.28 * shape;
      side.metalness = 0.45 + 0.35 * shape;
      refMat.color.copy(cFace);
      refMat.opacity = 0.07 + 0.06 * (shape * 0.5 + done * 0.5);
      glow.value = seg(p, 0.13, 0.66) * 2.05;
      ember.value = 0.30 + seg(p, 0.08, 0.62) * 0.55;
      coreCol.value.setHex(C.terra).lerp(new THREE.Color(C.cream), seg(p, 0.3, 0.85));
    },
  };
}

/* ---------- floor ---------- */
export function buildFloor(y, env) {
  const g = new THREE.PlaneGeometry(26, 26);
  const fade = document.createElement('canvas'); fade.width = fade.height = 256;
  const fc = fade.getContext('2d');
  const fg = fc.createRadialGradient(128, 128, 0, 128, 128, 128);
  fg.addColorStop(0, '#ffffff'); fg.addColorStop(0.34, '#c8c8c8');
  fg.addColorStop(0.62, '#3a3a3a'); fg.addColorStop(1, '#000000');
  fc.fillStyle = fg; fc.fillRect(0, 0, 256, 256);
  const alphaMap = new THREE.CanvasTexture(fade);
  const m = new THREE.MeshPhysicalMaterial({
    color: 0x070508, roughness: 0.18, metalness: 0.95,
    clearcoat: 1, clearcoatRoughness: 0.14, envMap: env, envMapIntensity: 0.3,
    transparent: true, alphaMap, depthWrite: false,
  });
  const mesh = new THREE.Mesh(g, m);
  mesh.rotation.x = -Math.PI / 2;
  mesh.position.y = y;
  mesh.receiveShadow = true;
  return { mesh, dispose() { g.dispose(); m.dispose(); alphaMap.dispose(); } };
}

/* ---------- mineral dust ---------- */
export function buildDust(y, count) {
  const r = rng(7);
  const mound = new THREE.SphereGeometry(0.95, 40, 18);
  const pos = mound.attributes.position;
  for (let i = 0; i < pos.count; i++) {
    const x = pos.getX(i), yy = pos.getY(i), z = pos.getZ(i);
    const n = Math.sin(x * 7.3) * Math.cos(z * 6.1) * 0.09 + Math.sin(x * 17) * 0.03;
    pos.setXYZ(i, x * (1 + n * 0.2), Math.max(0, yy) * (0.10 + n * 0.05), z * (0.30 + n * 0.1));
  }
  mound.computeVertexNormals();
  const mMat = new THREE.MeshStandardMaterial({ color: 0x3A1420, roughness: 1, metalness: 0 });
  const moundMesh = new THREE.Mesh(mound, mMat);
  moundMesh.position.set(0.05, y + 0.001, 0.06);
  moundMesh.scale.setScalar(1.05);

  const n = count;
  const p = new Float32Array(n * 3);
  const sz = new Float32Array(n);
  for (let i = 0; i < n; i++) {
    const a = r() * Math.PI * 2, rad = 0.35 + r() * 0.85;
    p[i * 3] = Math.cos(a) * rad + 0.05;
    p[i * 3 + 1] = y + r() * 0.05;
    p[i * 3 + 2] = Math.sin(a) * rad * 0.34 + 0.06;
    sz[i] = 0.008 + r() * 0.014;
  }
  const g = new THREE.BufferGeometry();
  g.setAttribute('position', new THREE.BufferAttribute(p, 3));
  g.setAttribute('aSize', new THREE.BufferAttribute(sz, 1));
  const mat = new THREE.PointsMaterial({
    color: new THREE.Color(C.terra), size: 0.014, transparent: true,
    opacity: 0.4, sizeAttenuation: true, depthWrite: false,
    map: softSprite('rgba(216,121,89,1)', 64),
  });
  const pts = new THREE.Points(g, mat);

  const group = new THREE.Group();
  group.add(moundMesh, pts);
  return {
    group,
    update(p2, t) {
      mMat.color.setHex(0x3A1420).lerp(new THREE.Color(C.burg), seg(p2, 0.3, 0.8) * 0.5);
      mat.opacity = 0.34 + Math.sin(t * 0.6) * 0.04 + seg(p2, 0, 0.25) * 0.16;
    },
    dispose() { mound.dispose(); mMat.dispose(); g.dispose(); mat.dispose(); },
  };
}

/* ---------- energy ribbon ---------- */
export function buildRibbon(y) {
  const curve = new THREE.CatmullRomCurve3([
    new THREE.Vector3(-4.2, y + 0.01, 2.1),
    new THREE.Vector3(-2.3, y + 0.02, 1.35),
    new THREE.Vector3(-1.1, y + 0.02, 0.55),
    new THREE.Vector3(-0.35, y + 0.03, -0.05),
    new THREE.Vector3(0.25, y + 0.04, 0.28),
    new THREE.Vector3(0.32, y + 0.16, 0.16),
    new THREE.Vector3(0.12, y + 0.42, 0.05),
    new THREE.Vector3(0.06, y + 0.62, 0.0),
  ], false, 'catmullrom', 0.6);

  const geo = new THREE.TubeGeometry(curve, 260, 0.014, 10, false);
  const mat = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: {
      uHead: { value: 0.0 }, uTime: { value: 0 },
      uA: { value: new THREE.Color(C.terra) }, uB: { value: new THREE.Color(C.cream) },
    },
    vertexShader: `varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }`,
    fragmentShader: `
      uniform float uHead; uniform float uTime; uniform vec3 uA; uniform vec3 uB;
      varying vec2 vUv;
      void main(){
        float t = vUv.x;
        float reveal = smoothstep(uHead + 0.02, uHead - 0.10, t);
        float head = smoothstep(uHead - 0.05, uHead, t) * smoothstep(uHead + 0.03, uHead, t);
        float pulse = 0.72 + 0.28 * sin(t * 26.0 - uTime * 2.2);
        vec3 col = mix(uA, uB, pow(t, 0.7));
        float a = reveal * pulse * 0.9 + head * 1.4;
        if (a < 0.01) discard;
        gl_FragColor = vec4(col * (0.8 + head * 1.6), a);
      }`,
  });
  const mesh = new THREE.Mesh(geo, mat);
  const light = new THREE.PointLight(new THREE.Color(C.terra), 0, 3.2, 2);
  const headPos = new THREE.Vector3();
  return {
    mesh, light,
    update(p, t) {
      const h = 0.12 + seg(p, 0.0, 0.30) * 0.94;
      mat.uniforms.uHead.value = h;
      mat.uniforms.uTime.value = t;
      curve.getPointAt(clamp(h, 0.001, 0.999), headPos);
      light.position.set(headPos.x, headPos.y, headPos.z + 0.95);
      light.intensity = 0.14 + seg(p, 0.1, 0.4) * 0.16 + Math.sin(t * 1.7) * 0.015;
    },
    dispose() { geo.dispose(); mat.dispose(); },
  };
}

/* ---------- signal particles ---------- */
export function buildSignals(count) {
  const r = rng(1337);
  const start = new Float32Array(count * 3);
  const end = new Float32Array(count * 3);
  const seed = new Float32Array(count);
  for (let i = 0; i < count; i++) {
    const a = r() * Math.PI * 2, rad = 2.6 + r() * 3.4;
    start[i * 3] = Math.cos(a) * rad;
    start[i * 3 + 1] = -0.7 + r() * 2.4;
    start[i * 3 + 2] = Math.sin(a) * rad * 0.7 - 0.8;
    const a2 = r() * Math.PI * 2, rad2 = 1.55 + r() * 1.85;
    end[i * 3] = Math.cos(a2) * rad2 * 1.15;
    end[i * 3 + 1] = -0.5 + r() * 1.9;
    end[i * 3 + 2] = -0.55 - r() * 1.5;
    seed[i] = r();
  }
  const g = new THREE.BufferGeometry();
  g.setAttribute('position', new THREE.BufferAttribute(start.slice(), 3));
  g.setAttribute('aStart', new THREE.BufferAttribute(start, 3));
  g.setAttribute('aEnd', new THREE.BufferAttribute(end, 3));
  g.setAttribute('aSeed', new THREE.BufferAttribute(seed, 1));
  const mat = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: {
      uP: { value: 0 }, uTime: { value: 0 }, uScale: { value: 1 },
      uA: { value: new THREE.Color(C.burg) }, uB: { value: new THREE.Color(C.terra) },
    },
    vertexShader: `
      attribute vec3 aStart; attribute vec3 aEnd; attribute float aSeed;
      uniform float uP; uniform float uTime; uniform float uScale;
      varying float vA; varying float vS;
      void main(){
        float local = clamp((uP - aSeed * 0.35) * 1.6, 0.0, 1.0);
        float e = local * local * (3.0 - 2.0 * local);
        vec3 pos = mix(aStart, aEnd, e);
        float w = 0.05 + aSeed * 0.09;
        pos.x += sin(uTime * (0.3 + aSeed * 0.5) + aSeed * 40.0) * w;
        pos.y += cos(uTime * (0.25 + aSeed * 0.4) + aSeed * 22.0) * w * 0.8;
        vA = smoothstep(0.0, 0.12, uP) * (0.35 + 0.65 * e);
        vS = aSeed;
        vec4 mv = modelViewMatrix * vec4(pos, 1.0);
        gl_PointSize = (0.85 + aSeed * 1.15) * uScale * (1.0 / -mv.z) * 30.0;
        gl_Position = projectionMatrix * mv;
      }`,
    fragmentShader: `
      uniform vec3 uA; uniform vec3 uB; varying float vA; varying float vS;
      void main(){
        vec2 d = gl_PointCoord - 0.5;
        float m = smoothstep(0.5, 0.06, length(d));
        if (m < 0.01) discard;
        gl_FragColor = vec4(mix(uA, uB, vS), m * vA * 0.5);
      }`,
  });
  const pts = new THREE.Points(g, mat);
  pts.frustumCulled = false;
  return {
    pts,
    update(p, t, scale) {
      mat.uniforms.uP.value = seg(p, 0.10, 0.46) * 0.85 + seg(p, 0.46, 1) * 0.15;
      mat.uniforms.uTime.value = t;
      mat.uniforms.uScale.value = scale;
    },
    dispose() { g.dispose(); mat.dispose(); },
  };
}

/* ---------- rising performance trajectory ---------- */
export function buildTrajectory(count) {
  const r = rng(90210);
  const curve = new THREE.CatmullRomCurve3([
    new THREE.Vector3(0.1, -0.35, 0.1),
    new THREE.Vector3(0.9, 0.05, -0.2),
    new THREE.Vector3(1.8, 0.55, -0.5),
    new THREE.Vector3(2.8, 1.2, -0.9),
    new THREE.Vector3(3.9, 1.95, -1.4),
  ]);
  const pos = new Float32Array(count * 3);
  const seed = new Float32Array(count);
  const off = new Float32Array(count);
  for (let i = 0; i < count; i++) {
    const t = r();
    const pt = curve.getPointAt(t);
    const sp = 0.06 + t * 0.14;
    pos[i * 3] = pt.x + (r() - 0.5) * sp;
    pos[i * 3 + 1] = pt.y + (r() - 0.5) * sp;
    pos[i * 3 + 2] = pt.z + (r() - 0.5) * sp;
    seed[i] = r(); off[i] = t;
  }
  const g = new THREE.BufferGeometry();
  g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
  g.setAttribute('aSeed', new THREE.BufferAttribute(seed, 1));
  g.setAttribute('aT', new THREE.BufferAttribute(off, 1));
  const mat = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: {
      uP: { value: 0 }, uTime: { value: 0 }, uScale: { value: 1 },
      uA: { value: new THREE.Color(C.terra) }, uB: { value: new THREE.Color(C.cream) },
    },
    vertexShader: `
      attribute float aSeed; attribute float aT;
      uniform float uP; uniform float uTime; uniform float uScale;
      varying float vA; varying float vT;
      void main(){
        vec3 pos = position;
        float flow = fract(aSeed + uTime * 0.06);
        pos += normalize(vec3(1.0, 0.85, -0.5)) * flow * 0.22;
        pos.y += sin(uTime * 0.7 + aSeed * 30.0) * 0.02;
        vA = smoothstep(aT * 0.7, aT * 0.7 + 0.25, uP);
        vT = aT;
        vec4 mv = modelViewMatrix * vec4(pos, 1.0);
        gl_PointSize = (0.7 + aSeed * 1.1) * uScale * (1.0 / -mv.z) * 30.0;
        gl_Position = projectionMatrix * mv;
      }`,
    fragmentShader: `
      uniform vec3 uA; uniform vec3 uB; varying float vA; varying float vT;
      void main(){
        vec2 d = gl_PointCoord - 0.5;
        float m = smoothstep(0.5, 0.05, length(d));
        if (m < 0.01) discard;
        gl_FragColor = vec4(mix(uA, uB, vT), m * vA * 0.55);
      }`,
  });
  const pts = new THREE.Points(g, mat);
  pts.frustumCulled = false;
  return {
    pts,
    update(p, t, scale) {
      mat.uniforms.uP.value = seg(p, 0.74, 1.0);
      mat.uniforms.uTime.value = t;
      mat.uniforms.uScale.value = scale;
    },
    dispose() { g.dispose(); mat.dispose(); },
  };
}

/* ---------- orbits ---------- */
export function buildOrbits() {
  const specs = [
    { rx: 1.95, rz: 0.78, tilt: 0.30, spin: 0.06, col: C.crim, op: 0.3, from: 0.17, to: 0.32 },
    { rx: 2.4, rz: 1.0, tilt: -0.22, spin: -0.045, col: C.terra, op: 0.24, from: 0.40, to: 0.60 },
    { rx: 2.85, rz: 1.22, tilt: 0.12, spin: 0.032, col: C.crim, op: 0.18, from: 0.58, to: 0.78 },
  ];
  const group = new THREE.Group();
  const rings = specs.map((s) => {
    const curve = new THREE.EllipseCurve(0, 0, s.rx, s.rz, 0, Math.PI * 2);
    const pts = curve.getPoints(190).map((v) => new THREE.Vector3(v.x, 0, v.y));
    const g = new THREE.BufferGeometry().setFromPoints(pts);
    const m = new THREE.LineBasicMaterial({ color: new THREE.Color(s.col), transparent: true, opacity: 0, depthWrite: false, blending: THREE.AdditiveBlending });
    const line = new THREE.LineLoop(g, m);
    line.rotation.z = s.tilt;
    line.rotation.x = 1.28;
    const bg = new THREE.SphereGeometry(0.022, 12, 12);
    const bm = new THREE.MeshBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0 });
    const bead = new THREE.Mesh(bg, bm);
    line.add(bead);
    group.add(line);
    return { s, line, m, bead, bm, curve, g, bg };
  });
  return {
    group,
    update(p, t) {
      rings.forEach((r, i) => {
        const a = seg(p, r.s.from, r.s.to);
        r.m.opacity = a * r.s.op;
        r.bm.opacity = a * 0.95;
        r.line.rotation.y = t * r.s.spin + i * 1.1;
        const u = (t * (0.055 + i * 0.02) + i * 0.33) % 1;
        const pt = r.curve.getPointAt(u);
        r.bead.position.set(pt.x, 0, pt.y);
        r.bead.scale.setScalar(0.6 + a * 0.7);
      });
    },
    dispose() { rings.forEach((r) => { r.g.dispose(); r.m.dispose(); r.bg.dispose(); r.bm.dispose(); }); },
  };
}

/* ---------- strategy compass disc ---------- */
export function buildCompass() {
  const group = new THREE.Group();
  const disc = new THREE.Mesh(
    new THREE.CircleGeometry(0.42, 64),
    new THREE.MeshPhysicalMaterial({
      color: new THREE.Color(C.terra), transparent: true, opacity: 0,
      roughness: 0.18, metalness: 0, transmission: 0.85, thickness: 0.25,
      side: THREE.DoubleSide, depthWrite: false,
    })
  );
  const ringGeo = new THREE.TorusGeometry(0.42, 0.005, 8, 90);
  const ringMat = new THREE.MeshBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0 });
  const ring = new THREE.Mesh(ringGeo, ringMat);
  const inner = new THREE.Mesh(new THREE.TorusGeometry(0.24, 0.003, 8, 70), ringMat);
  const spokePts = [];
  for (let i = 0; i < 16; i++) {
    const a = (i / 16) * Math.PI * 2;
    const r0 = i % 4 === 0 ? 0.06 : 0.30;
    spokePts.push(new THREE.Vector3(Math.cos(a) * r0, Math.sin(a) * r0, 0));
    spokePts.push(new THREE.Vector3(Math.cos(a) * 0.40, Math.sin(a) * 0.40, 0));
  }
  const spokeGeo = new THREE.BufferGeometry().setFromPoints(spokePts);
  const spokeMat = new THREE.LineBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
  const spokes = new THREE.LineSegments(spokeGeo, spokeMat);
  group.add(disc, ring, inner, spokes);
  const A = new THREE.Vector3(-1.62, 0.36, 0.35);
  const B = new THREE.Vector3(-1.9, 0.52, -0.7);
  return {
    group,
    update(p, t) {
      const a = seg(p, 0.15, 0.30);
      const move = seg(p, 0.56, 0.80);
      disc.material.opacity = a * 0.42;
      ringMat.opacity = a * 0.75;
      spokeMat.opacity = a * 0.5;
      group.position.copy(A).lerp(B, move);
      group.rotation.z = t * 0.07;
      group.rotation.y = -0.35 + Math.sin(t * 0.3) * 0.05;
      group.scale.setScalar(0.72 + a * 0.28);
    },
    dispose() { disc.geometry.dispose(); disc.material.dispose(); ringGeo.dispose(); ringMat.dispose(); spokeGeo.dispose(); spokeMat.dispose(); },
  };
}

/* ---------- layered brand-grid planes ---------- */
export function buildGridPlanes() {
  const group = new THREE.Group();
  const cfg = [
    { w: 0.52, h: 0.72, x: -0.16, y: 0.0, z: 0.14, col: C.terra, o: 0.5 },
    { w: 0.46, h: 0.58, x: 0.06, y: -0.06, z: 0.0, col: C.cream, o: 0.34 },
    { w: 0.4, h: 0.44, x: 0.26, y: -0.13, z: -0.14, col: C.terra, o: 0.42 },
  ];
  const parts = cfg.map((c, i) => {
    const g = new THREE.PlaneGeometry(c.w, c.h);
    const m = new THREE.MeshPhysicalMaterial({
      color: new THREE.Color(c.col), transparent: true, opacity: 0,
      roughness: 0.12, metalness: 0, transmission: 0.9, thickness: 0.18,
      side: THREE.DoubleSide, depthWrite: false,
    });
    const mesh = new THREE.Mesh(g, m);
    mesh.position.set(c.x, c.y, c.z);
    const eg = new THREE.EdgesGeometry(g);
    const em = new THREE.LineBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
    mesh.add(new THREE.LineSegments(eg, em));
    group.add(mesh);
    return { mesh, m, em, g, eg, i, base: c };
  });
  const A = new THREE.Vector3(-1.18, -0.34, 0.62);
  const B = new THREE.Vector3(-1.42, -0.42, -0.45);
  return {
    group,
    update(p, t) {
      const a = seg(p, 0.36, 0.56);
      const move = seg(p, 0.58, 0.82);
      parts.forEach((q, i) => {
        const local = clamp((a - i * 0.12) / 0.7);
        q.m.opacity = local * q.base.o;
        q.em.opacity = local * 0.2;
        q.mesh.position.y = q.base.y + (1 - local) * -0.3;
        q.mesh.rotation.y = -0.42 + Math.sin(t * 0.25 + i) * 0.03;
      });
      group.position.copy(A).lerp(B, move);
      group.scale.setScalar(0.9 + a * 0.1);
    },
    dispose() { parts.forEach((q) => { q.g.dispose(); q.m.dispose(); q.eg.dispose(); q.em.dispose(); }); },
  };
}

/* ---------- cinematic content frame ---------- */
export function buildContentFrame() {
  const w = 0.92, h = 0.5, r = 0.05;
  const sh = new THREE.Shape();
  sh.moveTo(-w / 2 + r, -h / 2);
  sh.lineTo(w / 2 - r, -h / 2); sh.quadraticCurveTo(w / 2, -h / 2, w / 2, -h / 2 + r);
  sh.lineTo(w / 2, h / 2 - r); sh.quadraticCurveTo(w / 2, h / 2, w / 2 - r, h / 2);
  sh.lineTo(-w / 2 + r, h / 2); sh.quadraticCurveTo(-w / 2, h / 2, -w / 2, h / 2 - r);
  sh.lineTo(-w / 2, -h / 2 + r); sh.quadraticCurveTo(-w / 2, -h / 2, -w / 2 + r, -h / 2);
  const outline = new THREE.BufferGeometry().setFromPoints(sh.getPoints(90).map((v) => new THREE.Vector3(v.x, v.y, 0)));
  const oMat = new THREE.LineBasicMaterial({ color: new THREE.Color(C.terra), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
  const line = new THREE.LineLoop(outline, oMat);

  const glassGeo = new THREE.ShapeGeometry(sh);
  const glassMat = new THREE.MeshPhysicalMaterial({
    color: new THREE.Color(C.burg), transparent: true, opacity: 0,
    roughness: 0.1, transmission: 0.92, thickness: 0.1, side: THREE.DoubleSide, depthWrite: false,
  });
  const glass = new THREE.Mesh(glassGeo, glassMat);

  const N = 120;
  const wavePts = [];
  for (let i = 0; i < N; i++) {
    const x = -w / 2 + 0.07 + (i / (N - 1)) * (w - 0.14);
    wavePts.push(new THREE.Vector3(x, 0, 0.002));
  }
  const waveGeo = new THREE.BufferGeometry().setFromPoints(wavePts);
  const waveMat = new THREE.LineBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
  const wave = new THREE.Line(waveGeo, waveMat);

  const group = new THREE.Group();
  group.add(glass, line, wave);
  const A = new THREE.Vector3(0.3, 1.1, -0.3);
  const B = new THREE.Vector3(0.58, 1.2, -0.8);
  return {
    group,
    update(p, t) {
      const a = seg(p, 0.40, 0.58);
      const move = seg(p, 0.6, 0.84);
      oMat.opacity = a * 0.8;
      glassMat.opacity = a * 0.3;
      waveMat.opacity = a * 0.9;
      const arr = waveGeo.attributes.position;
      for (let i = 0; i < arr.count; i++) {
        const u = i / (arr.count - 1);
        arr.setY(i, Math.sin(u * 9.2 - t * 1.5) * 0.055 * (0.4 + 0.6 * Math.sin(u * Math.PI)));
      }
      arr.needsUpdate = true;
      group.position.copy(A).lerp(B, move);
      group.rotation.y = -0.3 + Math.sin(t * 0.22) * 0.03;
      group.scale.setScalar(0.8 + a * 0.2);
    },
    dispose() { outline.dispose(); oMat.dispose(); glassGeo.dispose(); glassMat.dispose(); waveGeo.dispose(); waveMat.dispose(); },
  };
}

/* ---------- campaign target ring ---------- */
export function buildTargetRing() {
  const group = new THREE.Group();
  const mat = new THREE.MeshBasicMaterial({ color: new THREE.Color(C.crim), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
  const creamMat = new THREE.MeshBasicMaterial({ color: new THREE.Color(C.cream), transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false });
  const geos = [];
  [0.44, 0.31, 0.18].forEach((r, i) => {
    const g = new THREE.TorusGeometry(r, 0.004, 8, 80);
    geos.push(g);
    group.add(new THREE.Mesh(g, i === 1 ? creamMat : mat));
  });
  const dot = new THREE.SphereGeometry(0.026, 14, 14);
  geos.push(dot);
  group.add(new THREE.Mesh(dot, creamMat));
  const tick = new THREE.SphereGeometry(0.014, 10, 10);
  geos.push(tick);
  for (let i = 0; i < 4; i++) {
    const m = new THREE.Mesh(tick, mat);
    const a = (i / 4) * Math.PI * 2 + Math.PI / 4;
    m.position.set(Math.cos(a) * 0.44, Math.sin(a) * 0.44, 0);
    group.add(m);
  }
  const A = new THREE.Vector3(1.82, 0.04, 0.1);
  const B = new THREE.Vector3(2.05, 0.14, -0.7);
  return {
    group,
    update(p, t) {
      const a = seg(p, 0.58, 0.76);
      mat.opacity = a * 0.72;
      creamMat.opacity = a * 0.9;
      group.position.copy(A).lerp(B, seg(p, 0.7, 0.9));
      group.rotation.z = -t * 0.12;
      group.rotation.y = 0.3 + Math.sin(t * 0.25) * 0.04;
      group.scale.setScalar(0.75 + a * 0.25);
    },
    dispose() { geos.forEach((g) => g.dispose()); mat.dispose(); creamMat.dispose(); },
  };
}

/* ---------- volumetric haze + halo ---------- */
export function buildHaze() {
  const group = new THREE.Group();
  const hazeTex = softSprite('rgba(122,18,48,1)', 256);
  const haloTex = softSprite('rgba(240,223,194,1)', 256);
  const haze = new THREE.Mesh(
    new THREE.PlaneGeometry(9, 5),
    new THREE.MeshBasicMaterial({ map: hazeTex, transparent: true, opacity: 0.32, blending: THREE.AdditiveBlending, depthWrite: false })
  );
  haze.position.set(-0.3, 0.1, -2.6);
  const halo = new THREE.Mesh(
    new THREE.PlaneGeometry(4.2, 3.0),
    new THREE.MeshBasicMaterial({ map: haloTex, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })
  );
  halo.position.set(0.05, 0.12, -1.15);
  group.add(haze, halo);
  return {
    group,
    update(p, t) {
      haze.material.opacity = 0.14 + seg(p, 0.2, 0.9) * 0.16 + Math.sin(t * 0.4) * 0.012;
      halo.material.opacity = seg(p, 0.72, 1) * 0.3;
    },
    dispose() { haze.geometry.dispose(); haze.material.dispose(); halo.geometry.dispose(); halo.material.dispose(); hazeTex.dispose(); haloTex.dispose(); },
  };
}
