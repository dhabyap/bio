<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $state['name'] }} — Links</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;600&display=swap');
  :root{ --bg:#08070c; --bg-soft:#0f0d16; --bg-card:#120f1c; --purple:#a855f7; --purple-deep:#6d28d9; --purple-glow:#c084fc; --text:#f2eefc; --text-dim:#9a92ad; --text-faint:#5f5870; }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{ background:var(--bg); color:var(--text); font-family:'Inter',-apple-system,sans-serif; min-height:100vh; display:flex; flex-direction:column; align-items:center; padding:40px 20px 20px; }
  .card{ max-width:380px; width:100%; text-align:center; }
  .avatar{ width:80px; height:80px; border-radius:50%; object-fit:cover; margin:0 auto 14px; background:linear-gradient(135deg,var(--purple-deep),var(--purple-glow)); display:block; }
  .avatar-img{ width:80px; height:80px; border-radius:50%; object-fit:cover; }
  h1{ font-family:'Space Grotesk',sans-serif; font-size:22px; font-weight:700; margin-bottom:4px; }
  .handle{ font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--purple-glow); margin-bottom:12px; }
  .bio{ font-size:14px; color:var(--text-dim); line-height:1.6; margin-bottom:24px; }
  .links{ display:flex; flex-direction:column; gap:10px; }
  .link-btn{ display:flex; align-items:center; gap:12px; padding:13px 16px; background:var(--bg-card); border:1px solid rgba(168,85,247,0.16); border-radius:13px; color:var(--text); text-decoration:none; font-size:14px; font-weight:500; transition:all .15s; }
  .link-btn:hover{ background:var(--bg-soft); border-color:var(--purple); transform:translateY(-2px); }
  .link-btn .icon{ width:32px; height:32px; border-radius:9px; background:linear-gradient(135deg,rgba(168,85,247,0.2),rgba(109,40,217,0.1)); display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
  .link-btn .label{ flex:1; text-align:left; }
  .link-btn .arrow{ color:var(--text-faint); font-size:16px; }
  .footer-sig{ margin-top:36px; font-family:'JetBrains Mono',monospace; font-size:10px; color:var(--text-faint); }
  .footer-sig span{ color:var(--purple-glow); }
</style>
</head>
<body>
<div class="card">
  @if(!empty($state['avatar']))
    <img class="avatar-img" src="{{ $state['avatar'] }}" alt="{{ $state['name'] }}">
  @else
    <div class="avatar"></div>
  @endif

  <h1>{{ $state['name'] }}</h1>
  <div class="handle">@ {{ $user->username }}</div>

  @if(!empty($state['bio']))
    <div class="bio">{{ $state['bio'] }}</div>
  @endif

  <div class="links">
    @foreach($state['links'] as $link)
      <a class="link-btn" href="{{ $link['url'] }}" target="_blank" rel="noopener"
         onclick="trackClick('{{ $link['title'] }}','{{ $link['url'] }}')">
        <span class="icon">{{ $link['icon'] ?? '→' }}</span>
        <span class="label">{{ $link['title'] }}</span>
        <span class="arrow">↗</span>
      </a>
    @endforeach
  </div>

  <div class="footer-sig">built by <span>@by0x_</span> · powered by Linkr</div>
</div>

<script>
function trackClick(name, url){
  var payload = JSON.stringify({link_name: name, link_url: url, source: document.referrer || ''});
  navigator.sendBeacon('/track-click', new Blob([payload], {type: 'application/json'}));
}
</script>
</body>
</html>