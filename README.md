# VHRent - Vehicle Rental Management System

Sistem manajemen penyewaan kendaraan berbasis web dengan PHP dan MySQL.

## 🚀 Deploy ke Railway

### Langkah 1: Persiapan
1. Buat akun di [Railway.app](https://railway.app/)
2. Install [Git](https://git-scm.com/) jika belum ada
3. Login ke Railway menggunakan GitHub

### Langkah 2: Setup Database MySQL di Railway
1. Di Railway Dashboard, klik **"New Project"**
2. Pilih **"Provision MySQL"** 
3. Setelah MySQL terbuat, klik service MySQL tersebut
4. Pergi ke tab **"Variables"** dan catat nilai-nilai berikut:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

### Langkah 3: Import Database
1. Di service MySQL, pergi ke tab **"Data"**
2. Klik **"Connect"** untuk membuka koneksi
3. Copy dan paste isi file `database/vhrent.sql` untuk membuat struktur tabel

### Langkah 4: Deploy Aplikasi PHP
1. Di project yang sama, klik **"New"** → **"GitHub Repo"**
2. Connect repository GitHub Anda yang berisi project ini
3. Railway akan otomatis mendeteksi PHP dan melakukan deploy
4. Pergi ke tab **"Variables"** di service PHP, tambahkan:
   ```
   MYSQL_HOST = [nilai dari MYSQLHOST]
   MYSQL_PORT = [nilai dari MYSQLPORT]
   MYSQL_DATABASE = [nilai dari MYSQLDATABASE]
   MYSQL_USER = [nilai dari MYSQLUSER]
   MYSQL_PASSWORD = [nilai dari MYSQLPASSWORD]
   ```

### Langkah 5: Generate Domain
1. Pergi ke **Settings** → **Networking**
2. Klik **"Generate Domain"**
3. Aplikasi Anda akan tersedia di URL yang diberikan!

## 📁 Struktur Project

```
vhrent/
├── backend/           # PHP API files
│   ├── config.php     # Database configuration
│   ├── auth.php       # Authentication API
│   ├── kendaraan.php  # Vehicles API
│   ├── pelanggan.php  # Customers API
│   ├── transaksi.php  # Transactions API
│   └── ...
├── database/          # SQL files
│   └── vhrent.sql     # Database schema
├── assets/            # Static assets
├── index.php          # Customer frontend
├── index.html         # Admin frontend
├── nixpacks.toml      # Railway config
└── README.md
```

## 💻 Development Lokal (XAMPP)

1. Clone project ke folder `htdocs` XAMPP
2. Import `database/vhrent.sql` ke phpMyAdmin
3. Akses via `http://localhost/vhrent`

## 🔐 Default Login Admin

- **Username:** admin
- **Password:** admin123

## 📝 Catatan

- Railway Free Trial memberikan $5 credit
- Untuk kebutuhan production, upgrade ke paid plan
- Database MySQL Railway akan sleep jika tidak aktif (free tier)

## 👨‍💻 Developer

VHRent - Vehicle Rental Management System
# rentalinCo
# rentalinCo
