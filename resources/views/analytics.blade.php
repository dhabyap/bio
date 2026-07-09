<!--
  ASUMSI DATA TAMBAHAN dari controller (selain totalClicks, clicksByLink, recentClicks):

  1. $range (string, default 'all') — dari request('range','all'), lalu query clicksByLink &
     recentClicks difilter sesuai ini di controller. Value: 'today' | '7d' | '30d' | 'all'.

  2. $dailyClicks (Collection|array, opsional) — [ ['date' => '2026-07-01', 'total' => 5], ... ]
     buat trend chart. Kalau belum ada di controller, chart fallback ngitung dari $recentClicks
     yang ke-load (kurang akurat kalau recentClicks cuma nampilin beberapa baris terakhir —
     idealnya recentClicks query-nya diperluas atau bikin query groupBy(date) terpisah).

  3. $trafficSources (Collection|array, opsional) — [ ['source' => 'x.com', 'total' => 8], ... ]
     Butuh capture `document.referrer` pas hit /track-click di links.blade.php, simpan ke
     kolom baru `source` di tabel link_clicks. Kalau variabel ini gak dikirim, section-nya
     nampilin empty state + instruksi, gak error.

  4. Route POST /analytics/clear-test (opsional) — buat tombol "Clear test data".
     Kalau belum ada, tinggal bikin controller yang delete row dengan link_name tertentu
     (misal 'test','v') atau semua data — sesuaikan sama kebutuhan lo.
