<!doctype html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard — Nusa HomeLab</title><link rel="stylesheet" href="<?= BASE_URL ?: '' ?>/css/admin.css"><script>window.APP_BASE_URL='<?= BASE_URL ?>';</script><script src="<?= BASE_URL ?: '' ?>/js/admin.js" defer></script></head>
<body class="dashboard">
<aside>
  <a href="<?= BASE_URL ?: '/' ?>" class="admin-brand"><b>N</b> NUSA HOMELAB<span>.</span></a>
  <nav><button class="active" data-page="overview">⌂<span>Ringkasan</span></button><button data-page="projects">◆<span>Portofolio</span></button><button data-page="services">✦<span>Layanan</span></button><button data-page="profile">●<span>Konten website</span></button></nav>
  <div class="sidebar-bottom"><a href="<?= BASE_URL ?: '/' ?>" target="_blank">Lihat website ↗</a><button id="logout">Keluar</button></div>
</aside>
<main>
  <header><div><small>CONTENT MANAGEMENT</small><h1 id="page-title">Ringkasan</h1></div><div class="admin-user"><span id="username">Admin</span><b>A</b></div></header>
  <div id="notice"></div>
  <section class="admin-page active" id="overview">
    <div class="welcome"><div><small>PORTFOLIO CONTROL ROOM</small><h2>Halo, <em>kreator.</em></h2><p>Semua yang tampil di website bisa Anda kelola dari satu tempat.</p></div><span>✦</span></div>
    <div class="summary-grid"><article><span>PROYEK</span><strong id="project-count">0</strong><button data-go="projects">Kelola proyek →</button></article><article><span>LAYANAN</span><strong id="service-count">0</strong><button data-go="services">Kelola layanan →</button></article><article><span>STATUS WEBSITE</span><strong class="online">● Online</strong><a href="<?= BASE_URL ?: '/' ?>" target="_blank">Buka website ↗</a></article></div>
  </section>
  <section class="admin-page" id="projects">
    <div class="toolbar"><p>Tambah, ubah, atau hapus karya yang tampil di website.</p><button class="primary" id="add-project">+ Tambah proyek</button></div>
    <div class="content-list" id="project-list"></div>
  </section>
  <section class="admin-page" id="services">
    <div class="toolbar"><p>Atur layanan utama yang Anda tawarkan.</p><button class="primary" id="add-service">+ Tambah layanan</button></div>
    <div class="content-list" id="service-list"></div>
  </section>
  <section class="admin-page" id="profile">
    <form id="settings-form" class="settings-form">
      <div class="form-section"><div><h2>Identitas</h2><p>Nama brand, logo, dan informasi utama.</p></div><div class="fields two"><label class="wide">Logo website <small>JPG, PNG, atau WebP; maksimal 2MB</small><input id="logo-file" name="logo_file" type="file" accept="image/jpeg,image/png,image/webp"></label><div class="logo-preview wide" id="logo-preview"><span>Belum ada logo custom</span></div><label>Nama brand<input name="name" required></label><label>Profesi<input name="role" required></label><label>Lokasi<input name="location"></label><label>Teks topbar<input name="topbar_text"></label><label>Teks tautan topbar<input name="topbar_link_text"></label></div></div>
      <div class="form-section"><div><h2>Hero</h2><p>Bagian pembuka halaman.</p></div><div class="fields two"><label class="wide">Judul <small>Gunakan | sebelum bagian berwarna</small><input name="hero_title"></label><label class="wide">Deskripsi<textarea name="hero_description" rows="3"></textarea></label><label>Tombol utama<input name="hero_primary_text"></label><label>Tombol kedua<input name="hero_secondary_text"></label><label>Label kepercayaan<input name="trust_label"></label><label>Daftar keahlian <small>Pisahkan dengan koma</small><input name="trust_items"></label><label>Jumlah proyek<input name="project_count"></label><label>Rating<input name="rating"></label><label>Label rating<input name="rating_label"></label></div></div>
      <div class="form-section"><div><h2>Keunggulan</h2><p>Tiga poin di bawah hero.</p></div><div class="fields two"><label>Judul 1<input name="benefit_1_title"></label><label>Keterangan 1<input name="benefit_1_text"></label><label>Judul 2<input name="benefit_2_title"></label><label>Keterangan 2<input name="benefit_2_text"></label><label>Judul 3<input name="benefit_3_title"></label><label>Keterangan 3<input name="benefit_3_text"></label></div></div>
      <div class="form-section"><div><h2>Judul section</h2><p>Pengantar layanan dan portofolio.</p></div><div class="fields two"><label>Label layanan<input name="services_label"></label><label>Judul layanan<input name="services_title"></label><label class="wide">Deskripsi layanan<textarea name="services_description"></textarea></label><label>Teks link layanan<input name="service_link_text"></label><label>Label portofolio<input name="portfolio_label"></label><label>Judul portofolio<input name="portfolio_title"></label><label>CTA portofolio<input name="portfolio_cta"></label></div></div>
      <div class="form-section"><div><h2>Tentang</h2><p>Profil dan angka pencapaian.</p></div><div class="fields two"><label>Label<input name="about_label"></label><label>Judul<input name="about_title"></label><label class="wide">Deskripsi<textarea name="about_description"></textarea></label><label>Lama pengalaman<input name="experience_years"></label><label>Label pengalaman<input name="experience_label"></label><label>Statistik 1<input name="stat_1_value"></label><label>Label statistik 1<input name="stat_1_label"></label><label>Statistik 2<input name="stat_2_value"></label><label>Label statistik 2<input name="stat_2_label"></label><label>Statistik 3<input name="stat_3_value"></label><label>Label statistik 3<input name="stat_3_label"></label></div></div>
      <div class="form-section"><div><h2>Kontak & footer</h2><p>Ajakan, sosial, dan bagian bawah.</p></div><div class="fields two"><label>Label kontak<input name="contact_label"></label><label>Judul kontak<input name="contact_title"></label><label class="wide">Deskripsi kontak<textarea name="contact_description"></textarea></label><label>Teks tombol kontak<input name="contact_cta"></label><label>Email<input name="email" type="email"></label><label>Instagram URL<input name="instagram"></label><label>LinkedIn URL<input name="linkedin"></label><label class="wide">Deskripsi footer<textarea name="footer_description"></textarea></label><label class="wide">Copyright<input name="copyright"></label></div></div>
      <button class="primary save" type="submit">Simpan semua perubahan</button>
    </form>
  </section>
</main>
<dialog id="editor"><form method="dialog" id="editor-form"><div class="modal-head"><div><small id="modal-kicker">PORTOFOLIO</small><h2 id="modal-title">Tambah proyek</h2></div><button value="cancel" class="close">×</button></div><input type="hidden" name="id"><div id="dynamic-fields"></div><p class="form-error" id="editor-error"></p><div class="modal-actions"><button value="cancel">Batal</button><button type="submit" class="primary">Simpan</button></div></form></dialog>
</body></html>
