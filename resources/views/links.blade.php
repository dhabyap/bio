<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $state['name'] }} — Links</title>
<meta name="description" content="{{ $state['name'] }} · {{ $state['handle'] }}. All links in one place.">

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<style>
  :root{
    --bg:#08070c; --bg-soft:#0f0d16;
    --purple:#a855f7; --purple-deep:#6d28d9; --purple-glow:#c084fc;
    --text:#f2eefc; --text-dim:#9a92ad;
    --line:rgba(168,85,247,0.25);
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    background:var(--bg); color:var(--text);
    font-family:'Inter',-apple-system,sans-serif;
    min-height:100vh; display:flex; justify-content:center;
    overflow-x:hidden; position:relative;
  }
  #bg-canvas{ position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; display:block; }

  @font-face{ font-family:'Space Grotesk'; src:local('Space Grotesk'); }

  body::before{
    content:''; position:fixed; top:-20%; left:50%; transform:translateX(-50%);
    width:900px; height:900px;
    background:radial-gradient(circle, rgba(168,85,247,0.20) 0%, transparent 65%);
    pointer-events:none; z-index:0;
  }
  body::after{
    content:''; position:fixed; bottom:-25%; right:-10%;
    width:700px; height:700px;
    background:radial-gradient(circle, rgba(109,40,217,0.18) 0%, transparent 70%);
    pointer-events:none; z-index:0;
  }

  .wrap{ width:100%; max-width:440px; padding:56px 24px 40px; position:relative; z-index:1; }

  .profile{ display:flex; flex-direction:column; align-items:center; text-align:center; margin-bottom:38px; }
  .avatar{
    width:96px; height:96px; border-radius:50%; object-fit:cover;
    border:2px solid var(--purple);
    box-shadow:0 0 0 6px rgba(168,85,247,0.08), 0 0 30px rgba(168,85,247,0.35);
    margin-bottom:18px; background:#1a1625;
  }
  .name{ font-family:'Space Grotesk',sans-serif; font-size:22px; font-weight:700; letter-spacing:-0.01em; }
  .handle{ font-family:'JetBrains Mono',monospace; font-size:13px; color:var(--purple-glow); margin-top:4px; }
  .bio{ font-size:14px; color:var(--text-dim); margin-top:12px; line-height:1.6; max-width:320px; }

  .section-label{
    font-family:'JetBrains Mono',monospace; font-size:11px; letter-spacing:0.12em;
    text-transform:uppercase; color:var(--text-dim);
    margin:26px 4px 10px; display:flex; align-items:center; gap:8px;
  }
  .section-label::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }

  .links{ position:relative; display:flex; flex-direction:column; gap:12px; }
  .links::before{
    content:''; position:absolute; left:22px; top:6px; bottom:6px; width:1px;
    background:linear-gradient(to bottom, transparent, var(--line) 8%, var(--line) 92%, transparent);
    z-index:0;
  }

  .link-btn{
    position:relative; z-index:1; display:flex; align-items:center; gap:14px;
    padding:15px 18px; background:var(--bg-soft);
    border:1px solid rgba(168,85,247,0.18); border-radius:14px;
    text-decoration:none; color:var(--text);
    transition:border-color .2s ease, transform .15s ease, box-shadow .2s ease, background .2s ease;
  }
  .link-btn:hover, .link-btn:focus-visible{
    border-color:var(--purple);
    box-shadow:0 0 0 1px var(--purple) inset, 0 0 24px rgba(168,85,247,0.25);
    transform:translateY(-2px); background:#141020;
  }
  .link-btn:focus-visible{ outline:2px solid var(--purple-glow); outline-offset:2px; }
  .link-btn.primary{
    background:linear-gradient(135deg, rgba(168,85,247,0.16), rgba(109,40,217,0.10));
    border-color:rgba(168,85,247,0.4);
  }

  .link-node{ width:36px; height:36px; min-width:36px; border-radius:10px; background:#1a1625; display:flex; align-items:center; justify-content:center; font-size:17px; }
  .link-copy{ flex:1; min-width:0; }
  .link-title{ font-size:14.5px; font-weight:600; line-height:1.3; }
  .link-sub{ font-size:12px; color:var(--text-dim); font-family:'JetBrains Mono',monospace; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .link-arrow{ color:var(--purple-glow); font-size:16px; opacity:0.6; }

  footer{ margin-top:44px; text-align:center; }
  .footer-sig{ font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--text-dim); letter-spacing:0.04em; }
  .footer-sig span{ color:var(--purple-glow); }

  @media(max-width:380px){ .wrap{ padding:44px 16px 32px; } .name{ font-size:20px; } }

  @media(prefers-reduced-motion:no-preference){
    .link-btn{ animation:rise .5s ease backwards; }
    .link-btn:nth-child(1){ animation-delay:.02s; }
    .link-btn:nth-child(2){ animation-delay:.06s; }
    .link-btn:nth-child(3){ animation-delay:.10s; }
    .link-btn:nth-child(4){ animation-delay:.14s; }
    .link-btn:nth-child(5){ animation-delay:.18s; }
    .link-btn:nth-child(6){ animation-delay:.22s; }
    .link-btn:nth-child(7){ animation-delay:.26s; }
  }
  @keyframes rise{ from{ opacity:0; transform:translateY(8px); } to{ opacity:1; transform:translateY(0); } }
</style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<div class="wrap">

  <div class="profile">
    <img class="avatar" src="{{ $state['avatar'] ?: 'https://via.placeholder.com/96/1a1625/a855f7?text=' }}" alt="{{ $state['name'] }}">
    <div class="name">{{ $state['name'] }}</div>
    <div class="handle">{{ $state['handle'] }}</div>
    @if(!empty($state['bio']))
      <div class="bio">{{ $state['bio'] }}</div>
    @endif
  </div>

  @foreach($sections as $section)
    @if(!empty($section['links']))
      <div class="section-label">{{ $section['label'] }}</div>
      <div class="links">
        @foreach($section['links'] as $link)
          <a class="link-btn{{ $loop->index === 0 && $section['key'] === 'highlight' ? ' primary' : '' }}"
             href="{{ $link['url'] }}"
             target="_blank" rel="noopener"
             onclick="trackClick('{{ $link['title'] }}','{{ $link['url'] }}')">
            <div class="link-node">{{ $link['icon'] ?? '→' }}</div>
            <div class="link-copy">
              <div class="link-title">{{ $link['title'] }}</div>
              @if(!empty($link['subtitle']))
                <div class="link-sub">{{ $link['subtitle'] }}</div>
              @endif
            </div>
            <div class="link-arrow">→</div>
          </a>
        @endforeach
      </div>
    @endif
  @endforeach

  <footer>
    <div class="footer-sig">built by <span>{{ $state['handle'] }}</span> · powered by Linkr</div>
  </footer>

</div>

<script>
(function(){
  var canvas = document.getElementById('bg-canvas');
  if(!window.THREE || !canvas) return;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(reduceMotion) return;

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.z = 60;
  var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);

  var isMobile = window.innerWidth < 600;
  var NODE_COUNT = isMobile ? 42 : 80;
  var SPREAD = 70;
  var LINK_DIST = isMobile ? 16 : 18;

  var nodes = [];
  var positions = new Float32Array(NODE_COUNT * 3);
  for(var i = 0; i < NODE_COUNT; i++){
    var p = new THREE.Vector3(
      (Math.random() - 0.5) * SPREAD,
      (Math.random() - 0.5) * SPREAD,
      (Math.random() - 0.5) * SPREAD * 0.6
    );
    nodes.push({ pos: p, vel: new THREE.Vector3((Math.random()-0.5)*0.02,(Math.random()-0.5)*0.02,(Math.random()-0.5)*0.02) });
    positions[i*3] = p.x; positions[i*3+1] = p.y; positions[i*3+2] = p.z;
  }

  var pointsGeo = new THREE.BufferGeometry();
  pointsGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  var pointsMat = new THREE.PointsMaterial({ color:0xc084fc, size:1.1, transparent:true, opacity:0.85, blending:THREE.AdditiveBlending, depthWrite:false });
  var pointCloud = new THREE.Points(pointsGeo, pointsMat);
  scene.add(pointCloud);

  var maxLines = NODE_COUNT * 6;
  var linePositions = new Float32Array(maxLines * 2 * 3);
  var lineGeo = new THREE.BufferGeometry();
  lineGeo.setAttribute('position', new THREE.BufferAttribute(linePositions, 3));
  var lineMat = new THREE.LineBasicMaterial({ color:0x8b5cf6, transparent:true, opacity:0.18, blending:THREE.AdditiveBlending });
  var lines = new THREE.LineSegments(lineGeo, lineMat);
  scene.add(lines);

  var group = new THREE.Group();
  group.add(pointCloud); group.add(lines);
  scene.add(group);

  var mouseX=0, mouseY=0;
  window.addEventListener('mousemove', function(e){
    mouseX = (e.clientX / window.innerWidth - 0.5);
    mouseY = (e.clientY / window.innerHeight - 0.5);
  });

  function updateNodes(){
    var posAttr = pointsGeo.attributes.position.array;
    for(var i=0; i<NODE_COUNT; i++){
      var n = nodes[i]; n.pos.add(n.vel);
      if(Math.abs(n.pos.x) > SPREAD/2) n.vel.x *= -1;
      if(Math.abs(n.pos.y) > SPREAD/2) n.vel.y *= -1;
      if(Math.abs(n.pos.z) > SPREAD*0.3) n.vel.z *= -1;
      posAttr[i*3]=n.pos.x; posAttr[i*3+1]=n.pos.y; posAttr[i*3+2]=n.pos.z;
    }
    pointsGeo.attributes.position.needsUpdate = true;

    var linePosAttr = lineGeo.attributes.position.array;
    var lineIdx = 0;
    for(var a=0; a<NODE_COUNT && lineIdx<maxLines; a++){
      for(var b=a+1; b<NODE_COUNT && lineIdx<maxLines; b++){
        var d = nodes[a].pos.distanceTo(nodes[b].pos);
        if(d < LINK_DIST){
          linePosAttr[lineIdx*6]=nodes[a].pos.x; linePosAttr[lineIdx*6+1]=nodes[a].pos.y; linePosAttr[lineIdx*6+2]=nodes[a].pos.z;
          linePosAttr[lineIdx*6+3]=nodes[b].pos.x; linePosAttr[lineIdx*6+4]=nodes[b].pos.y; linePosAttr[lineIdx*6+5]=nodes[b].pos.z;
          lineIdx++;
        }
      }
    }
    for(var k=lineIdx; k<maxLines; k++){
      linePosAttr[k*6]=0; linePosAttr[k*6+1]=0; linePosAttr[k*6+2]=0;
      linePosAttr[k*6+3]=0; linePosAttr[k*6+4]=0; linePosAttr[k*6+5]=0;
    }
    lineGeo.attributes.position.needsUpdate = true;
    lineGeo.setDrawRange(0, lineIdx * 2);
  }

  function animate(){
    requestAnimationFrame(animate);
    updateNodes();
    group.rotation.y += 0.0009;
    group.rotation.x += 0.0002;
    group.rotation.z += 0.0001;
    renderer.render(scene, camera);
  }
  animate();

  window.addEventListener('resize', function(){
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
})();
</script>

<script>
function trackClick(name, url){
  var payload = JSON.stringify({link_name: name, link_url: url, source: document.referrer || ''});
  navigator.sendBeacon('/track-click', new Blob([payload], {type: 'application/json'}));
}
</script>
</body>
</html>