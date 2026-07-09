<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — Linkr</title>
<style>
  :root{
    --bg: #08070c; --bg-soft: #0f0d16; --bg-card:#120f1c;
    --purple: #a855f7; --purple-deep:#6d28d9; --purple-glow: #c084fc;
    --text: #f2eefc; --text-dim: #9a92ad; --text-faint:#5f5870; --red: #f87171;
  }
  *{ margin:0; padding:0; box-sizing:border-box; }
  body{
    background:var(--bg); color:var(--text);
    font-family:'Inter',-apple-system,sans-serif;
    min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
  }
  .card{
    width:100%; max-width:400px;
    background:var(--bg-card); border:1px solid rgba(168,85,247,0.18);
    border-radius:20px; padding:36px 28px 28px;
    box-shadow:0 0 60px rgba(168,85,247,0.08);
  }
  h1{ font-family:'Space Grotesk',sans-serif; font-size:22px; margin-bottom:6px; }
  p.sub{ color:var(--text-dim); font-size:13px; margin-bottom:24px; }
  label{ display:block; font-size:12px; color:var(--text-dim); margin-bottom:5px; }
  input{
    width:100%; padding:11px 13px; margin-bottom:14px; border-radius:10px;
    border:1px solid rgba(168,85,247,0.2); background:var(--bg-soft);
    color:var(--text); font-size:14px; outline:none; transition:border-color .15s;
  }
  input:focus{ border-color:var(--purple); box-shadow:0 0 0 2px rgba(168,85,247,0.15); }
  button{
    width:100%; padding:12px; border:none; border-radius:100px;
    background:linear-gradient(135deg, var(--purple-deep), var(--purple));
    color:#fff; font-weight:600; font-size:14px; cursor:pointer; margin-top:4px;
    box-shadow:0 0 0 1px rgba(168,85,247,0.4), 0 6px 20px rgba(168,85,247,0.3);
    transition:transform .15s;
  }
  button:hover{ transform:translateY(-2px); }
  .error{ color:var(--red); font-size:12px; margin:-10px 0 14px; }
  .login-link{ text-align:center; margin-top:16px; font-size:13px; color:var(--text-dim); }
  .login-link a{ color:var(--purple-glow); text-decoration:none; }
  .login-link a:hover{ text-decoration:underline; }
</style>
</head>
<body>
<div class="card">
  <h1>Buat akun</h1>
  <p class="sub">Pilih handle & mulai halaman link kamu.</p>
  <form method="POST" action="/register">
    @csrf
    <label>Username</label>
    <input type="text" name="username" value="{{ old('username') }}" placeholder="namakamu" required>
    @error('username')<div class="error">{{ $message }}</div>@enderror

    <label>Nama</label>
    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Kamu" required>
    @error('name')<div class="error">{{ $message }}</div>@enderror

    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
    @error('email')<div class="error">{{ $message }}</div>@enderror

    <label>Password</label>
    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
    @error('password')<div class="error">{{ $message }}</div>@enderror

    <label>Konfirmasi Password</label>
    <input type="password" name="password_confirmation" placeholder="Ulangi password" required>

    <button type="submit">Daftar →</button>
  </form>
  <div class="login-link">Sudah punya akun? <a href="/login">Login</a></div>
</div>
</body>
</html>