# LAPORAN PROYEK
# SISTEM MANAJEMEN PENYEWAAN KENDARAAN (VHRent)

**Mata Kuliah:** Pemrograman Web / Rekayasa Perangkat Lunak

---

## DAFTAR ISI

1. [BAB I - PENDAHULUAN](#bab-i-pendahuluan)
2. [BAB II - LANDASAN TEORI](#bab-ii-landasan-teori)
3. [BAB III - ISI DAN PEMBAHASAN](#bab-iii-isi-dan-pembahasan)
4. [BAB IV - PENUTUP](#bab-iv-penutup)

---

# BAB I PENDAHULUAN

## 1.1 Latar Belakang

Perkembangan teknologi informasi yang pesat telah membawa perubahan signifikan dalam berbagai sektor bisnis, termasuk industri penyewaan kendaraan. Bisnis rental kendaraan merupakan salah satu usaha yang berkembang seiring dengan meningkatnya kebutuhan mobilitas masyarakat, baik untuk keperluan pribadi, wisata, maupun bisnis.

Saat ini, banyak usaha rental kendaraan yang masih menggunakan sistem manual dalam mengelola operasional bisnisnya. Pencatatan data kendaraan, pelanggan, dan transaksi masih dilakukan dengan buku catatan atau spreadsheet sederhana. Hal ini menimbulkan beberapa permasalahan seperti:

1. **Kesulitan dalam pencarian data** - Mencari informasi kendaraan atau pelanggan membutuhkan waktu yang lama
2. **Risiko kehilangan data** - Data fisik rentan hilang atau rusak
3. **Penghitungan manual yang rawan kesalahan** - Perhitungan biaya sewa dan denda sering terjadi kesalahan
4. **Tidak ada pencatatan riwayat transaksi yang terstruktur** - Sulit melacak status kendaraan yang sedang disewa
5. **Pelanggan kesulitan melihat ketersediaan kendaraan** - Tidak ada platform untuk pelanggan melihat katalog

Berdasarkan permasalahan tersebut, diperlukan sebuah sistem informasi berbasis web yang dapat membantu mengelola seluruh proses bisnis rental kendaraan secara digital, efisien, dan terintegrasi. Sistem ini diharapkan dapat mempermudah admin dalam mengelola data serta memberikan kemudahan bagi pelanggan untuk melakukan pemesanan secara online.

## 1.2 Rumusan Masalah

Berdasarkan latar belakang yang telah diuraikan, rumusan masalah dalam proyek ini adalah:

1. Bagaimana merancang sistem informasi penyewaan kendaraan berbasis web yang dapat mengelola data kendaraan, pelanggan, dan transaksi secara efisien?

2. Bagaimana mengimplementasikan fitur perhitungan otomatis untuk biaya sewa dan denda keterlambatan pengembalian kendaraan?

3. Bagaimana menyediakan platform yang memudahkan pelanggan untuk melihat katalog kendaraan dan melakukan pemesanan secara online?

## 1.3 Tujuan

Tujuan dari pengembangan sistem ini adalah:

1. Merancang dan membangun sistem informasi penyewaan kendaraan berbasis web dengan fitur CRUD (Create, Read, Update, Delete) untuk data kendaraan, pelanggan, dan transaksi.

2. Mengimplementasikan fitur perhitungan otomatis biaya sewa berdasarkan durasi dan harga per hari, serta perhitungan denda keterlambatan.

3. Menyediakan halaman katalog kendaraan yang dapat diakses pelanggan beserta fitur pemesanan online dan notifikasi via WhatsApp.

## 1.4 Manfaat

### Manfaat Bagi Pengelola Rental:
- Mempermudah pengelolaan data kendaraan dan pelanggan
- Mengotomatisasi perhitungan biaya sewa dan denda
- Menyediakan laporan transaksi dan statistik pendapatan
- Mengurangi risiko kesalahan pencatatan manual
- Meningkatkan profesionalitas layanan

### Manfaat Bagi Pelanggan:
- Dapat melihat katalog kendaraan kapan saja dan di mana saja
- Kemudahan melakukan pemesanan online
- Mendapatkan invoice digital
- Komunikasi langsung dengan admin via WhatsApp

### Manfaat Akademis:
- Sebagai sarana pembelajaran pengembangan aplikasi web
- Implementasi konsep CRUD dan database relasional
- Penerapan arsitektur client-server

---

# BAB II LANDASAN TEORI

## 2.1 Tinjauan Pustaka

### 2.1.1 Sistem Informasi
Sistem informasi adalah suatu sistem dalam suatu organisasi yang mempertemukan kebutuhan pengolahan transaksi harian yang mendukung fungsi operasi organisasi yang bersifat manajerial dengan kegiatan strategi dari suatu organisasi untuk dapat menyediakan kepada pihak luar tertentu dengan laporan-laporan yang diperlukan (Sutabri, 2012).

### 2.1.2 Penyewaan Kendaraan
Penyewaan atau rental adalah suatu perjanjian di mana pemilik barang memberikan kesempatan kepada penyewa untuk menggunakan barang miliknya dalam jangka waktu tertentu dengan pembayaran sewa yang telah disepakati. Dalam konteks rental kendaraan, objek yang disewakan berupa kendaraan bermotor seperti mobil, motor, atau sepeda.

### 2.1.3 Aplikasi Berbasis Web
Aplikasi berbasis web adalah aplikasi yang diakses menggunakan web browser melalui jaringan internet atau intranet. Aplikasi web memiliki kelebihan yaitu dapat diakses dari berbagai perangkat tanpa perlu instalasi khusus.

## 2.2 Metode Pengembangan

Metode yang digunakan dalam pengembangan sistem ini adalah **Waterfall** (Air Terjun), yang terdiri dari tahapan:

```
┌─────────────────┐
│ 1. Analisis     │ → Mengidentifikasi kebutuhan sistem
└────────┬────────┘
         ▼
┌─────────────────┐
│ 2. Desain       │ → Merancang arsitektur dan database
└────────┬────────┘
         ▼
┌─────────────────┐
│ 3. Implementasi │ → Menulis kode program
└────────┬────────┘
         ▼
┌─────────────────┐
│ 4. Pengujian    │ → Testing fungsionalitas
└────────┬────────┘
         ▼
┌─────────────────┐
│ 5. Deployment   │ → Deploy ke server production
└─────────────────┘
```

## 2.3 UML (Unified Modeling Language)

UML adalah bahasa pemodelan standar yang digunakan untuk visualisasi, spesifikasi, konstruksi, dan dokumentasi sistem perangkat lunak. Diagram UML yang digunakan dalam proyek ini:

### 2.3.1 Use Case Diagram
Use Case Diagram menggambarkan fungsionalitas yang disediakan sistem dan interaksi antara aktor dengan sistem. Komponen utama:
- **Aktor**: Entitas eksternal yang berinteraksi dengan sistem
- **Use Case**: Fungsionalitas atau layanan yang disediakan sistem
- **Relasi**: Hubungan antara aktor dan use case (include, extend, generalization)

### 2.3.2 Activity Diagram
Activity Diagram menggambarkan aliran kerja (workflow) dari sebuah proses bisnis atau operasi sistem. Komponen utama:
- **Initial Node**: Titik awal aktivitas
- **Activity**: Aksi yang dilakukan
- **Decision**: Percabangan keputusan
- **Final Node**: Titik akhir aktivitas
- **Swimlane**: Partisi berdasarkan aktor/entitas

### 2.3.3 Sequence Diagram
Sequence Diagram menggambarkan interaksi antar objek dalam urutan waktu. Komponen utama:
- **Actor**: Pengguna sistem
- **Object/Participant**: Komponen sistem yang terlibat
- **Lifeline**: Garis waktu hidup objek
- **Message**: Pesan yang dikirim antar objek

## 2.4 Perangkat Lunak Pendukung

### 2.4.1 Frontend
| Teknologi | Deskripsi |
|-----------|-----------|
| **HTML5** | Struktur halaman web |
| **CSS3** | Styling dan layout |
| **JavaScript** | Interaktivitas dan logic frontend |

### 2.4.2 Backend
| Teknologi | Deskripsi |
|-----------|-----------|
| **PHP 8.x** | Server-side scripting language |
| **PDO** | PHP Data Objects untuk koneksi database |
| **REST API** | Arsitektur API untuk komunikasi client-server |

### 2.4.3 Database
| Teknologi | Deskripsi |
|-----------|-----------|
| **MySQL** | Relational Database Management System |

### 2.4.4 Development Tools
| Tool | Deskripsi |
|------|-----------|
| **Visual Studio Code** | Code editor |
| **XAMPP** | Local development server |
| **Git** | Version control system |
| **PlantUML** | Pembuatan diagram UML |

### 2.4.5 Deployment
| Platform | Deskripsi |
|----------|-----------|
| **Railway** | Cloud platform untuk PHP + MySQL |

---

# BAB III ISI DAN PEMBAHASAN

## 3.1 Analisis Kebutuhan

### 3.1.1 Kebutuhan Fungsional

**A. Modul Admin:**
| No | Kebutuhan | Deskripsi |
|----|-----------|-----------|
| 1 | Login/Logout | Admin dapat masuk dan keluar dari sistem |
| 2 | Registrasi | Admin baru dapat mendaftar akun |
| 3 | Lupa Password | Admin dapat reset password dengan verifikasi NIM |
| 4 | Kelola Kendaraan | CRUD data kendaraan (tambah, lihat, edit, hapus) |
| 5 | Kelola Pelanggan | CRUD data pelanggan |
| 6 | Kelola Transaksi | Buat transaksi sewa dan proses pengembalian |
| 7 | Kelola Denda | Lihat dan proses pembayaran denda |
| 8 | Lihat Laporan | Dashboard statistik dan laporan transaksi |

**B. Modul Pelanggan:**
| No | Kebutuhan | Deskripsi |
|----|-----------|-----------|
| 1 | Registrasi | Pelanggan dapat mendaftar akun |
| 2 | Login/Logout | Pelanggan dapat masuk dan keluar |
| 3 | Lihat Katalog | Melihat daftar kendaraan tersedia |
| 4 | Booking | Melakukan pemesanan kendaraan online |
| 5 | Lihat Invoice | Melihat detail invoice pemesanan |
| 6 | Hubungi WhatsApp | Konfirmasi pemesanan via WhatsApp |

### 3.1.2 Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|-------|-----------|
| **Usability** | Antarmuka user-friendly dan responsif |
| **Performance** | Waktu loading < 3 detik |
| **Security** | Password di-hash, session management |
| **Compatibility** | Berjalan di browser modern (Chrome, Firefox, Edge) |
| **Availability** | Dapat diakses 24/7 melalui internet |

### 3.1.3 Identifikasi Aktor

| Aktor | Deskripsi |
|-------|-----------|
| **Admin** | Pengelola rental yang memiliki akses penuh ke sistem manajemen |
| **Pelanggan** | Penyewa kendaraan yang dapat melihat katalog dan melakukan booking |

## 3.2 Perancangan Sistem

### 3.2.1 Use Case Diagram

```
@startuml
left to right direction

actor "Admin" as Admin
actor "Pelanggan" as Customer

rectangle "VHRent - Sistem Penyewaan Kendaraan" {
    usecase "Login" as UC_Login
    usecase "Registrasi" as UC_Register
    usecase "Lupa Password" as UC_ForgotPwd
    usecase "Kelola Kendaraan" as UC_ManageVehicle
    usecase "Kelola Pelanggan" as UC_ManageCustomer
    usecase "Kelola Transaksi" as UC_ManageTransaction
    usecase "Proses Pengembalian" as UC_ProcessReturn
    usecase "Kelola Denda" as UC_ManagePenalty
    usecase "Lihat Laporan" as UC_ViewReport
    usecase "Lihat Katalog" as UC_ViewCatalog
    usecase "Booking Kendaraan" as UC_Booking
    usecase "Lihat Invoice" as UC_ViewInvoice
    usecase "Hubungi WhatsApp" as UC_WhatsApp
}

Admin --> UC_Login
Admin --> UC_Register
Admin --> UC_ForgotPwd
Admin --> UC_ManageVehicle
Admin --> UC_ManageCustomer
Admin --> UC_ManageTransaction
Admin --> UC_ProcessReturn
Admin --> UC_ManagePenalty
Admin --> UC_ViewReport

UC_Login <-- Customer
UC_Register <-- Customer
UC_ViewCatalog <-- Customer
UC_Booking <-- Customer
UC_ViewInvoice <-- Customer
UC_WhatsApp <-- Customer

UC_Booking ..> UC_ViewInvoice : <<include>>
UC_ProcessReturn ..> UC_ManagePenalty : <<extend>>
@enduml
```

**Penjelasan Use Case:**

| Use Case | Deskripsi |
|----------|-----------|
| Login | Proses autentikasi user ke sistem |
| Registrasi | Pembuatan akun baru |
| Lupa Password | Reset password dengan verifikasi |
| Kelola Kendaraan | Manajemen data kendaraan |
| Kelola Pelanggan | Manajemen data pelanggan |
| Kelola Transaksi | Pembuatan transaksi sewa |
| Proses Pengembalian | Pengembalian kendaraan + hitung denda |
| Kelola Denda | Manajemen pembayaran denda |
| Lihat Laporan | Melihat statistik dan laporan |
| Lihat Katalog | Melihat daftar kendaraan |
| Booking Kendaraan | Pemesanan online |
| Lihat Invoice | Melihat detail tagihan |
| Hubungi WhatsApp | Konfirmasi via WhatsApp |

### 3.2.2 Activity Diagram

**A. Activity Diagram - Login**
```
@startuml
|User|
start
:Buka Halaman Login;
:Input Username & Password;
:Klik Login;

|Sistem|
:Validasi Input;
:Cek Database;

if (Valid?) then (ya)
    :Buat Session;
    |User|
    :Masuk Dashboard;
    stop
else (tidak)
    |User|
    :Lihat Pesan Error;
    stop
endif
@enduml
```

**B. Activity Diagram - Booking Kendaraan**
```
@startuml
|Pelanggan|
start
:Klik Booking;

|Sistem|
:Cek Login;

if (Sudah Login?) then (tidak)
    |Pelanggan|
    :Login/Registrasi;
endif

|Sistem|
:Tampilkan Form Booking;

|Pelanggan|
:Input Tanggal Sewa;
:Klik Konfirmasi;

|Sistem|
:Hitung Total Biaya;
:Simpan Pemesanan;
:Generate Invoice;

|Pelanggan|
:Lihat Invoice;
stop
@enduml
```

**C. Activity Diagram - Proses Pengembalian**
```
@startuml
|Admin|
start
:Pilih Transaksi Aktif;
:Klik Proses Kembali;
:Input Tanggal Kembali;

|Sistem|
:Hitung Selisih Hari;

if (Terlambat?) then (ya)
    :Hitung Denda;
    :Simpan Data Denda;
    :Update Status Terlambat;
else (tidak)
    :Update Status Dikembalikan;
endif

:Tambah Stok Kendaraan;

|Admin|
:Lihat Hasil;
stop
@enduml
```

### 3.2.3 Sequence Diagram

**A. Sequence Diagram - Login**
```
@startuml
actor User
participant "Halaman Login" as UI
participant "auth.php" as API
database "Database" as DB

User -> UI : Input username & password
User -> UI : Klik Login
UI -> API : POST /auth.php?action=login
API -> DB : SELECT * FROM admins
DB --> API : Data user
API -> API : password_verify()
alt Password Cocok
    API --> UI : {success: true}
    UI --> User : Redirect ke Dashboard
else Password Salah
    API --> UI : {success: false}
    UI --> User : Tampilkan error
end
@enduml
```

**B. Sequence Diagram - Buat Transaksi**
```
@startuml
actor Admin
participant "Form Transaksi" as UI
participant "transaksi.php" as API
database "Database" as DB

Admin -> UI : Pilih pelanggan & kendaraan
Admin -> UI : Input tanggal sewa
UI -> API : POST /transaksi.php
API -> API : Generate kode transaksi
API -> API : Hitung total biaya
API -> DB : INSERT INTO transaksi
API -> DB : UPDATE stok kendaraan
DB --> API : Success
API --> UI : {success: true}
UI --> Admin : Tampilkan invoice
@enduml
```

## 3.3 Perancangan Database

### 3.3.1 Entity Relationship Diagram (ERD)

```
┌─────────────────┐       ┌─────────────────┐
│     ADMINS      │       │    PELANGGAN    │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │
│ username        │       │ nama            │
│ password        │       │ nik             │
│ nama_lengkap    │       │ no_telepon      │
│ nim             │       │ email           │
│ email           │       │ alamat          │
│ created_at      │       │ jenis_kelamin   │
└─────────────────┘       │ password        │
                          │ is_registered   │
                          └────────┬────────┘
                                   │
                                   │ 1:N
                                   ▼
┌─────────────────┐       ┌─────────────────┐
│    KENDARAAN    │       │    TRANSAKSI    │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄──────│ id (PK)         │
│ nama            │  1:N  │ kode_transaksi  │
│ jenis           │       │ pelanggan_id(FK)│
│ merk            │       │ kendaraan_id(FK)│
│ tahun           │       │ tanggal_sewa    │
│ plat_nomor      │       │ tanggal_kembali │
│ harga_sewa      │       │ jumlah_hari     │
│ stok            │       │ harga_per_hari  │
│ stok_tersedia   │       │ total_biaya     │
│ gambar_url      │       │ status          │
│ status          │       │ admin_id (FK)   │
└─────────────────┘       └────────┬────────┘
                                   │
                                   │ 1:1
                                   ▼
                          ┌─────────────────┐
                          │      DENDA      │
                          ├─────────────────┤
                          │ id (PK)         │
                          │ transaksi_id(FK)│
                          │ hari_terlambat  │
                          │ denda_per_hari  │
                          │ total_denda     │
                          │ status_bayar    │
                          └─────────────────┘
```

### 3.3.2 Struktur Tabel

**Tabel: admins**
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key, Auto Increment |
| username | VARCHAR(50) | Username unik |
| password | VARCHAR(255) | Password ter-hash |
| nama_lengkap | VARCHAR(100) | Nama lengkap admin |
| nim | VARCHAR(9) | NIM untuk reset password |
| email | VARCHAR(100) | Email admin |

**Tabel: pelanggan**
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key |
| nama | VARCHAR(100) | Nama pelanggan |
| nik | VARCHAR(16) | NIK (unik) |
| no_telepon | VARCHAR(15) | Nomor telepon |
| email | VARCHAR(100) | Email |
| password | VARCHAR(255) | Password untuk pelanggan terdaftar |
| is_registered | TINYINT | Flag pelanggan terdaftar |

**Tabel: kendaraan**
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key |
| nama | VARCHAR(100) | Nama kendaraan |
| jenis | ENUM | Mobil/Motor/Sepeda/Truk/Bus |
| merk | VARCHAR(50) | Merk kendaraan |
| plat_nomor | VARCHAR(20) | Plat nomor (unik) |
| harga_sewa_per_hari | DECIMAL(12,2) | Harga sewa per hari |
| stok | INT | Total stok |
| stok_tersedia | INT | Stok yang tersedia |

**Tabel: transaksi**
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key |
| kode_transaksi | VARCHAR(20) | Kode unik transaksi |
| pelanggan_id | INT | Foreign Key ke pelanggan |
| kendaraan_id | INT | Foreign Key ke kendaraan |
| tanggal_sewa | DATE | Tanggal mulai sewa |
| tanggal_kembali_rencana | DATE | Tanggal rencana kembali |
| tanggal_kembali_aktual | DATE | Tanggal kembali aktual |
| total_biaya | DECIMAL(12,2) | Total biaya sewa |
| status | ENUM | Disewa/Dikembalikan/Terlambat/Dibatalkan |

**Tabel: denda**
| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| id | INT | Primary Key |
| transaksi_id | INT | Foreign Key ke transaksi |
| jumlah_hari_terlambat | INT | Jumlah hari terlambat |
| denda_per_hari | DECIMAL(12,2) | Denda per hari (10% harga sewa) |
| total_denda | DECIMAL(12,2) | Total denda |
| status_pembayaran | ENUM | Belum Dibayar/Lunas |

## 3.4 Implementasi Sistem

### 3.4.1 Struktur Direktori

```
vhrent/
├── backend/                 # API PHP
│   ├── config.php          # Konfigurasi database
│   ├── auth.php            # API autentikasi
│   ├── kendaraan.php       # API kendaraan
│   ├── pelanggan.php       # API pelanggan
│   ├── transaksi.php       # API transaksi
│   ├── denda.php           # API denda
│   ├── laporan.php         # API laporan
│   └── pemesanan.php       # API booking pelanggan
├── database/
│   └── vhrent.sql          # Script database
├── docs/                    # Dokumentasi
│   ├── use_case_diagram.txt
│   ├── activity_diagrams.txt
│   └── sequence_diagrams.txt
├── assets/                  # Asset statis
├── index.html              # Halaman admin (SPA)
├── index.php               # Halaman pelanggan
├── nixpacks.toml           # Konfigurasi Railway
└── README.md               # Dokumentasi
```

### 3.4.2 Implementasi Fitur Utama

**A. Koneksi Database (config.php)**
```php
// Konfigurasi menggunakan environment variables untuk deployment
define('DB_HOST', getenv('MYSQL_HOST') ?: 'localhost');
define('DB_USER', getenv('MYSQL_USER') ?: 'root');
define('DB_PASS', getenv('MYSQL_PASSWORD') ?: '');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'vhrent');
define('DB_PORT', getenv('MYSQL_PORT') ?: '3306');

class Database {
    public function __construct() {
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME;
        $this->conn = new PDO($dsn, DB_USER, DB_PASS);
    }
}
```

**B. Perhitungan Biaya Sewa**
```javascript
// Frontend - Kalkulasi otomatis
const hariSewa = selisihHari(tanggalMulai, tanggalSelesai);
const totalBiaya = hariSewa * hargaPerHari;
```

**C. Perhitungan Denda**
```php
// Backend - 10% per hari keterlambatan
$hariTerlambat = selisihHari($tanggalKembaliRencana, $tanggalKembaliAktual);
$dendaPerHari = $hargaSewa * 0.10;
$totalDenda = $hariTerlambat * $dendaPerHari;
```

**D. Integrasi WhatsApp**
```javascript
// Generate link WhatsApp dengan pesan otomatis
const pesan = `Halo, saya ingin konfirmasi booking:
Kode: ${kodeTransaksi}
Kendaraan: ${namaKendaraan}
Total: Rp ${totalBiaya}`;

const waLink = `https://wa.me/6281234567890?text=${encodeURIComponent(pesan)}`;
window.open(waLink);
```

### 3.4.3 Screenshot Tampilan

**A. Halaman Login Admin**
- Form login dengan input username dan password
- Link ke halaman registrasi dan lupa password

**B. Dashboard Admin**
- Statistik total kendaraan, pelanggan, transaksi
- Grafik pendapatan
- Daftar transaksi aktif

**C. Halaman Katalog Pelanggan**
- Grid kendaraan dengan gambar
- Filter berdasarkan jenis kendaraan
- Tombol booking pada setiap kendaraan

**D. Form Booking**
- Pilihan tanggal sewa dan kembali
- Kalkulasi otomatis total biaya
- Tombol konfirmasi

**E. Invoice**
- Detail lengkap transaksi
- QR Code (opsional)
- Tombol cetak dan WhatsApp

---

# BAB IV PENUTUP

## 4.1 Kesimpulan

Berdasarkan hasil perancangan dan implementasi Sistem Manajemen Penyewaan Kendaraan (VHRent), dapat diambil kesimpulan sebagai berikut:

1. **Menjawab Rumusan Masalah 1:**
   Sistem informasi penyewaan kendaraan berbasis web telah berhasil dirancang dan diimplementasikan dengan fitur CRUD lengkap untuk mengelola data kendaraan, pelanggan, dan transaksi. Sistem menggunakan arsitektur client-server dengan frontend berbasis HTML/CSS/JavaScript dan backend PHP dengan database MySQL. Semua data tersimpan secara terstruktur dan dapat diakses dengan mudah melalui antarmuka yang user-friendly.

2. **Menjawab Rumusan Masalah 2:**
   Fitur perhitungan otomatis telah berhasil diimplementasikan dalam sistem. Perhitungan biaya sewa dilakukan secara otomatis berdasarkan formula: **Total Biaya = Jumlah Hari × Harga per Hari**. Sistem juga dapat menghitung denda keterlambatan secara otomatis dengan formula: **Denda = Hari Terlambat × 10% × Harga Sewa per Hari**. Perhitungan ini mengurangi risiko kesalahan yang sering terjadi pada sistem manual.

3. **Menjawab Rumusan Masalah 3:**
   Platform pelanggan telah berhasil dikembangkan dengan halaman katalog yang menampilkan daftar kendaraan beserta ketersediaan stok. Pelanggan dapat melakukan registrasi, login, dan melakukan pemesanan kendaraan secara online. Setelah booking, sistem menghasilkan invoice digital dan menyediakan tombol WhatsApp untuk konfirmasi langsung dengan admin rental. Fitur ini meningkatkan kemudahan akses dan pengalaman pelanggan.

## 4.2 Saran

Untuk pengembangan sistem lebih lanjut, disarankan:

1. Menambahkan fitur pembayaran online terintegrasi (payment gateway)
2. Mengembangkan aplikasi mobile untuk pelanggan
3. Menambahkan fitur notifikasi email/SMS untuk pengingat pengembalian
4. Implementasi sistem rating dan review kendaraan
5. Menambahkan fitur multi-cabang untuk ekspansi bisnis

---

# DAFTAR PUSTAKA

1. Sutabri, T. (2012). Konsep Sistem Informasi. Yogyakarta: Andi Offset.
2. Pressman, R. S. (2014). Software Engineering: A Practitioner's Approach (8th ed.). McGraw-Hill.
3. Sommerville, I. (2015). Software Engineering (10th ed.). Pearson.
4. PHP Documentation. (2024). https://www.php.net/docs.php
5. MySQL Documentation. (2024). https://dev.mysql.com/doc/

---

# LAMPIRAN

## Lampiran A: Kode PlantUML Diagram
Lihat file:
- `docs/use_case_diagram.txt`
- `docs/activity_diagrams.txt`
- `docs/sequence_diagrams.txt`

## Lampiran B: Script Database
Lihat file: `database/vhrent.sql`

## Lampiran C: Panduan Deployment
Lihat file: `README.md`

---
*Laporan ini dibuat sebagai dokumentasi proyek VHRent - Vehicle Rental Management System*
