<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editor — @by0x_</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;600&display=swap');
  :root{
    --bg:#08070c; --bg-soft:#0f0d16; --bg-card:#120f1c;
    --purple:#a855f7; --purple-deep:#6d28d9; --purple-glow:#c084fc;
    --green:#4ade80; --red:#f87171;
    --text:#f2eefc; --text-dim:#9a92ad; --text-faint:#5f5870;
    --line:rgba(168,85,247,0.16);
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
    background:radial-gradient(circle, rgba(168,85,247,0.14) 0%, transparent 65%);
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
    padding:9px 14px; border-radius:9px; border:1px solid var(--line);
    background:var(--bg-soft); color:var(--text-dim); text-decoration:none; transition:.15s;
    cursor:pointer;
  }
  .nav-btn:hover{ border-color:var(--purple); color:var(--text); }
  .nav-btn.accent{
    background:linear-gradient(135deg, rgba(168,85,247,0.22), rgba(109,40,217,0.14));
    color:var(--purple-glow); border-color:rgba(168,85,247,0.4);
  }
  .nav-btn.primary{
    background:linear-gradient(135deg, var(--purple-deep), var(--purple));
    color:#fff; border:none; font-weight:600;
  }
  .nav-btn.primary:hover{ opacity:.9; }

  .card{
    background:var(--bg-card); border:1px solid var(--line);
    border-radius:14px; padding:20px; margin-bottom:20px;
  }
  .card-title{
    font-family:'JetBrains Mono', monospace; font-size:11px; text-transform:uppercase;
    letter-spacing:.12em; color:var(--text-dim); margin-bottom:16px;
    display:flex; align-items:center; justify-content:space-between; gap:8px;
  }
  .card-title .label{ display:flex; align-items:center; gap:8px; }
  .card-title .label::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }

  .field-row{ margin-bottom:14px; }
  .field-row:last-child{ margin-bottom:0; }
  label{ display:block; font-size:11px; color:var(--text-faint); margin-bottom:5px; }
  input, textarea{
    width:100%; padding:10px 12px; border-radius:8px;
    border:1px solid var(--line); background:var(--bg-soft);
    color:var(--text); font-size:13px; outline:none; font-family:'Inter',sans-serif;
    transition:border-color .15s;
  }
  input:focus, textarea:focus{ border-color:var(--purple); box-shadow:0 0 0 1px var(--purple) inset; }
  textarea{ min-height:60px; resize:vertical; }

  .section-block{ margin-bottom:24px; }
  .section-hdr{
    display:flex; align-items:center; gap:8px; margin-bottom:10px;
  }
  .section-hdr input{
    font-size:12px; font-family:'JetBrains Mono',monospace; text-transform:uppercase;
    letter-spacing:.08em; padding:7px 10px; background:var(--bg-soft);
    border:1px solid var(--line); border-radius:7px; color:var(--text);
    outline:none; flex:1;
  }
  .section-hdr input:focus{ border-color:var(--purple); }
  .section-rm{
    font-size:11px; color:var(--text-faint); background:none; border:none; cursor:pointer;
    padding:4px 8px; border-radius:6px; font-family:'JetBrains Mono',monospace;
  }
  .section-rm:hover{ color:var(--red); background:rgba(248,113,113,0.1); }

  .link-item{
    background:var(--bg-soft); border:1px solid var(--line);
    border-radius:10px; padding:14px; margin-bottom:8px;
  }
  .link-grid{ display:grid; grid-template-columns:36px 1fr 1fr; gap:8px; margin-bottom:8px; }
  .link-grid input{ padding:8px 10px; font-size:12px; }
  .link-grid .icon-input{ text-align:center; font-size:14px; }
  .link-url-row{ display:grid; grid-template-columns:1fr 32px; gap:8px; }
  .link-url-row input{ padding:8px 10px; font-size:12px; }
  .link-actions{ display:flex; justify-content:flex-end; gap:6px; margin-top:6px; }
  .mini-btn{
    font-size:11px; color:var(--text-faint); background:none; border:none; cursor:pointer;
    padding:3px 8px; border-radius:5px; font-family:'JetBrains Mono',monospace; transition:.1s;
  }
  .mini-btn:hover{ color:var(--purple-glow); background:rgba(168,85,247,0.1); }
  .mini-btn.del:hover{ color:var(--red); background:rgba(248,113,113,0.1); }
  .add-btn{
    width:100%; padding:9px; border-radius:8px; border:1px dashed rgba(168,85,247,0.3);
    background:transparent; color:var(--text-dim); font-size:12px; cursor:pointer;
    font-family:'JetBrains Mono',monospace; transition:.15s;
  }
  .add-btn:hover{ border-color:var(--purple); color:var(--purple-glow); }

  .empty{ text-align:center; padding:30px 20px; color:var(--text-faint); font-size:12px; background:var(--bg-soft); border-radius:10px; border:1px dashed var(--line); }

  /* ---- preview inbox ---- */
  .preview-box{
    background:#050408; border:1px solid var(--line);
    border-radius:18px; padding:28px 22px 24px; text-align:center;
    max-width:340px; margin:0 auto;
  }
  .pv-avatar{ width:64px;height:64px;border-radius:50%;object-fit:cover;margin:0 auto 10px;display:block;background:#1a1625;border:2px solid var(--purple);box-shadow:0 0 0 4px rgba(168,85,247,0.08),0 0 20px rgba(168,85,247,0.2);}
  .pv-name{ font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; }
  .pv-handle{ font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--purple-glow); margin-top:3px; }
  .pv-bio{ font-size:13px; color:var(--text-dim); margin-top:8px; line-height:1.5; }
  .pv-section-label{ font-family:'JetBrains Mono',monospace; font-size:9px; text-transform:uppercase; letter-spacing:.1em; color:var(--text-dim); margin:16px 2px 8px; }
  .pv-links{ display:flex; flex-direction:column; gap:7px; }
  .pv-link{ display:flex; align-items:center; gap:10px; padding:10px 13px; background:var(--bg-soft); border:1px solid var(--line); border-radius:10px; }
  .pv-link-icon{ width:28px;height:28px;min-width:28px;border-radius:7px;background:#1a1625;display:flex;align-items:center;justify-content:center;font-size:13px; }
  .pv-link-text{ flex:1;min-width:0;text-align:left;font-size:12px;font-weight:600; }
  .pv-link-sub{ font-size:10px;color:var(--text-dim);font-family:'JetBrains Mono',monospace;margin-top:1px; }
  .pv-footer{ margin-top:20px; font-size:9px; color:var(--text-faint); font-family:'JetBrains Mono',monospace; }

  /* ---- avatar upload hint ---- */
  .avatar-preview-wrap{ display:flex; align-items:center; gap:14px; margin-bottom:14px; }
  .avatar-preview-wrap .mini-av{ width:44px;height:44px;border-radius:50%;object-fit:cover;background:#1a1625;border:1px solid var(--purple); }

  ::-webkit-scrollbar{ width:6px; }
  ::-webkit-scrollbar-thumb{ background:rgba(168,85,247,0.2); border-radius:3px; }

  @media(max-width:640px){ .shell{ padding:20px 14px 40px; } }
</style>
</head>
<body>

@if($errors->any())
  <div style="position:fixed;top:60px;left:50%;transform:translateX(-50%);z-index:99;background:rgba(248,113,113,0.12);color:#f87171;padding:10px 18px;border-radius:8px;font-size:13px;border:1px solid rgba(248,113,113,0.25);max-width:440px;width:calc(100%-28px)">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
  </div>
@endif

@if(session('status'))
  <div style="position:fixed;top:60px;left:50%;transform:translateX(-50%);z-index:99;background:rgba(74,222,128,0.12);color:#4ade80;padding:10px 18px;border-radius:8px;font-size:13px;border:1px solid rgba(74,222,128,0.25);max-width:440px;width:calc(100%-28px)">{{ session('status') }}</div>
@endif

<div class="shell">

  <div class="topbar">
    <div class="brand">
      <span class="dot"></span>
      <div>
        <h1>Editor</h1>
        <div class="sub">Edit your link-in-bio page</div>
      </div>
    </div>
    <div class="nav-actions">
      <a class="nav-btn accent" href="/u/{{ auth()->user()->username }}" target="_blank">🔗 View Page →</a>
      <a class="nav-btn" href="/analytics">← Analytics</a>
      <button class="nav-btn primary" id="saveBtn">Save</button>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:20px;align-items:start;">

    <!-- === EDIT PANEL === -->
    <div>
      <!-- Profile -->
      <div class="card">
        <div class="card-title"><span class="label">Profile</span></div>
        <div class="avatar-preview-wrap">
          <img class="mini-av" id="miniAv" src="" alt="">
          <label style="color:var(--text-faint);font-size:11px;">Avatar preview</label>
        </div>
        <div class="field-row">
          <label>Avatar URL</label>
          <input id="f-avatar" placeholder="https://example.com/avatar.jpg">
        </div>
        <div class="field-row">
          <label>Nama</label>
          <input id="f-name" placeholder="Your name">
        </div>
        <div class="field-row">
          <label>Handle</label>
          <input id="f-handle" placeholder="@username">
        </div>
        <div class="field-row">
          <label>Bio</label>
          <textarea id="f-bio" placeholder="Short bio"></textarea>
        </div>
      </div>

      <!-- Sections -->
      <div class="card">
        <div class="card-title"><span class="label">Sections</span></div>
        <div id="sectionsWrap"></div>
      </div>

      <form id="editorForm" method="POST" action="/editor">
        @csrf
        <input type="hidden" name="state_json" id="stateJson">
      </form>
    </div>

    <!-- === PREVIEW PANEL === -->
    <div>
      <div class="card" style="position:sticky;top:20px;">
        <div class="card-title"><span class="label">Preview</span></div>
        <div class="preview-box">
          <img class="pv-avatar" id="pv-avatar" src="" alt="">
          <div class="pv-name" id="pv-name"></div>
          <div class="pv-handle" id="pv-handle"></div>
          <div class="pv-bio" id="pv-bio"></div>
          <div id="pv-sections"></div>
          <div class="pv-footer">powered by <span style="color:var(--purple-glow)">Linkr</span></div>
        </div>
        <a class="nav-btn accent" href="/u/{{ auth()->user()->username }}" target="_blank" style="display:block;text-align:center;margin-top:16px;">🔗 Open Live Page →</a>
      </div>
    </div>

  </div>

</div>

<script>
function escA(s){ return (s||'').replace(/"/g,'&quot;'); }
function escH(s){ var d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function uid(){ return Math.random().toString(36).slice(2,9); }

var defaultState = {
  profile:{ name:'', handle:'@', bio:'', avatar:'' },
  sections:[]
};

var state = @json($initialState ?? null) || defaultState;
if(!state || !state.profile) state = defaultState;

var sectionsEl = document.getElementById('sectionsWrap');
var formEl = document.getElementById('editorForm');
var stateJson = document.getElementById('stateJson');

function renderForm(){
  document.getElementById('f-name').value = state.profile.name||'';
  document.getElementById('f-handle').value = state.profile.handle||'';
  document.getElementById('f-bio').value = state.profile.bio||'';
  document.getElementById('f-avatar').value = state.profile.avatar||'';
  document.getElementById('miniAv').src = state.profile.avatar||'';

  sectionsEl.innerHTML = '';
  if(!state.sections) state.sections = [];

  if(state.sections.length === 0){
    var e = document.createElement('div');
    e.className = 'empty';
    e.textContent = 'No sections yet. Click "+ Section" to add one.';
    sectionsEl.appendChild(e);
  }

  state.sections.forEach(function(s, si){
    var wrap = document.createElement('div');
    wrap.className = 'section-block';

    var hdr = document.createElement('div');
    hdr.className = 'section-hdr';
    hdr.innerHTML =
      '<input id="sec-lbl-'+si+'" placeholder="SECTION LABEL" value="'+escA(s.label||'')+'">'+
      '<button type="button" class="section-rm">✕ Delete</button>';
    hdr.querySelector('input').oninput = function(){ s.label = hdr.querySelector('input').value; syncPreview(); };
    hdr.querySelector('.section-rm').onclick = function(){
      if(!confirm('Delete "'+(s.label||'this section')+'" and all its links?')) return;
      state.sections.splice(si,1); renderForm(); syncPreview();
    };
    wrap.appendChild(hdr);

    (s.links||[]).forEach(function(lk, li){
      var item = document.createElement('div');
      item.className = 'link-item';
      item.innerHTML =
        '<div class="link-grid">'+
          '<input class="icon-input" placeholder="🔗" value="'+escA(lk.icon)+'">'+
          '<input placeholder="Title" value="'+escA(lk.title)+'">'+
          '<input placeholder="Sub" value="'+escA(lk.subtitle||'')+'">'+
        '</div>'+
        '<div class="link-url-row">'+
          '<input placeholder="https://..." value="'+escA(lk.url||'')+'">'+
          '<button type="button" class="mini-btn del" style="border:1px solid rgba(248,113,113,0.3);">✕</button>'+
        '</div>'+
        '<div class="link-actions">'+
          '<button type="button" class="mini-btn">↑ Move up</button>'+
          '<button type="button" class="mini-btn">↓ Move down</button>'+
        '</div>';

      var i = item.querySelectorAll('.link-grid input');
      i[0].oninput = function(){ lk.icon=i[0].value; syncPreview(); };
      i[1].oninput = function(){ lk.title=i[1].value; syncPreview(); };
      i[2].oninput = function(){ lk.subtitle=i[2].value; syncPreview(); };

      var urlIn = item.querySelector('.link-url-row input');
      urlIn.oninput = function(){ lk.url=urlIn.value; syncPreview(); };

      item.querySelector('.del').onclick = function(){ s.links.splice(li,1); renderForm(); syncPreview(); };
      item.querySelectorAll('.link-actions .mini-btn')[0].onclick = function(){ moveL(si,li,-1); };
      item.querySelectorAll('.link-actions .mini-btn')[1].onclick = function(){ moveL(si,li,1); };

      wrap.appendChild(item);
    });

    var addL = document.createElement('button');
    addL.type = 'button'; addL.className = 'add-btn';
    addL.textContent = '+ Add Link';
    addL.onclick = function(){
      if(!s.links) s.links=[];
      s.links.push({icon:'🔗',title:'New Link',subtitle:'',url:'#'});
      renderForm(); syncPreview();
    };
    wrap.appendChild(addL);
    sectionsEl.appendChild(wrap);
  });

  var addSec = document.createElement('button');
  addSec.type = 'button'; addSec.className = 'add-btn';
  addSec.style.marginTop = '12px';
  addSec.textContent = '+ New Section';
  addSec.onclick = function(){
    state.sections.push({key:'sec-'+uid(),label:'SECTION',links:[]});
    renderForm(); syncPreview();
  };
  sectionsEl.appendChild(addSec);
}

function moveL(si,li,dir){
  var arr = state.sections[si].links;
  var ni = li+dir;
  if(ni<0||ni>=arr.length)return;
  var t=arr[li];arr[li]=arr[ni];arr[ni]=t;
  renderForm();syncPreview();
}

['f-name','f-handle','f-bio','f-avatar'].forEach(function(id){
  document.getElementById(id).addEventListener('input', function(e){
    if(id==='f-name') state.profile.name = e.target.value;
    if(id==='f-handle') state.profile.handle = e.target.value;
    if(id==='f-bio') state.profile.bio = e.target.value;
    if(id==='f-avatar') state.profile.avatar = e.target.value;
    syncPreview();
  });
});

function syncPreview(){
  document.getElementById('pv-avatar').src = state.profile.avatar||'';
  document.getElementById('miniAv').src = state.profile.avatar||'';
  document.getElementById('pv-name').textContent = state.profile.name||'';
  document.getElementById('pv-handle').textContent = state.profile.handle||'';
  document.getElementById('pv-bio').textContent = state.profile.bio||'';
  var pv = document.getElementById('pv-sections');
  pv.innerHTML = '';
  var any = false;
  (state.sections||[]).forEach(function(sec){
    if(!sec.links || !sec.links.length) return;
    any = true;
    var lbl = document.createElement('div');
    lbl.className = 'pv-section-label';
    lbl.textContent = sec.label;
    pv.appendChild(lbl);
    var w = document.createElement('div');
    w.className = 'pv-links';
    sec.links.forEach(function(lk){
      w.innerHTML +=
        '<div class="pv-link">'+
          '<div class="pv-link-icon">'+(lk.icon||'🔗')+'</div>'+
          '<div class="pv-link-text">'+escH(lk.title)+'<div class="pv-link-sub">'+escH(lk.subtitle||'')+'</div></div>'+
        '</div>';
    });
    pv.appendChild(w);
  });
  if(!any){
    pv.innerHTML = '<div style="color:var(--text-faint);font-size:11px;padding:16px 0;">Add links to see preview</div>';
  }
  stateJson.value = JSON.stringify(state);
}

document.getElementById('saveBtn').addEventListener('click', function(){
  stateJson.value = JSON.stringify(state);
  formEl.submit();
});

renderForm(); syncPreview();
</script>
</body>
</html>