-->
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
  .shell{ position:relative; z-index:1; max-width:960px; margin:0 auto; padding:32px 24px 60px; }

  .topbar{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:22px; flex-wrap:wrap; gap:14px;
  }
  .brand{ display:flex; align-items:center; gap:10px; }
  .brand .dot{ width:10px;height:10px;border-radius:50%; background:var(--purple); box-shadow:0 0 10px var(--purple-glow); }
  .brand h1{ font-size:19px; font-weight:700; letter-spacing:-0.01em; }
  .brand .sub{ font-size:12px; color:var(--text-dim); margin-top:2px; }
  .nav-actions{ display:flex; gap:8px; }
  .nav-btn{
    font-family:'JetBrains Mono', monospace; font-size:12px;
    padding:9px 14px; border-radius:9px; border:1px solid rgba(168,85,247,0.2);
    background:var(--bg-soft); color:var(--text-dim); text-decoration:none; transition:.15s;
    cursor:pointer;
  }
  .nav-btn:hover{ border-color:var(--purple); color:var(--text); }
  .nav-btn.accent{
    background:linear-gradient(135deg, rgba(168,85,247,0.22), rgba(109,40,217,0.14));
    color:var(--purple-glow); border-color:rgba(168,85,247,0.4);
  }

  .range-tabs{ display:flex; gap:6px; margin-bottom:28px; flex-wrap:wrap; }
  .range-tab{
    font-family:'JetBrains Mono', monospace; font-size:11.5px;
    padding:7px 13px; border-radius:20px; border:1px solid rgba(168,85,247,0.16);
    color:var(--text-dim); text-decoration:none; transition:.15s;
  }
  .range-tab:hover{ border-color:var(--purple); color:var(--text); }
  .range-tab.active{ background:var(--purple); border-color:var(--purple); color:#fff; font-weight:600; }

  .stats{
    display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr));
    gap:14px; margin-bottom:36px;
  }
  .stat-box{
    position:relative; overflow:hidden;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.14);
    border-left:2px solid var(--purple); border-radius:12px; padding:18px 18px 16px;
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .stat-box:hover{ transform:translateY(-2px); box-shadow:0 8px 24px rgba(168,85,247,0.12); }
  .stat-box .icon{ font-size:15px; opacity:.7; margin-bottom:10px; }
  .stat-box b{
    display:block; font-family:'Space Grotesk', sans-serif; font-size:30px; font-weight:700;
    background:linear-gradient(135deg, var(--text), var(--purple-glow));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    margin-bottom:4px; line-height:1;
  }
  .stat-box span{ font-size:12px; color:var(--text-dim); }

  h2.section-title{
    font-family:'JetBrains Mono', monospace; font-size:11px; text-transform:uppercase;
    letter-spacing:.12em; color:var(--text-dim); margin:0 0 16px;
    display:flex; align-items:center; justify-content:space-between; gap:8px;
  }
  h2.section-title .label{ display:flex; align-items:center; gap:8px; }
  h2.section-title .label::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }

  .chart-card{
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.12);
    border-radius:14px; padding:20px; margin-bottom:40px;
  }
  .chart-empty{ text-align:center; padding:30px; color:var(--text-faint); font-size:12px; }
  canvas#trendChart{ width:100%; height:160px; display:block; }

  .rank-list{ display:flex; flex-direction:column; gap:10px; margin-bottom:42px; }
  .rank-row{
    display:flex; align-items:center; gap:14px;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.1);
    border-radius:12px; padding:13px 16px;
  }
  .rank-num{ font-family:'JetBrains Mono', monospace; font-size:11px; color:var(--text-faint); width:16px; }
  .rank-icon{ width:32px; height:32px; min-width:32px; border-radius:9px; background:#1a1625; display:flex; align-items:center; justify-content:center; font-size:15px; }
  .rank-meta{ flex:1; min-width:0; }
  .rank-name{ font-size:13.5px; font-weight:600; margin-bottom:6px; }
  .bar-bg{ height:5px; background:#1a1625; border-radius:4px; overflow:hidden; }
  .bar-fill{
    height:100%; border-radius:4px; width:0%;
    background:linear-gradient(90deg, var(--purple-deep), var(--purple-glow));
    transition:width 1s cubic-bezier(.22,.9,.3,1); box-shadow:0 0 8px rgba(192,132,252,0.5);
  }
  .rank-count{ font-family:'Space Grotesk', sans-serif; font-size:17px; font-weight:700; min-width:34px; text-align:right; }
  .rank-pct{ font-size:10px; color:var(--text-faint); text-align:right; margin-top:2px; }

  .source-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:10px; margin-bottom:42px; }
  .source-box{ background:var(--bg-card); border:1px solid rgba(168,85,247,0.1); border-radius:10px; padding:14px 16px; }
  .source-name{ font-size:12.5px; font-weight:600; margin-bottom:8px; display:flex; align-items:center; gap:6px; }
  .source-count{ font-family:'Space Grotesk', sans-serif; font-size:20px; font-weight:700; color:var(--purple-glow); }

  .recent-toolbar{ display:flex; gap:8px; margin-bottom:14px; }
  .search-input{
    flex:1; padding:9px 13px; border-radius:9px; border:1px solid rgba(168,85,247,0.16);
    background:var(--bg-card); color:var(--text); font-size:12.5px; outline:none;
    font-family:'Inter',sans-serif;
  }
  .search-input:focus{ border-color:var(--purple); }
  .icon-btn{
    padding:9px 14px; border-radius:9px; border:1px solid rgba(168,85,247,0.16);
    background:var(--bg-card); color:var(--text-dim); font-size:12px; cursor:pointer;
    font-family:'JetBrains Mono', monospace; white-space:nowrap;
  }
  .icon-btn:hover{ border-color:var(--purple); color:var(--text); }

  .timeline{ position:relative; padding-left:8px; }
  .timeline::before{
    content:''; position:absolute; left:15px; top:6px; bottom:6px; width:1px;
    background:linear-gradient(to bottom, transparent, var(--line) 8%, var(--line) 92%, transparent);
  }
  .tl-item{ position:relative; display:flex; align-items:center; gap:14px; padding:11px 4px; z-index:1; }
  .tl-dot{
    width:30px; height:30px; min-width:30px; border-radius:50%; background:var(--bg-card);
    border:1px solid rgba(168,85,247,0.25); display:flex; align-items:center; justify-content:center; font-size:13px;
  }
  .tl-body{ flex:1; min-width:0; display:flex; justify-content:space-between; align-items:baseline; gap:10px; flex-wrap:wrap; }
  .tl-name{ font-size:13px; font-weight:600; }
  .tl-meta{ font-size:11px; color:var(--text-faint); font-family:'JetBrains Mono', monospace; white-space:nowrap; }
  .tl-item.hidden-row{ display:none; }

  .load-more{
    display:block; margin:16px auto 0; padding:9px 20px; border-radius:20px;
    border:1px solid rgba(168,85,247,0.2); background:var(--bg-card); color:var(--text-dim);
    font-size:12px; cursor:pointer; font-family:'JetBrains Mono', monospace;
  }
  .load-more:hover{ border-color:var(--purple); color:var(--text); }

  .empty{
    text-align:center; padding:36px 20px; color:var(--text-faint); font-size:13px;
    background:var(--bg-card); border-radius:12px; border:1px dashed rgba(168,85,247,0.18);
  }
  .empty .hint{ font-size:11px; margin-top:6px; color:var(--text-faint); opacity:.7; }

  footer.foot{ margin-top:48px; text-align:center; font-size:11px; color:var(--text-faint); font-family:'JetBrains Mono', monospace; }

  @media(max-width:520px){ .stat-box b{ font-size:24px; } }
</style>
</head>
<body>

<div class="shell">

  @if(session('status'))
    <div style="background:rgba(74,222,128,0.1);color:#4ade80;padding:12px;border-radius:8px;margin-bottom:20px;font-size:13px;border:1px solid rgba(74,222,128,0.2)">
      {{ session('status') }}
    </div>
  @endif

  <div class="topbar">
    <div class="brand">
      <span class="dot"></span>
      <div>
        <h1>Analytics</h1>
        <div class="sub">Link-in-bio click tracking — @by0x_</div>
      </div>
    </div>
    <div class="nav-actions">
      <a class="nav-btn accent" href="/editor">✎ Edit Content</a>
      <a class="nav-btn" href="/">← Links</a>
      <a class="nav-btn" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
    </div>
  </div>

  @php
    $range = $range ?? 'all';
    $rangeLabels = ['today' => 'Hari ini', '7d' => '7 hari', '30d' => '30 hari', 'all' => 'Semua'];
    $topLink = $clicksByLink->sortByDesc('total')->first();
    $todayCount = $recentClicks->filter(fn($r) => $r->created_at->isToday())->count();
    $max = $clicksByLink->max('total') ?: 0;
    $iconMap = [
      'x' => '𝕏', 'twitter' => '𝕏', 'discord' => '💬', 'github' => '⌘',
      'moove ambassador' => '🌙', 'chaintrinket' => '◈', 'email' => '✉',
      'telegram' => '✈', 'website/projects' => '◎',
    ];
    $iconFor = fn($name) => $iconMap[strtolower($name)] ?? '🔗';

    $trend = collect($dailyClicks ?? $recentClicks->groupBy(fn($r) => $r->created_at->format('Y-m-d'))
      ->map(fn($g, $date) => ['date' => $date, 'total' => $g->count()])->values());
  @endphp

  <div class="range-tabs">
    @foreach($rangeLabels as $key => $label)
      <a class="range-tab {{ $range === $key ? 'active' : '' }}" href="?range={{ $key }}">{{ $label }}</a>
    @endforeach
  </div>

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

  <h2 class="section-title"><span class="label">Trend</span></h2>
  <div class="chart-card">
    @if($trend->count() >= 2)
      <canvas id="trendChart"></canvas>
    @else
      <div class="chart-empty">Belum cukup data buat trend chart (minimal 2 hari data).</div>
    @endif
  </div>

  <h2 class="section-title"><span class="label">Per Link</span></h2>
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

  <h2 class="section-title"><span class="label">Traffic Source</span></h2>
  @if(!empty($trafficSources) && collect($trafficSources)->count())
    <div class="source-grid">
      @foreach(collect($trafficSources)->sortByDesc('total') as $s)
        <div class="source-box">
          <div class="source-name">🌐 {{ $s['source'] ?? $s->source }}</div>
          <div class="source-count">{{ $s['total'] ?? $s->total }}</div>
        </div>
      @endforeach
    </div>
  @else
    <div class="empty" style="margin-bottom:42px;">
      Belum ada data traffic source.
      <div class="hint">Butuh capture <code>document.referrer</code> pas /track-click & kolom "source" di tabel klik.</div>
    </div>
  @endif

  <h2 class="section-title">
    <span class="label">Recent Activity</span>
    <button class="icon-btn" id="clearTestBtn" type="button">🧹 Clear test data</button>
  </h2>

  @if($recentClicks->count())
    <div class="recent-toolbar">
      <input class="search-input" id="searchRecent" placeholder="Cari nama link...">
      <button class="icon-btn" id="exportCsvBtn" type="button">⬇ Export CSV</button>
    </div>
    <div class="timeline" id="timelineList">
      @foreach($recentClicks as $i => $r)
        <div class="tl-item {{ $i >= 10 ? 'hidden-row' : '' }}" data-name="{{ strtolower($r->link_name) }}">
          <div class="tl-dot">{{ $iconFor($r->link_name) }}</div>
          <div class="tl-body">
            <div class="tl-name">{{ $r->link_name }}</div>
            <div class="tl-meta">{{ $r->created_at->diffForHumans() }} · {{ substr($r->ip, 0, 10) }}{{ strlen($r->ip) > 10 ? '…' : '' }}</div>
          </div>
        </div>
      @endforeach
    </div>
    @if($recentClicks->count() > 10)
      <button class="load-more" id="loadMoreBtn">Load more ({{ $recentClicks->count() - 10 }} lagi)</button>
    @endif
  @else
    <div class="empty">Belum ada aktivitas.</div>
  @endif

  <footer class="foot">@by0x_ · analytics dashboard</footer>

  <form id="logout-form" method="POST" action="/logout" style="display:none;">@csrf</form>
  <form id="clearTestForm" method="POST" action="/analytics/clear-test" style="display:none;">@csrf</form>
</div>

<script>
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

  window.addEventListener('DOMContentLoaded', function(){
    requestAnimationFrame(function(){
      document.querySelectorAll('.bar-fill').forEach(function(el){
        el.style.width = (el.getAttribute('data-width') || 0) + '%';
      });
    });
  });

  var trendData = @json($trend->values());
  var canvas = document.getElementById('trendChart');
  if (canvas && trendData.length >= 2){
    var ctx = canvas.getContext('2d');
    function draw(){
      var dpr = window.devicePixelRatio || 1;
      var w = canvas.clientWidth, h = 160;
      canvas.width = w * dpr; canvas.height = h * dpr;
      ctx.setTransform(dpr,0,0,dpr,0,0);
      ctx.clearRect(0,0,w,h);

      var pad = 20;
      var max = Math.max.apply(null, trendData.map(function(d){ return d.total; })) || 1;
      var stepX = (w - pad*2) / (trendData.length - 1);

      var pts = trendData.map(function(d,i){
        return { x: pad + i*stepX, y: h - pad - (d.total/max)*(h-pad*2) };
      });

      ctx.beginPath();
      ctx.moveTo(pts[0].x, h-pad);
      pts.forEach(function(p){ ctx.lineTo(p.x, p.y); });
      ctx.lineTo(pts[pts.length-1].x, h-pad);
      ctx.closePath();
      var grad = ctx.createLinearGradient(0,0,0,h);
      grad.addColorStop(0, 'rgba(168,85,247,0.35)');
      grad.addColorStop(1, 'rgba(168,85,247,0)');
      ctx.fillStyle = grad;
      ctx.fill();

      ctx.beginPath();
      pts.forEach(function(p,i){ i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y); });
      ctx.strokeStyle = '#c084fc';
      ctx.lineWidth = 2;
      ctx.shadowColor = 'rgba(192,132,252,0.6)';
      ctx.shadowBlur = 8;
      ctx.stroke();
      ctx.shadowBlur = 0;

      pts.forEach(function(p){
        ctx.beginPath();
        ctx.arc(p.x, p.y, 3, 0, Math.PI*2);
        ctx.fillStyle = '#f2eefc';
        ctx.fill();
      });
    }
    draw();
    window.addEventListener('resize', draw);
  }

  var searchInput = document.getElementById('searchRecent');
  if (searchInput){
    searchInput.addEventListener('input', function(){
      var q = searchInput.value.toLowerCase();
      document.querySelectorAll('#timelineList .tl-item').forEach(function(el){
        var name = el.getAttribute('data-name') || '';
        el.style.display = name.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  }

  var loadMoreBtn = document.getElementById('loadMoreBtn');
  if (loadMoreBtn){
    loadMoreBtn.addEventListener('click', function(){
      document.querySelectorAll('#timelineList .hidden-row').forEach(function(el){
        el.classList.remove('hidden-row');
      });
      loadMoreBtn.style.display = 'none';
    });
  }

  var exportBtn = document.getElementById('exportCsvBtn');
  if (exportBtn){
    exportBtn.addEventListener('click', function(){
      var rows = [['link_name','time_ip']];
      document.querySelectorAll('#timelineList .tl-item').forEach(function(el){
        var name = el.querySelector('.tl-name').textContent.trim();
        var meta = el.querySelector('.tl-meta').textContent.trim();
        rows.push([name, meta]);
      });
      var csv = rows.map(function(r){ return r.map(function(v){ return '"'+String(v).replace(/"/g,'""')+'"'; }).join(','); }).join('\n');
      var blob = new Blob([csv], { type:'text/csv' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url; a.download = 'analytics-export.csv';
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });
  }

  var clearBtn = document.getElementById('clearTestBtn');
  if (clearBtn){
    clearBtn.addEventListener('click', function(){
      if (!confirm('Hapus semua data test (klik dari link "test", "v", dll)? Ini gak bisa di-undo.')) return;
      document.getElementById('clearTestForm').submit();
    });
  }
</script>

</body>
</html>