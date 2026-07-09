<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — @by0x_</title>
<style>
  :root{ --bg:#08070c; --bg-soft:#0f0d16; --purple:#a855f7; --purple-glow:#c084fc; --text:#f2eefc; --text-dim:#9a92ad; }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    background:var(--bg); color:var(--text); font-family:'Inter',-apple-system,sans-serif;
    min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
  }
  .card{
    background:var(--bg-soft); border:1px solid rgba(168,85,247,0.18);
    border-radius:16px; padding:36px 32px 32px; width:100%; max-width:360px;
  }
  h1{ font-size:20px; margin-bottom:4px; }
  .sub{ color:var(--text-dim); font-size:13px; margin-bottom:24px; }
  label{ display:block; font-size:12px; color:var(--text-dim); margin-bottom:6px; }
  input{
    width:100%; padding:12px 14px; border-radius:10px;
    border:1px solid rgba(168,85,247,0.18); background:#1a1625;
    color:var(--text); font-size:14px; margin-bottom:18px; outline:none;
  }
  input:focus{ border-color:var(--purple); box-shadow:0 0 0 1px var(--purple) inset; }
  button{
    width:100%; padding:12px; border:none; border-radius:10px;
    background:var(--purple); color:#fff; font-weight:600; font-size:14px;
    cursor:pointer; transition:opacity .2s;
  }
  button:hover{ opacity:.85; }
  .error{ color:#f87171; font-size:12px; margin-bottom:12px; }
  a{ color:var(--text-dim); font-size:12px; text-decoration:none; display:block; text-align:center; margin-top:18px; }
  a:hover{ color:var(--purple); }
</style>
</head>
<body>
  <div class="card">
    <h1>🔐 Masuk</h1>
    <p class="sub">Dashboard analytics @by0x_</p>

    @if($errors->any())
      <div class="error">{{ $errors->first('username') }}</div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

      <label>Password</label>
      <input type="password" name="password" autocomplete="current-password" required>

      <button type="submit">Masuk</button>
    </form>

    <a href="/">← Kembali</a>
  </div>
</body>
</html>