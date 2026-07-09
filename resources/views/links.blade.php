<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@by0x_ — Links</title>
<meta name="description" content="Putra (@by0x_) — Web3 developer & Moove Ambassador. All links in one place.">

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- GA4 — ganti G-XXXXXXXXXX -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>

<style>
  :root{
    --bg: #08070c;
    --bg-soft: #0f0d16;
    --purple: #a855f7;
    --purple-deep: #6d28d9;
    --purple-glow: #c084fc;
    --text: #f2eefc;
    --text-dim: #9a92ad;
    --line: rgba(168,85,247,0.25);
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    background: var(--bg);
    color: var(--text);
    font-family: 'Inter', -apple-system, sans-serif;
    min-height: 100vh;
    display:flex;
    justify-content:center;
    overflow-x:hidden;
    position:relative;
  }
  #bg-canvas{
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    z-index:0;
    display:block;
  }
  body::before{
    content:'';
    position:fixed;
    top:-20%; left:50%; transform:translateX(-50%);
    width:900px; height:900px;
    background: radial-gradient(circle, rgba(168,85,247,0.20) 0%, rgba(168,85,247,0) 65%);
    pointer-events:none; z-index:0;
  }
  body::after{
    content:'';
    position:fixed;
    bottom:-25%; right:-10%;
    width:700px; height:700px;
    background: radial-gradient(circle, rgba(109,40,217,0.18) 0%, rgba(109,40,217,0) 70%);
    pointer-events:none; z-index:0;
  }
  .wrap{
    width:100%; max-width:440px;
    padding: 56px 24px 40px;
    position:relative; z-index:1;
  }
  .profile{
    display:flex; flex-direction:column; align-items:center; text-align:center;
    margin-bottom:38px;
  }
  .avatar{
    width:96px; height:96px; border-radius:50%; object-fit:cover;
    border:2px solid var(--purple);
    box-shadow: 0 0 0 6px rgba(168,85,247,0.08), 0 0 30px rgba(168,85,247,0.35);
    margin-bottom:18px; background:#1a1625;
  }
  .name{ font-size:22px; font-weight:700; }
  .handle{
    font-family:'JetBrains Mono', monospace; font-size:13px;
    color:var(--purple-glow); margin-top:4px;
  }
  .bio{
    font-size:14px; color:var(--text-dim); margin-top:12px;
    line-height:1.6; max-width:320px;
  }
  .section-label{
    font-family:'JetBrains Mono', monospace; font-size:11px;
    letter-spacing:0.12em; text-transform:uppercase;
    color:var(--text-dim); margin:26px 4px 10px;
    display:flex; align-items:center; gap:8px;
  }
  .section-label::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }
  .links{
    position:relative; display:flex; flex-direction:column; gap:12px;
  }
  .links::before{
    content:'';
    position:absolute; left:22px; top:6px; bottom:6px; width:1px;
    background: linear-gradient(to bottom, transparent, var(--line) 8%, var(--line) 92%, transparent);
    z-index:0;
  }
  .link-btn{
    position:relative; z-index:1;
    display:flex; align-items:center; gap:14px;
    padding:15px 18px;
    background:var(--bg-soft);
    border:1px solid rgba(168,85,247,0.18);
    border-radius:14px; text-decoration:none; color:var(--text);
    transition: border-color .2s ease, transform .15s ease, box-shadow .2s ease, background .2s ease;
    cursor:pointer;
  }
  .link-btn:hover{
    border-color:var(--purple);
    box-shadow: 0 0 0 1px var(--purple) inset, 0 0 24px rgba(168,85,247,0.25);
    transform: translateY(-2px); background:#141020;
  }
  .link-node{
    width:36px; height:36px; min-width:36px;
    border-radius:10px; background:#1a1625;
    display:flex; align-items:center; justify-content:center; font-size:17px;
  }
  .link-copy{ flex:1; min-width:0; }
  .link-title{ font-size:14.5px; font-weight:600; }
  .link-sub{
    font-size:12px; color:var(--text-dim);
    font-family:'JetBrains Mono', monospace; margin-top:2px;
  }
  .link-arrow{ color:var(--purple-glow); font-size:16px; opacity:0.6; }
  footer{
    margin-top:44px; text-align:center;
  }
  .footer-sig{
    font-family:'JetBrains Mono', monospace; font-size:11px; color:var(--text-dim);
  }
  .footer-sig span{ color:var(--purple-glow); }
