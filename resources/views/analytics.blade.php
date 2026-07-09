<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analytics — @by0x_</title>
<style>
  :root{
    --bg: #08070c; --bg-soft: #0f0d16; --bg-card: #120f1c;
    --purple: #a855f7; --purple-deep:#6d28d9; --purple-glow: #c084fc;
    --green: #4ade80; --red:#f87171;
    --text: #f2eefc; --text-dim: #9a92ad; --text-faint:#5f5870;
    --line: rgba(168,85,247,0.16);
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    background:var(--bg); color:var(--text);
    font-family:'Inter',-apple-system,sans-serif;
    min-height:100vh; position:relative; overflow-x:hidden;
  }
  body::before{
    content:''; position:fixed; top:-15%; left:50%; transform:translateX(-50%);
    width:900px; height:900px;
    background:radial-gradient(circle, rgba(168,85,247,0.14) 0%, rgba(168,85,247,0) 65%);
    pointer-events:none; z-index:0;
  }
  .shell{ position:relative; z-index:1; max-width:900px; margin:0 auto; padding:32px 24px 60px; }

  /* ---- top bar ---- */
  .topbar{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:34px; flex-wrap:wrap; gap:14px;
  }
  .brand{ display:flex; align-items:center; gap:10px; }
  .brand .dot{
    width:10px;height:10px;border-radius:50%;
    background:var(--purple); box-shadow:0 0 10px var(--purple-glow);
  }
  .brand h1{ font-size:19px; font-weight:700; letter-spacing:-0.01em; }
  .brand .sub{ font-size:12px; color:var(--text-dim); margin-top:2px; }
  .nav-actions{ display:flex; gap:8px; }
  .nav-btn{
    font-family:'JetBrains Mono', monospace; font-size:12px;
    padding:9px 14px; border-radius:9px;
    border:1px solid rgba(168,85,247,0.2);
    background:var(--bg-soft); color:var(--text-dim);
    text-decoration:none; transition:.15s;
  }
  .nav-btn:hover{ border-color:var(--purple); color:var(--text); }
  .nav-btn.accent{
    background:linear-gradient(135deg, rgba(168,85,247,0.22), rgba(109,40,217,0.14));
    color:var(--purple-glow); border-color:rgba(168,85,247,0.4);
  }

  /* ---- stat cards ---- */
  .stats{
    display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr));
    gap:14px; margin-bottom:40px;
  }
  .stat-box{
    position:relative; overflow:hidden;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.14);
    border-left:2px solid var(--purple);
    border-radius:12px; padding:18px 18px 16px;
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .stat-box:hover{ transform:translateY(-2px); box-shadow:0 8px 24px rgba(168,85,247,0.12); }
  .stat-box .icon{ font-size:15px; opacity:.7; margin-bottom:10px; }
  .stat-box b{
    display:block; font-family:'Space Grotesk', sans-serif;
    font-size:30px; font-weight:700;
    background:linear-gradient(135deg, var(--text), var(--purple-glow));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    margin-bottom:4px; line-height:1;
  }
  .stat-box span{ font-size:12px; color:var(--text-dim); }

  h2.section-title{
    font-family:'JetBrains Mono', monospace; font-size:11px;
    text-transform:uppercase; letter-spacing:.12em; color:var(--text-dim);
    margin:0 0 16px; display:flex; align-items:center; gap:8px;
  }
  h2.section-title::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }

  /* ---- per link ranking ---- */
  .rank-list{ display:flex; flex-direction:column; gap:10px; margin-bottom:42px; }
  .rank-row{
    display:flex; align-items:center; gap:14px;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.1);
    border-radius:12px; padding:13px 16px;
  }
  .rank-num{
    font-family:'JetBrains Mono', monospace; font-size:11px; color:var(--text-faint);
    width:16px;
  }
  .rank-icon{
    width:32px; height:32px; min-width:32px; border-radius:9px;
    background:#1a1625; display:flex; align-items:center; justify-content:center; font-size:15px;
  }
  .rank-meta{ flex:1; min-width:0; }
  .rank-name{ font-size:13.5px; font-weight:600; margin-bottom:6px; }
  .bar-bg{ height:5px; background:#1a1625; border-radius:4px; overflow:hidden; }
  .bar-fill{
    height:100%; border-radius:4px; width:0%;
    background:linear-gradient(90deg, var(--purple-deep), var(--purple-glow));
    transition:width 1s cubic-bezier(.22,.9,.3,1);
    box-shadow:0 0 8px rgba(192,132,252,0.5);
  }
  .rank-count{
    font-family:'Space Grotesk', sans-serif; font-size:17px; font-weight:700;
    min-width:34px; text-align:right;
  }
  .rank-pct{ font-size:10px; color:var(--text-faint); text-align:right; margin-top:2px; }

  /* ---- recent activity timeline ---- */
  .timeline{ position:relative; padding-left:8px; }
  .timeline::before{
    content:''; position:absolute; left:15px; top:6px; bottom:6px; width:1px;
    background:linear-gradient(to bottom, transparent, var(--line) 8%, var(--line) 92%, transparent);
  }
  .tl-item{
    position:relative; display:flex; align-items:center; gap:14px;
    padding:11px 4px; z-index:1;
  }
  .tl-dot{
    width:30px; height:30px; min-width:30px; border-radius:50%;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.25);
    display:flex; align-items:center; justify-content:center; font-size:13px;
  }
  .tl-body{ flex:1; min-width:0; display:flex; justify-content:space-between; align-items:baseline; gap:10px; flex-wrap:wrap; }
  .tl-name{ font-size:13px; font-weight:600; }
  .tl-meta{ font-size:11px; color:var(--text-faint); font-family:'JetBrains Mono', monospace; white-space:nowrap; }

  .empty{
    text-align:center; padding:40px 20px; color:var(--text-faint);
    font-size:13px; background:var(--bg-card); border-radius:12px;
    border:1px dashed rgba(168,85,247,0.18);
  }

  footer.foot{ margin-top:48px; text-align:center; font-size:11px; color:var(--text-faint); font-family:'JetBrains Mono', monospace; }

  @media(max-width:520px){
    .stat-box b{ font-size:24px; }
  }
