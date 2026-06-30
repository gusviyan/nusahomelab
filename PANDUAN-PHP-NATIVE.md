# Nusa HomeLab PHP Native

Nusa HomeLab menggunakan PHP native tanpa Laravel, Composer, Node.js, atau package eksternal.
Backend memakai PDO MySQL, session PHP, `password_hash`, validasi upload, dan router sederhana.

## Menjalankan

Pastikan MySQL Laragon aktif, lalu dari folder proyek jalankan:

```powershell
& 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe' `
  -S 127.0.0.1:8000 -t public public/index.php
```

Buka:

- Website: `http://127.0.0.1:8000`
- Admin: `http://127.0.0.1:8000/admin`

Konfigurasi database dan akun admin dibaca dari `.env`. Database dan tabel dibuat otomatis saat
permintaan API pertama. Data yang sudah ada tidak ditimpa.

Untuk Laragon/Apache, arahkan document root ke folder `public`. File `.htaccess` sudah menangani
route dinamis.

## Struktur utama

- `public/index.php`: router halaman dan seluruh endpoint API
- `src/bootstrap.php`: pembaca `.env`, session, respons JSON, validasi, dan upload
- `src/Database.php`: koneksi PDO, pembuatan tabel, dan seed awal
- `views/home.php` dan `views/login.php`: halaman utama dan login
- `views/admin/*.php`: halaman dashboard admin
- `public/*.php`: halaman publik tambahan
- `public/css`, `public/js`, dan `public/uploads`: aset frontend dan gambar

Semua halaman menggunakan ekstensi `.php`; tidak ada halaman `.html` yang menjadi bagian runtime.
