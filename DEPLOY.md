# 📋 Panduan Deploy VHRent ke Shared Hosting

## Persyaratan Hosting
- PHP 7.4 atau lebih tinggi (disarankan PHP 8.x)
- MySQL 5.7 atau lebih tinggi
- Ekstensi PHP: PDO, pdo_mysql, mbstring, json

---

## Langkah-langkah Deployment

### 1️⃣ Persiapan di cPanel

1. **Login ke cPanel** hosting Anda
2. **Buat Database MySQL:**
   - Buka **MySQL Databases**
   - Buat database baru (contoh: `username_vhrent`)
   - Buat user database baru
   - Tambahkan user ke database dengan **ALL PRIVILEGES**

3. **Catat informasi database:**
   ```
   Host: localhost
   Database: username_vhrent
   Username: username_dbuser
   Password: password_anda
   ```

---

### 2️⃣ Upload File

**Opsi A: Via File Manager cPanel**
1. Buka **File Manager** di cPanel
2. Masuk ke folder `public_html` (atau subdomain folder)
3. Upload semua file dari folder `vhrent`
4. Pastikan struktur folder seperti ini:
   ```
   public_html/
   ├── backend/
   ├── assets/
   ├── database/
   ├── img/
   ├── index.html
   └── index.php
   ```

**Opsi B: Via FTP**
1. Gunakan FileZilla atau FTP client lainnya
2. Connect ke hosting dengan kredensial FTP
3. Upload ke folder `public_html`

---

### 3️⃣ Konfigurasi Database

1. **Edit file `backend/config.php`**
2. Ubah bagian konfigurasi database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'username_dbuser');    // Username dari cPanel
   define('DB_PASS', 'password_anda');      // Password yang Anda buat
   define('DB_NAME', 'username_vhrent');    // Nama database
   define('DB_PORT', '3306');
   ```

---

### 4️⃣ Import Database

1. Buka **phpMyAdmin** di cPanel
2. Pilih database yang sudah dibuat
3. Klik tab **Import**
4. Upload file `database/vhrent.sql`
5. Klik **Go** untuk import
6. Ulangi untuk file `database/migration_online_orders.sql`

---

### 5️⃣ Setting Domain (Opsional)

Jika menggunakan custom domain:
1. Arahkan nameserver domain ke hosting
2. Atau tambahkan domain di **Addon Domains** / **Subdomains**

---

### 6️⃣ Testing

1. Buka website Anda di browser
2. Akses landing page: `https://domain-anda.com/`
3. Akses admin panel: `https://domain-anda.com/index.html`
4. Login admin default:
   - Username: `admin`
   - Password: (sesuai yang ada di database)

---

## 🔐 Keamanan (PENTING!)

Setelah deploy berhasil:

1. **Ubah password admin** dari panel admin
2. **Hapus folder `database/`** dari server (sudah tidak diperlukan)
3. Pastikan **CORS** di `config.php` tidak menggunakan `*` di production
   - Ubah `Access-Control-Allow-Origin: *` menjadi domain Anda

---

## 🔧 Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Error 500 | Cek error log di cPanel, pastikan PHP version sesuai |
| Database error | Verifikasi kredensial di config.php |
| Blank page | Aktifkan `display_errors` sementara untuk debug |
| File permission | Set folder ke 755, file ke 644 |

---

## 📁 Struktur File untuk Upload

```
vhrent/
├── backend/           ← API PHP
│   ├── config.php     ← ⚠️ EDIT INI
│   ├── auth.php
│   ├── kendaraan.php
│   ├── pelanggan.php
│   ├── pemesanan.php
│   ├── transaksi.php
│   ├── laporan.php
│   └── denda.php
├── assets/
│   ├── css/
│   └── js/
├── database/          ← Import ke MySQL, lalu hapus
│   ├── vhrent.sql
│   └── migration_online_orders.sql
├── img/
├── docs/              ← Opsional, bisa tidak diupload
├── index.html         ← Admin Panel
├── index.php          ← Landing Page Customer
└── README.md
```

---

© 2026 rentalinCo - Deployment Guide
