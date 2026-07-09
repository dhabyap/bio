<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Linkr — Satu link, semua platform kamu</title>
<meta name="description" content="Alternatif Linktree self-hosted. Analytics penuh, branding bebas, tanpa watermark.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<style>
:root{
  --bg:#08070c;--bg-soft:#0f0d16;--bg-card:#120f1c;
  --purple:#a855f7;--purple-deep:#6d28d9;--purple-glow:#c084fc;
  --text:#f2eefc;--text-dim:#9a92ad;--text-faint:#5f5870;
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{background:var(--bg);color:var(--text);font-family:'Inter',-apple-system,sans-serif;position:relative;overflow-x:hidden}

/* NAV */
nav{position:fixed;top:0;left:0;right:0;z-index:50;display:flex;align-items:center;justify-content:space-between;padding:16px 32px;backdrop-filter:blur(10px);background:rgba(8,7,12,0.5);border-bottom:1px solid rgba(168,85,247,0.08);transition:all .3s}
nav.scrolled{background:rgba(8,7,12,0.86);border-bottom:1px solid rgba(168,85,247,0.22);padding:11px 32px}
.logo{display:flex;align-items:center;gap:11px;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:17px}
.logo-orb{position:relative;width:26px;height:26px}
.logo-orb i{position:absolute;width:6px;height:6px;border-radius:50%;background:var(--purple-glow);box-shadow:0 0 8px var(--purple-glow);top:50%;left:50%;margin:-3px 0 0 -3px;animation:orbit 3.2s linear infinite}
.logo-orb i:nth-child(2){animation-delay:-1.07s;opacity:.75}
.logo-orb i:nth-child(3){animation-delay:-2.14s;opacity:.5}
@keyframes orbit{from{transform:rotate(0) translateX(11px) rotate(0)}to{transform:rotate(360deg) translateX(11px) rotate(-360deg)}}
.nav-center{display:flex;align-items:center;gap:28px}
.nav-link{position:relative;font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:.03em;color:var(--text-dim);text-decoration:none;padding:6px 0;transition:color .15s}
.nav-link::after{content:'';position:absolute;left:0;bottom:0;height:1px;width:0;background:linear-gradient(90deg,var(--purple),var(--purple-glow));box-shadow:0 0 6px var(--purple-glow);transition:width .25s}
.nav-link:hover{color:var(--text)}
.nav-link:hover::after{width:100%}
.nav-actions{display:flex;align-items:center;gap:14px}
.btn-cta{position:relative;overflow:hidden;font-family:'JetBrains Mono',monospace;font-size:12.5px;font-weight:600;color:#fff;text-decoration:none;padding:10px 20px;border-radius:100px;background:linear-gradient(135deg,var(--purple-deep),var(--purple));box-shadow:0 0 0 1px rgba(168,85,247,0.4),0 6px 20px rgba(168,85,247,0.3);transition:transform .15s}
.btn-cta:hover{transform:translateY(-2px)}
.btn-cta::before{content:'';position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(120deg,transparent,rgba(255,255,255,0.35),transparent);transform:skewX(-20deg);animation:shimmer 3.2s ease-in-out infinite}
@keyframes shimmer{0%{left:-60%}45%{left:130%}100%{left:130%}}
.nav-toggle{display:none;flex-direction:column;gap:4px;background:none;border:none;cursor:pointer;padding:8px}
.nav-toggle span{width:18px;height:2px;background:var(--text-dim);border-radius:2px;transition:.2s}
.mobile-menu{position:fixed;top:0;right:-100%;width:78%;max-width:300px;height:100%;background:var(--bg-soft);border-left:1px solid rgba(168,85,247,0.2);z-index:60;padding:90px 28px 28px;display:flex;flex-direction:column;gap:22px;transition:right .3s}
.mobile-menu.open{right:0}
.mobile-menu a{font-family:'JetBrains Mono',monospace;font-size:14px;color:var(--text-dim);text-decoration:none}
.mobile-menu a:hover{color:var(--purple-glow)}
.mobile-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:55}
.mobile-overlay.open{display:block}
@media(max-width:760px){.nav-center{display:none}.nav-actions .nav-link{display:none}.nav-toggle{display:flex}}

/* HERO */
.hero{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:120px 24px 60px}
#bg-canvas{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;display:block}
.hero::before{content:'';position:fixed;top:-10%;left:50%;transform:translateX(-50%);width:900px;height:900px;background:radial-gradient(circle,rgba(168,85,247,0.16) 0%,rgba(168,85,247,0) 65%);pointer-events:none;z-index:0}
.hero-inner{position:relative;z-index:1;max-width:640px;text-align:center}
.badge{display:inline-flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--text-dim);padding:7px 16px;border:1px solid rgba(168,85,247,0.25);border-radius:20px;margin-bottom:26px}
.badge .dot{width:6px;height:6px;border-radius:50%;background:var(--purple);box-shadow:0 0 8px var(--purple-glow);animation:pulse 1.8s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
h1{font-family:'Space Grotesk',sans-serif;font-size:clamp(34px,6vw,56px);font-weight:700;letter-spacing:-0.02em;line-height:1.08;margin-bottom:20px}
h1 span{background:linear-gradient(135deg,#fff 20%,var(--purple-glow) 80%);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p.sub{font-size:15.5px;color:var(--text-dim);max-width:460px;margin:0 auto 34px;line-height:1.7}
.hero-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-bottom:60px}
.btn-secondary{font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--text-dim);text-decoration:none;padding:13px 24px;border-radius:100px;border:1px solid rgba(168,85,247,0.25);transition:.15s}
.btn-secondary:hover{border-color:var(--purple);color:var(--text)}
.btn-primary-lg{font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:600;color:#fff;text-decoration:none;padding:13px 26px;border-radius:100px;background:linear-gradient(135deg,var(--purple-deep),var(--purple));box-shadow:0 0 0 1px rgba(168,85,247,0.4),0 8px 28px rgba(168,85,247,0.35);transition:all .18s;display:inline-flex;align-items:center;gap:8px}
.btn-primary-lg:hover{transform:translateY(-2px);box-shadow:0 0 0 1px rgba(168,85,247,0.6),0 12px 34px rgba(168,85,247,0.5)}
.mock-wrap{display:flex;justify-content:center}
.mock-phone{width:100%;max-width:300px;background:#050408;border:1px solid rgba(168,85,247,0.25);border-radius:26px;padding:26px 20px 22px;box-shadow:0 0 60px rgba(168,85,247,0.15)}
.mock-avatar{width:56px;height:56px;border-radius:50%;margin:0 auto 12px;background:linear-gradient(135deg,var(--purple-deep),var(--purple-glow))}
.mock-name{text-align:center;font-size:14px;font-weight:700}
.mock-handle{text-align:center;font-family:'JetBrains Mono',monospace;font-size:10.5px;color:var(--purple-glow);margin-top:2px;margin-bottom:18px}
.mock-link{display:flex;align-items:center;gap:10px;padding:11px 13px;margin-bottom:8px;background:var(--bg-soft);border:1px solid rgba(168,85,247,0.14);border-radius:11px}
.mock-link .mn{width:24px;height:24px;border-radius:7px;background:#1a1625;display:flex;align-items:center;justify-content:center;font-size:11px}
.mock-link .mt{height:6px;border-radius:3px;background:rgba(242,238,252,0.14);flex:1}
.mock-link .mt.w1{max-width:70%}
.mock-link .mt.w2{max-width:45%}

/* FEATURES */
.section{position:relative;z-index:1;max-width:1040px;margin:0 auto;padding:90px 24px}
.section-head{text-align:center;margin-bottom:48px}
.section-label{font-family:'JetBrains Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:var(--purple-glow);margin-bottom:12px}
.section-head h2{font-family:'Space Grotesk',sans-serif;font-size:clamp(24px,4vw,34px);font-weight:700}
.section-head p{color:var(--text-dim);font-size:14px;margin-top:10px;max-width:480px;margin-left:auto;margin-right:auto}
.feature-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:18px}
.feature-card{background:var(--bg-card);border:1px solid rgba(168,85,247,0.12);border-radius:16px;padding:26px 22px;transition:all .18s}
.feature-card:hover{transform:translateY(-3px);border-color:rgba(168,85,247,0.4)}
.feature-icon{width:42px;height:42px;border-radius:11px;background:linear-gradient(135deg,rgba(168,85,247,0.25),rgba(109,40,217,0.15));display:flex;align-items:center;justify-content:center;font-size:19px;margin-bottom:16px}
.feature-card h3{font-size:15px;font-weight:700;margin-bottom:8px}
.feature-card p{font-size:13px;color:var(--text-dim);line-height:1.6}

/* HOW IT WORKS */
.steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px}
.step{text-align:center;padding:0 12px}
.step-num{width:38px;height:38px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:15px;background:var(--bg-card);border:1px solid rgba(168,85,247,0.3);color:var(--purple-glow)}
.step h3{font-size:14px;font-weight:700;margin-bottom:8px}
.step p{font-size:12.5px;color:var(--text-dim);line-height:1.6}

/* CTA BAND */
.cta-band{position:relative;z-index:1;margin:40px 24px 100px;max-width:900px;margin-left:auto;margin-right:auto;background:linear-gradient(135deg,rgba(168,85,247,0.14),rgba(109,40,217,0.08));border:1px solid rgba(168,85,247,0.25);border-radius:24px;padding:56px 32px;text-align:center}
.cta-band h2{font-family:'Space Grotesk',sans-serif;font-size:clamp(22px,4vw,30px);font-weight:700;margin-bottom:14px}
.cta-band p{color:var(--text-dim);font-size:14px;margin-bottom:28px}

/* FOOTER */
footer{position:relative;z-index:1;padding:0 24px 0}
.footer-trace{position:relative;height:1px;max-width:1040px;margin:0 auto;background:linear-gradient(90deg,transparent,rgba(168,85,247,0.35) 15%,rgba(168,85,247,0.35) 85%,transparent)}
.footer-trace::after{content:'';position:absolute;top:-1.5px;left:0;width:70px;height:4px;background:linear-gradient(90deg,transparent,var(--purple-glow),transparent);filter:blur(1px);box-shadow:0 0 10px var(--purple-glow);animation:trace-scan 4.5s linear infinite}
@keyframes trace-scan{0%{left:-8%}100%{left:104%}}
.trace-node{position:absolute;top:-3px;width:7px;height:7px;border-radius:50%;background:var(--bg);border:1px solid var(--purple);box-shadow:0 0 8px var(--purple-glow);animation:node-pulse 2.4s ease-in-out infinite}
.trace-node:nth-child(2){left:20%;animation-delay:.2s}
.trace-node:nth-child(3){left:50%;animation-delay:.8s}
.trace-node:nth-child(4){left:80%;animation-delay:1.4s}
@keyframes node-pulse{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.3)}}
.footer-inner{max-width:1040px;margin:0 auto;padding:56px 0 36px;display:grid;grid-template-columns:1.6fr 1fr 1fr;gap:40px}
.footer-brand p{font-size:12.5px;color:var(--text-dim);line-height:1.7;margin:12px 0 18px;max-width:280px}
.footer-social{display:flex;gap:10px}
.footer-social a{width:32px;height:32px;border-radius:9px;background:var(--bg-card);border:1px solid rgba(168,85,247,0.16);display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text-dim);text-decoration:none;transition:.15s}
.footer-social a:hover{border-color:var(--purple);color:var(--purple-glow);transform:translateY(-2px)}
.footer-col-title{font-family:'JetBrains Mono',monospace;font-size:10.5px;text-transform:uppercase;letter-spacing:.12em;color:var(--text-faint);margin-bottom:16px}
.footer-col a{display:block;font-size:13px;color:var(--text-dim);text-decoration:none;margin-bottom:12px;transition:.15s;width:fit-content}
.footer-col a:hover{color:var(--purple-glow);transform:translateX(2px)}
.footer-bottom{max-width:1040px;margin:0 auto;padding:22px 0 34px;border-top:1px solid rgba(168,85,247,0.08);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-bottom .copy{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-faint)}
.status-pill{display:inline-flex;align-items:center;gap:7px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-faint)}
.status-pill .sdot{width:6px;height:6px;border-radius:50%;background:#4ade80;box-shadow:0 0 7px #4ade80;animation:pulse 1.8s ease-in-out infinite}
@media(max-width:680px){.footer-inner{grid-template-columns:1fr;gap:30px}}
@media(max-width:720px){nav{padding:16px 18px}.nav-link{display:none}}
</style>
</head>
<body>
<canvas id="bg-canvas"></canvas>

<nav id="mainNav">
  <div class="logo"><span class="logo-orb"><i></i><i></i><i></i></span> Linkr</div>
  <div class="nav-center">
    <a class="nav-link" href="#features">Fitur</a>
    <a class="nav-link" href="#how">Cara kerja</a>
    <a class="nav-link" href="#preview">Contoh</a>
  </div>
  <div class="nav-actions">
    <a class="nav-link" href="{{ url('/login') }}">Login</a>
    <a class="btn-cta" href="{{ url('/register') }}">Buat halaman gratis</a>
    <button class="nav-toggle" id="navToggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>
<div class="mobile-overlay" id="mobileOverlay"></div>
<div class="mobile-menu" id="mobileMenu">
  <a href="#features">Fitur</a>
  <a href="#how">Cara kerja</a>
  <a href="#preview">Contoh</a>
  <a href="{{ url('/login') }}">Login</a>
  <a class="btn-cta" href="{{ url('/register') }}" style="text-align:center">Buat halaman gratis</a>
</div>

<section class="hero">
  <div class="hero-inner">
    <div class="badge"><span class="dot"></span> Self-hosted link-in-bio</div>
    <h1>Satu link untuk <span>semua platform</span> kamu</h1>
    <p class="sub">Alternatif Linktree yang lebih bebas — kontrol penuh atas desain, analytics detail per klik, dan halaman kamu sendiri tanpa watermark.</p>
    <div class="hero-actions">
      <a class="btn-primary-lg" href="{{ url('/register') }}">Buat halaman kamu →</a>
      <a class="btn-secondary" href="#preview">Lihat contoh</a>
    </div>
    <div class="mock-wrap" id="preview">
      <div class="mock-phone">
        <div class="mock-avatar"></div>
        <div class="mock-name">Nama Kamu</div>
        <div class="mock-handle">@handle</div>
        <div class="mock-link"><div class="mn">🌐</div><div class="mt w1"></div></div>
        <div class="mock-link"><div class="mn">𝕏</div><div class="mt w2"></div></div>
        <div class="mock-link"><div class="mn">💬</div><div class="mt w1"></div></div>
        <div class="mock-link"><div class="mn">◈</div><div class="mt w2"></div></div>
      </div>
    </div>
  </div>
</section>

<section class="section" id="features">
  <div class="section-head">
    <div class="section-label">Kenapa Linkr</div>
    <h2>Bukan sekadar kumpulan link</h2>
    <p>Dibuat buat siapa aja yang mau halaman profil sendiri — tanpa batasan platform.</p>
  </div>
  <div class="feature-grid">
    <div class="feature-card">
      <div class="feature-icon">📊</div>
      <h3>Analytics penuh</h3>
      <p>Total klik, per-link breakdown, traffic source, trend harian — semua kelihatan.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🎨</div>
      <h3>Branding bebas</h3>
      <p>Warna, font, layout — semua bisa disesuaikan. Ga keliatan generic.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">⚡</div>
      <h3>Ringan & cepat</h3>
      <p>Static-first, load instan. Ga ada bloat script pihak ketiga.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🌐</div>
      <h3>Custom domain</h3>
      <p>Pakai domain sendiri, lebih profesional buat kolaborasi & audiens.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">✎</div>
      <h3>Edit real-time</h3>
      <p>Ubah profil & link dari dashboard, langsung lihat preview-nya.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔒</div>
      <h3>Punya kamu sepenuhnya</h3>
      <p>Self-hosted — data & desain kamu, bukan di platform pihak ketiga.</p>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="section-head">
    <div class="section-label">Cara kerja</div>
    <h2>Tiga langkah, langsung jadi</h2>
  </div>
  <div class="steps">
    <div class="step">
      <div class="step-num">1</div>
      <h3>Buat akun</h3>
      <p>Daftar dan pilih handle buat halaman kamu.</p>
    </div>
    <div class="step">
      <div class="step-num">2</div>
      <h3>Susun link kamu</h3>
      <p>Tambah profil, bio, dan link — atur urutan sesuka hati.</p>
    </div>
    <div class="step">
      <div class="step-num">3</div>
      <h3>Bagikan</h3>
      <p>Taro di bio sosmed kamu, mulai pantau klik dari dashboard.</p>
    </div>
  </div>
</section>

<div class="cta-band">
  <h2>Siap ganti dari Linktree?</h2>
  <p>Gratis buat mulai. Ga ada watermark, ga ada batasan link.</p>
  <a class="btn-primary-lg" href="{{ url('/register') }}">Buat halaman gratis →</a>
</div>

<footer>
  <div class="footer-trace">
    <i class="trace-node"></i><i class="trace-node"></i><i class="trace-node"></i>
  </div>
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="logo"><span class="logo-orb"><i></i><i></i><i></i></span> Linkr</div>
      <p>Self-hosted link-in-bio. Kontrol penuh atas desain, data, dan analytics.</p>
      <div class="footer-social">
        <a href="#" aria-label="X">𝕏</a>
        <a href="#" aria-label="GitHub">⌘</a>
        <a href="#" aria-label="Discord">💬</a>
      </div>
    </div>
    <div class="footer-col">
      <div class="footer-col-title">Product</div>
      <a href="#features">Fitur</a>
      <a href="#how">Cara kerja</a>
      <a href="#preview">Contoh</a>
      <a href="{{ url('/register') }}">Mulai gratis</a>
    </div>
    <div class="footer-col">
      <div class="footer-col-title">Resources</div>
      <a href="#">Docs</a>
      <a href="#">Changelog</a>
      <a href="#">Status</a>
      <a href="{{ url('/login') }}">Login</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span class="copy">© 2026 Linkr — built by @by0x_</span>
    <span class="status-pill"><span class="sdot"></span> all systems operational</span>
  </div>
</footer>

<script>
var mainNav=document.getElementById('mainNav');
window.addEventListener('scroll',function(){mainNav.classList.toggle('scrolled',window.scrollY>20)});
var navToggle=document.getElementById('navToggle'),mobileMenu=document.getElementById('mobileMenu'),mobileOverlay=document.getElementById('mobileOverlay');
function closeMenu(){mobileMenu.classList.remove('open');mobileOverlay.classList.remove('open')}
navToggle.addEventListener('click',function(){mobileMenu.classList.toggle('open');mobileOverlay.classList.toggle('open')});
mobileOverlay.addEventListener('click',closeMenu);
mobileMenu.querySelectorAll('a').forEach(function(a){a.addEventListener('click',closeMenu)});

// Three.js background
var bgCanvas=document.getElementById('bg-canvas');
var renderer=new THREE.WebGLRenderer({canvas:bgCanvas,alpha:true,antialias:true});
renderer.setSize(window.innerWidth,window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio,2));
var scene=new THREE.Scene();
var camera=new THREE.PerspectiveCamera(60,window.innerWidth/window.innerHeight,0.1,1000);
camera.position.z=30;
var particles=[];
for(var i=0;i<200;i++){
  var geo=new THREE.SphereGeometry(0.08,8,8);
  var mat=new THREE.MeshBasicMaterial({color:0xc084fc,transparent:true,opacity:Math.random()*0.5+0.1});
  var p=new THREE.Mesh(geo,mat);
  p.position.set((Math.random()-0.5)*60,(Math.random()-0.5)*40,(Math.random()-0.5)*30);
  scene.add(p);particles.push(p);
}
function animate(){
  requestAnimationFrame(animate);
  particles.forEach(function(p){p.position.y+=0.003;if(p.position.y>20)p.position.y=-20;});
  camera.position.x=Math.sin(Date.now()*0.0001)*2;
  renderer.render(scene,camera);
}
animate();
window.addEventListener('resize',function(){
  camera.aspect=window.innerWidth/window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth,window.innerHeight);
});
</script>
</body>
</html>