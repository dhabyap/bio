<!--
  ASUMSI DATA (sesuaikan sama controller-mu):
  $initialState (opsional) = array/object hasil json_decode konten tersimpan, bentuknya:
  [
    'profile' => ['name'=>'Putra','handle'=>'@by0x_','bio'=>'...','avatar'=>'https://...'],
    'sections' => [
      ['key'=>'highlight','label'=>'Highlight','links'=>[['icon'=>'🌙','title'=>'Moove Ambassador','subtitle'=>'...','url'=>'#']]],
      ['key'=>'social','label'=>'Social','links'=>[...]],
      ...
    ]
  ]
  Kalau belum ada, dipakein default (persis konten links.blade.php sekarang) biar editor tetep kepake.

  Submit: form POST ke /editor (ganti sesuai route-mu), isinya 1 hidden input
  "state_json" berisi JSON lengkap. Backend tinggal json_decode($request->state_json)
  lalu simpan ke DB / tulis ulang links.blade.php via templating — jauh lebih simple
  daripada parsing array field bernested nama.
-->
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Content — @by0x_</title>
<style>
  :root{
    --bg:#08070c; --bg-soft:#0f0d16; --bg-card:#120f1c;
    --purple:#a855f7; --purple-deep:#6d28d9; --purple-glow:#c084fc;
    --text:#f2eefc; --text-dim:#9a92ad; --text-faint:#5f5870;
    --line: rgba(168,85,247,0.16);
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  html,body{ height:100%; }
  body{
    background:var(--bg); color:var(--text);
    font-family:'Inter',-apple-system,sans-serif;
    display:flex; flex-direction:column; overflow:hidden;
  }

  /* ---- top bar ---- */
  .topbar{
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 22px; border-bottom:1px solid rgba(168,85,247,0.14);
    background:var(--bg-soft); z-index:5;
  }
  .brand{ display:flex; align-items:center; gap:9px; }
  .brand .dot{ width:9px;height:9px;border-radius:50%; background:var(--purple); box-shadow:0 0 10px var(--purple-glow); }
  .brand span{ font-weight:700; font-size:15px; }
  .topbar-actions{ display:flex; gap:8px; align-items:center; }
  .save-status{ font-family:'JetBrains Mono', monospace; font-size:11px; color:var(--text-faint); margin-right:6px; }
  .btn{
    font-family:'JetBrains Mono', monospace; font-size:12px;
    padding:9px 16px; border-radius:9px; border:1px solid rgba(168,85,247,0.2);
    background:var(--bg-card); color:var(--text-dim); text-decoration:none; cursor:pointer;
    transition:.15s;
  }
  .btn:hover{ border-color:var(--purple); color:var(--text); }
  .btn.primary{
    background:linear-gradient(135deg, var(--purple-deep), var(--purple));
    color:#fff; border:none; font-weight:600;
  }
  .btn.primary:hover{ opacity:.9; }

  /* ---- layout ---- */
  .body-split{ flex:1; display:flex; overflow:hidden; }
  .panel-edit{
    width:46%; min-width:340px; overflow-y:auto; padding:26px 24px 80px;
    border-right:1px solid rgba(168,85,247,0.12);
  }
  .panel-preview{
    flex:1; overflow-y:auto; display:flex; justify-content:center;
    padding:40px 24px; background:
      radial-gradient(circle at 30% 0%, rgba(168,85,247,0.10), transparent 55%);
  }

  .fieldset{ margin-bottom:30px; }
  .fieldset-title{
    font-family:'JetBrains Mono', monospace; font-size:11px; text-transform:uppercase;
    letter-spacing:.1em; color:var(--text-dim); margin-bottom:12px;
    display:flex; align-items:center; gap:8px;
  }
  .fieldset-title::before{
    content:''; width:6px; height:6px; border-radius:50%;
    background:var(--purple); box-shadow:0 0 8px var(--purple-glow);
  }
  label.field-label{ display:block; font-size:11px; color:var(--text-faint); margin:0 0 5px; }
  input.field, textarea.field{
    width:100%; padding:10px 12px; border-radius:8px;
    border:1px solid rgba(168,85,247,0.16); background:var(--bg-card);
    color:var(--text); font-size:13px; margin-bottom:12px; outline:none;
    font-family:'Inter',sans-serif; resize:vertical;
  }
  input.field:focus, textarea.field:focus{ border-color:var(--purple); box-shadow:0 0 0 1px var(--purple) inset; }
  textarea.field{ min-height:56px; }

  .link-card{
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.12);
    border-radius:10px; padding:12px 12px 4px; margin-bottom:10px; position:relative;
  }
  .link-card-row{ display:grid; grid-template-columns:52px 1fr; gap:8px; }
  .link-card .icon-input{ text-align:center; font-size:16px; }
  .link-card-actions{
    display:flex; justify-content:flex-end; gap:6px; margin:-2px 0 8px;
  }
  .mini-btn{
    font-size:11px; color:var(--text-faint); background:none; border:none; cursor:pointer;
    padding:2px 6px; border-radius:5px; font-family:'JetBrains Mono', monospace;
  }
  .mini-btn:hover{ color:var(--purple-glow); background:rgba(168,85,247,0.1); }
  .add-link-btn{
    width:100%; padding:9px; border-radius:8px; border:1px dashed rgba(168,85,247,0.3);
    background:transparent; color:var(--text-dim); font-size:12px; cursor:pointer;
    font-family:'JetBrains Mono', monospace; margin-bottom:26px; transition:.15s;
  }
  .add-link-btn:hover{ border-color:var(--purple); color:var(--purple-glow); }

  /* ---- preview mirror (mimics links.blade.php) ---- */
  .phone{
    width:100%; max-width:400px; height:fit-content;
    background:#050408; border:1px solid rgba(168,85,247,0.25);
    border-radius:28px; padding:34px 22px 30px;
    box-shadow:0 0 50px rgba(168,85,247,0.12);
    position:relative;
  }
  .p-profile{ display:flex; flex-direction:column; align-items:center; text-align:center; margin-bottom:30px; }
  .p-avatar{
    width:80px; height:80px; border-radius:50%; object-fit:cover;
    border:2px solid var(--purple);
    box-shadow:0 0 0 5px rgba(168,85,247,0.08), 0 0 26px rgba(168,85,247,0.3);
    margin-bottom:14px; background:#1a1625;
  }
  .p-name{ font-size:19px; font-weight:700; }
  .p-handle{ font-family:'JetBrains Mono', monospace; font-size:12px; color:var(--purple-glow); margin-top:3px; }
  .p-bio{ font-size:13px; color:var(--text-dim); margin-top:10px; line-height:1.55; max-width:290px; }
  .p-section-label{
    font-family:'JetBrains Mono', monospace; font-size:10px; text-transform:uppercase;
    letter-spacing:.1em; color:var(--text-dim); margin:20px 2px 8px; display:flex; align-items:center; gap:7px;
  }
  .p-section-label::before{ content:''; width:5px; height:5px; border-radius:50%; background:var(--purple); box-shadow:0 0 7px var(--purple-glow); }
  .p-links{ display:flex; flex-direction:column; gap:9px; }
  .p-link{
    display:flex; align-items:center; gap:12px; padding:12px 14px;
    background:var(--bg-soft); border:1px solid rgba(168,85,247,0.16); border-radius:12px;
  }
  .p-link-node{ width:30px; height:30px; min-width:30px; border-radius:8px; background:#1a1625; display:flex; align-items:center; justify-content:center; font-size:14px; }
  .p-link-copy{ flex:1; min-width:0; }
  .p-link-title{ font-size:13px; font-weight:600; }
  .p-link-sub{ font-size:11px; color:var(--text-dim); font-family:'JetBrains Mono', monospace; margin-top:1px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .p-footer{ margin-top:26px; text-align:center; font-family:'JetBrains Mono', monospace; font-size:10px; color:var(--text-faint); }
  .p-footer span{ color:var(--purple-glow); }
  .preview-tag{
    text-align:center; font-family:'JetBrains Mono', monospace; font-size:10px;
    color:var(--text-faint); margin-bottom:14px; letter-spacing:.08em; text-transform:uppercase;
  }

  ::-webkit-scrollbar{ width:8px; }
  ::-webkit-scrollbar-thumb{ background:rgba(168,85,247,0.25); border-radius:4px; }

  @media(max-width:860px){
    .body-split{ flex-direction:column; overflow:auto; }
    .panel-edit, .panel-preview{ width:100%; }
    .panel-preview{ padding:24px; }
  }
</style>
</head>
<body>

@if($errors->any())
  <div style="position:fixed;top:70px;left:50%;transform:translateX(-50%);z-index:99;background:rgba(248,113,113,0.12);color:#f87171;padding:12px 20px;border-radius:8px;font-size:13px;border:1px solid rgba(248,113,113,0.25);max-width:500px;width:calc(100%-40px)">
    @foreach($errors->all() as $e)
      <div>{{ $e }}</div>
    @endforeach
  </div>
@endif

@if(session('status'))
  <div style="position:fixed;top:70px;left:50%;transform:translateX(-50%);z-index:99;background:rgba(74,222,128,0.12);color:#4ade80;padding:12px 20px;border-radius:8px;font-size:13px;border:1px solid rgba(74,222,128,0.25);max-width:500px;width:calc(100%-40px)">
    {{ session('status') }}
  </div>
@endif

<div class="topbar">
  <div class="brand"><span class="dot"></span><span>Edit Content</span></div>
  <div class="topbar-actions">
    <span class="save-status" id="saveStatus"></span>
    <a class="btn" href="/analytics">← Dashboard</a>
    <button class="btn primary" id="saveBtn" onclick="document.getElementById('editorForm').submit();">Save Changes</button>
  </div>
</div>

<div class="body-split">

  <!-- ===== FORM PANEL ===== -->
  <div class="panel-edit">

    <div class="fieldset">
      <div class="fieldset-title">Profile</div>
      <label class="field-label">Nama</label>
      <input class="field" id="f-name" data-bind="profile.name">
      <label class="field-label">Handle</label>
      <input class="field" id="f-handle" data-bind="profile.handle">
      <label class="field-label">Bio</label>
      <textarea class="field" id="f-bio" data-bind="profile.bio"></textarea>
      <label class="field-label">Avatar URL</label>
      <input class="field" id="f-avatar" data-bind="profile.avatar">
    </div>

    <div id="sectionsContainer"><!-- diisi JS --></div>

    <form id="editorForm" method="POST" action="/editor">
      @csrf
      <input type="hidden" name="state_json" id="stateJson">
    </form>
  </div>

  <!-- ===== PREVIEW PANEL ===== -->
  <div class="panel-preview">
    <div style="width:100%;max-width:400px;">
      <div class="preview-tag">● Live Preview</div>
      <div class="phone">
        <div class="p-profile">
          <img class="p-avatar" id="pv-avatar" src="" alt="">
          <div class="p-name" id="pv-name"></div>
          <div class="p-handle" id="pv-handle"></div>
          <div class="p-bio" id="pv-bio"></div>
        </div>
        <div id="pv-sections"></div>
        <div class="p-footer">built by <span id="pv-footer-handle"></span> · 2026</div>
      </div>
    </div>
  </div>

</div>

<script>
// ---------- initial state ----------
var defaultState = {
  profile: {
    name: "Putra",
    handle: "@by0x_",
    bio: "Web2 → Web3 Developer. Moove Ambassador. Building in public.",
    avatar: "https://via.placeholder.com/96/1a1625/a855f7?text=P"
  },
  sections: [
    { key:"highlight", label:"Highlight", links:[
      { icon:"🌙", title:"Moove Ambassador", subtitle:"Official Ambassador Program", url:"#" }
    ]},
    { key:"social", label:"Social", links:[
      { icon:"𝕏", title:"X (Twitter)", subtitle:"@by0x_", url:"https://x.com/by0x_" },
      { icon:"💬", title:"Discord", subtitle:"Join community", url:"https://discord.gg/DTPJh5Bzp" }
    ]},
    { key:"portfolio", label:"Portfolio", links:[
      { icon:"⌘", title:"GitHub", subtitle:"dhabyap", url:"https://github.com/dhabyap" },
      { icon:"◈", title:"ChainTrinket", subtitle:"Physical auth on Stellar/Soroban", url:"https://dhabyap.github.io/chaintrinket/" }
    ]},
    { key:"contact", label:"Contact", links:[] }
  ]
};

var state = @json($initialState ?? null) || defaultState;
if (!state || !state.profile) state = defaultState;

// ---------- render form ----------
var sectionsEl = document.getElementById('sectionsContainer');

function uid(){ return Math.random().toString(36).slice(2,9); }

function renderForm(){
  document.getElementById('f-name').value = state.profile.name || '';
  document.getElementById('f-handle').value = state.profile.handle || '';
  document.getElementById('f-bio').value = state.profile.bio || '';
  document.getElementById('f-avatar').value = state.profile.avatar || '';

  sectionsEl.innerHTML = '';
  state.sections.forEach(function(section, sIdx){
    var wrap = document.createElement('div');
    wrap.className = 'fieldset';
    var title = document.createElement('div');
    title.className = 'fieldset-title';
    title.textContent = section.label;
    wrap.appendChild(title);

    section.links.forEach(function(link, lIdx){
      var card = document.createElement('div');
      card.className = 'link-card';

      var actions = document.createElement('div');
      actions.className = 'link-card-actions';
      actions.innerHTML =
        '<button type="button" class="mini-btn" data-act="up">↑</button>' +
        '<button type="button" class="mini-btn" data-act="down">↓</button>' +
        '<button type="button" class="mini-btn" data-act="del">✕ hapus</button>';
      actions.querySelector('[data-act="up"]').onclick = function(){ moveLink(sIdx, lIdx, -1); };
      actions.querySelector('[data-act="down"]').onclick = function(){ moveLink(sIdx, lIdx, 1); };
      actions.querySelector('[data-act="del"]').onclick = function(){ delLink(sIdx, lIdx); };
      card.appendChild(actions);

      var row = document.createElement('div');
      row.className = 'link-card-row';
      row.innerHTML =
        '<input class="field icon-input" placeholder="🔗" value="'+escAttr(link.icon)+'">' +
        '<input class="field" placeholder="Judul link" value="'+escAttr(link.title)+'">';
      card.appendChild(row);

      var subtitle = document.createElement('input');
      subtitle.className = 'field';
      subtitle.placeholder = 'Subtitle';
      subtitle.value = link.subtitle || '';
      card.appendChild(subtitle);

      var url = document.createElement('input');
      url.className = 'field';
      url.placeholder = 'https://...';
      url.value = link.url || '';
      card.appendChild(url);

      var inputs = row.querySelectorAll('input');
      inputs[0].oninput = function(){ link.icon = inputs[0].value; syncPreview(); };
      inputs[1].oninput = function(){ link.title = inputs[1].value; syncPreview(); };
      subtitle.oninput = function(){ link.subtitle = subtitle.value; syncPreview(); };
      url.oninput = function(){ link.url = url.value; syncPreview(); };

      wrap.appendChild(card);
    });

    var addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.className = 'add-link-btn';
    addBtn.textContent = '+ Tambah link di ' + section.label;
    addBtn.onclick = function(){
      section.links.push({ icon:'🔗', title:'Link baru', subtitle:'', url:'#' });
      renderForm(); syncPreview();
    };
    wrap.appendChild(addBtn);

    sectionsEl.appendChild(wrap);
  });
}

function moveLink(sIdx, lIdx, dir){
  var arr = state.sections[sIdx].links;
  var newIdx = lIdx + dir;
  if (newIdx < 0 || newIdx >= arr.length) return;
  var tmp = arr[lIdx]; arr[lIdx] = arr[newIdx]; arr[newIdx] = tmp;
  renderForm(); syncPreview();
}
function delLink(sIdx, lIdx){
  state.sections[sIdx].links.splice(lIdx, 1);
  renderForm(); syncPreview();
}
function escAttr(s){ return (s || '').replace(/"/g, '&quot;'); }

// profile field bindings
['f-name','f-handle','f-bio','f-avatar'].forEach(function(id){
  document.getElementById(id).addEventListener('input', function(e){
    var key = e.target.getAttribute('data-bind').split('.')[1];
    state.profile[key] = e.target.value;
    syncPreview();
  });
});

// ---------- render preview ----------
function syncPreview(){
  document.getElementById('pv-avatar').src = state.profile.avatar || '';
  document.getElementById('pv-name').textContent = state.profile.name || '';
  document.getElementById('pv-handle').textContent = state.profile.handle || '';
  document.getElementById('pv-bio').textContent = state.profile.bio || '';
  document.getElementById('pv-footer-handle').textContent = state.profile.handle || '';

  var pvSections = document.getElementById('pv-sections');
  pvSections.innerHTML = '';
  state.sections.forEach(function(section){
    if (!section.links.length) return;
    var label = document.createElement('div');
    label.className = 'p-section-label';
    label.textContent = section.label;
    pvSections.appendChild(label);

    var linksWrap = document.createElement('div');
    linksWrap.className = 'p-links';
    section.links.forEach(function(link){
      var a = document.createElement('div');
      a.className = 'p-link';
      a.innerHTML =
        '<div class="p-link-node">'+ (link.icon || '🔗') +'</div>' +
        '<div class="p-link-copy">' +
          '<div class="p-link-title">'+ escHtml(link.title) +'</div>' +
          '<div class="p-link-sub">'+ escHtml(link.subtitle) +'</div>' +
        '</div>';
      linksWrap.appendChild(a);
    });
    pvSections.appendChild(linksWrap);
  });

  document.getElementById('stateJson').value = JSON.stringify(state);
  var status = document.getElementById('saveStatus');
  status.textContent = 'unsaved changes';
}

function escHtml(s){
  var d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}

document.getElementById('editorForm').addEventListener('submit', function(){
  document.getElementById('stateJson').value = JSON.stringify(state);
});

renderForm();
syncPreview();
document.getElementById('saveStatus').textContent = '';
</script>

</body>
</html>
