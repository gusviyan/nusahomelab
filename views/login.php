<!doctype html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login — Nusa HomeLab</title><link rel="stylesheet" href="<?= BASE_URL ?: '' ?>/css/admin.css"></head>
<body class="login-page">
  <script>window.APP_BASE_URL='<?= BASE_URL ?>';</script>
  <main class="login-card">
    <a href="<?= BASE_URL ?: '/' ?>" class="admin-brand"><b>N</b> NUSA HOMELAB<span>.</span></a>
    <div><small>AREA TERBATAS</small><h1>Selamat datang<br>kembali.</h1><p>Masuk untuk mengelola konten portofolio Anda.</p></div>
    <form id="login-form">
      <label>Username<input name="username" autocomplete="username" required></label>
      <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
      <p class="form-error" id="login-error"></p>
      <button type="submit">Masuk ke dashboard <span>↗</span></button>
    </form>
    <a class="back-link" href="<?= BASE_URL ?: '/' ?>">← Kembali ke website</a>
  </main>
  <div class="login-art"><div class="art-circle"><i>DESIGN</i><strong>×</strong><i>CODE</i></div><p>CREATE<br><em>WITH</em><br>PURPOSE.</p></div>
  <script>
    document.querySelector('#login-form').addEventListener('submit',async(e)=>{e.preventDefault();const button=e.target.querySelector('button');button.disabled=true;button.firstChild.textContent='Memeriksa... ';const base=window.APP_BASE_URL||'';const response=await fetch(`${base}/api/auth/login`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(e.target)))});const data=await response.json();if(data.success)location.href=`${base}/admin/dashboard`;else{document.querySelector('#login-error').textContent=data.message;button.disabled=false;button.firstChild.textContent='Masuk ke dashboard ';}});
  </script>
</body></html>