</style>
</head>
<body>

<div class="shell">

  <div class="topbar">
    <div class="brand">
      <div style="display:flex;align-items:center;gap:8px;">
        <span class="dot"></span>
        <div>
          <h1>Analytics</h1>
          <div class="sub">Link-in-bio click tracking — @by0x_</div>
        </div>
      </div>
    </div>
    <div class="nav-actions">
      <a class="nav-btn accent" href="/editor">✎ Edit Content</a>
      <a class="nav-btn" href="/">← Links</a>
      <a class="nav-btn" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
    </div>
  </div>

  @php
    $topLink = $clicksByLink->sortByDesc('total')->first();
    $todayCount = $recentClicks->filter(fn($r) => $r->created_at->isToday())->count();
    $max = $clicksByLink->max('total') ?: 0;
    $iconMap = [
      'x' => '𝕏', 'twitter' => '𝕏', 'discord' => '💬', 'github' => '⌘',
      'moove ambassador' => '🌙', 'chaintrinket' => '◈', 'email' => '✉',
      'telegram' => '✈', 'website/projects' => '◎',
    ];
    $iconFor = fn($name) => $iconMap[strtolower($name)] ?? '🔗';
  @endphp

  <div class="stats">
    <div class="stat-box">
      <div class="icon">👆</div>
      <b data-count="{{ $totalClicks }}">0</b>
      <span>Total Clicks</span>
    </div>
    <div class="stat-box">
      <div class="icon">🔗</div>
      <b data-count="{{ $clicksByLink->count() }}">0</b>
      <span>Unique Links</span>
    </div>
    <div class="stat-box">
      <div class="icon">📅</div>
      <b data-count="{{ $todayCount }}">0</b>
      <span>Today</span>
    </div>
    <div class="stat-box">
      <div class="icon">🏆</div>
      <b style="font-size:16px;">{{ $topLink->link_name ?? '—' }}</b>
      <span>Top Performer</span>
    </div>
  </div>

  <h2 class="section-title">Per Link</h2>
  @if($clicksByLink->count())
    <div class="rank-list">
      @foreach($clicksByLink->sortByDesc('total') as $i => $c)
        <div class="rank-row">
          <div class="rank-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
          <div class="rank-icon">{{ $iconFor($c->link_name) }}</div>
          <div class="rank-meta">
            <div class="rank-name">{{ $c->link_name }}</div>
            <div class="bar-bg"><div class="bar-fill" data-width="{{ $max > 0 ? ($c->total / $max) * 100 : 0 }}"></div></div>
          </div>
          <div>
            <div class="rank-count">{{ $c->total }}</div>
            <div class="rank-pct">{{ $totalClicks > 0 ? round(($c->total / $totalClicks) * 100) : 0 }}%</div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="empty" style="margin-bottom:42px;">Belum ada klik tercatat.</div>
  @endif

  <h2 class="section-title">Recent Activity</h2>
  @if($recentClicks->count())
    <div class="timeline">
      @foreach($recentClicks as $r)
        <div class="tl-item">
          <div class="tl-dot">{{ $iconFor($r->link_name) }}</div>
          <div class="tl-body">
            <div class="tl-name">{{ $r->link_name }}</div>
            <div class="tl-meta">{{ $r->created_at->diffForHumans() }} · {{ substr($r->ip, 0, 10) }}{{ strlen($r->ip) > 10 ? '…' : '' }}</div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="empty">Belum ada aktivitas.</div>
  @endif

  <footer class="foot">@by0x_ · analytics dashboard</footer>

  <form id="logout-form" method="POST" action="/logout" style="display:none;">@csrf</form>
</div>

<script>
  // count-up animation
  document.querySelectorAll('[data-count]').forEach(function(el){
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var current = 0;
    var step = Math.max(1, Math.ceil(target / 30));
    var timer = setInterval(function(){
      current += step;
      if (current >= target){ current = target; clearInterval(timer); }
      el.textContent = current;
    }, 25);
  });
  // bar fill animation
  window.addEventListener('DOMContentLoaded', function(){
    requestAnimationFrame(function(){
      document.querySelectorAll('.bar-fill').forEach(function(el){
        el.style.width = (el.getAttribute('data-width') || 0) + '%';
      });
    });
  });
</script>

</body>
</html>