</style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<div class="wrap">
  <div class="profile">
    <img class="avatar" src="https://via.placeholder.com/96/1a1625/a855f7?text=P" alt="Putra">
    <div class="name">Putra</div>
    <div class="handle">@by0x_</div>
    <div class="bio">Web2 → Web3 Developer. Moove Ambassador. Building in public.</div>
  </div>

  <div class="section-label">Highlight</div>
  <div class="links">
    <a class="link-btn" href="#" data-link="Moove Ambassador">
      <div class="link-node">🌙</div>
      <div class="link-copy">
        <div class="link-title">Moove Ambassador</div>
        <div class="link-sub">Official Ambassador Program</div>
      </div>
      <div class="link-arrow">→</div>
    </a>
  </div>

  <div class="section-label">Social</div>
  <div class="links">
    <a class="link-btn" href="https://x.com/by0x_" target="_blank" data-link="X">
      <div class="link-node">𝕏</div>
      <div class="link-copy">
        <div class="link-title">X (Twitter)</div>
        <div class="link-sub">@by0x_</div>
      </div>
    </a>
    <a class="link-btn" href="https://discord.gg/DTPJh5Bzp" target="_blank" data-link="Discord">
      <div class="link-node">💬</div>
      <div class="link-copy">
        <div class="link-title">Discord</div>
        <div class="link-sub">Join community</div>
      </div>
    </a>
  </div>

  <div class="section-label">Portfolio</div>
  <div class="links">
    <a class="link-btn" href="https://github.com/dhabyap" target="_blank" data-link="GitHub">
      <div class="link-node">⌘</div>
      <div class="link-copy">
        <div class="link-title">GitHub</div>
        <div class="link-sub">dhabyap</div>
      </div>
    </a>
    <a class="link-btn" href="https://dhabyap.github.io/chaintrinket/" target="_blank" data-link="ChainTrinket">
      <div class="link-node">◈</div>
      <div class="link-copy">
        <div class="link-title">ChainTrinket</div>
        <div class="link-sub">Physical auth on Stellar/Soroban</div>
      </div>
    </a>
  </div>

  <footer>
    <div class="footer-sig">built by <span>@by0x_</span> · 2026</div>
    <div class="footer-sig" style="margin-top:8px;font-size:10px;">
      @auth
        <a href="/analytics" style="color:var(--purple-glow);text-decoration:none;">dashboard</a>
      @else
        <a href="/login" style="color:var(--text-dim);text-decoration:none;">login</a>
      @endauth
    </div>
  </footer>
</div>

<script>
(function(){
  var canvas = document.getElementById('bg-canvas');
  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(60, window.innerWidth/window.innerHeight, 0.1, 1000);
  camera.position.z = 60;
  var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);

  var NODE_COUNT = 50;
  var nodes = [];
  var geo = new THREE.BufferGeometry();
  var pos = new Float32Array(NODE_COUNT*3);
  for(let i=0; i<NODE_COUNT; i++){
    nodes.push({x:(Math.random()-0.5)*80, y:(Math.random()-0.5)*80, z:(Math.random()-0.5)*30, vx:(Math.random()-0.5)*0.05, vy:(Math.random()-0.5)*0.05});
    pos[i*3]=nodes[i].x; pos[i*3+1]=nodes[i].y; pos[i*3+2]=nodes[i].z;
  }
  geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
  var mat = new THREE.PointsMaterial({color: 0xc084fc, size: 1.5, transparent:true, opacity:0.6});
  scene.add(new THREE.Points(geo, mat));

  function animate(){
    requestAnimationFrame(animate);
    for(let i=0; i<NODE_COUNT; i++){
      nodes[i].x += nodes[i].vx; nodes[i].y += nodes[i].vy;
      pos[i*3]=nodes[i].x; pos[i*3+1]=nodes[i].y;
      if(Math.abs(nodes[i].x)>40) nodes[i].vx*=-1;
      if(Math.abs(nodes[i].y)>40) nodes[i].vy*=-1;
    }
    geo.attributes.position.needsUpdate=true;
    renderer.render(scene, camera);
  }
  animate();
})();

// Track clicks — simpan ke DB + GA4
document.querySelectorAll('.link-btn').forEach(function(el){
  el.addEventListener('click', function(e){
    var name = el.getAttribute('data-link') || 'unknown';
    var url = el.getAttribute('href') || '';
    // GA4
    if(window.gtag) gtag('event', 'link_click', {link_name: name});
    // DB — cookie-based CSRF biar sendBeacon works
    var payload = JSON.stringify({link_name: name, link_url: url, source: document.referrer || ''});
    if(navigator.sendBeacon){
      navigator.sendBeacon('/track-click', new Blob([payload], {type:'application/json'}));
    } else {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '/track-click', true);
      xhr.setRequestHeader('Content-Type','application/json');
      xhr.send(payload);
    }
  });
});
</script>
</body>
</html>