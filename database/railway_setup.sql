-- =====================================================
-- VHRent - Railway MySQL Setup Script
-- =====================================================
-- Jalankan SQL ini di Railway MySQL Query Editor
-- Caranya: Klik MySQL service -> Data tab -> Query

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nim VARCHAR(9) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kendaraan (Master)
CREATE TABLE IF NOT EXISTS kendaraan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis ENUM('Mobil', 'Motor', 'Sepeda', 'Truk', 'Bus') NOT NULL,
    merk VARCHAR(50) NOT NULL,
    tahun INT NOT NULL,
    plat_nomor VARCHAR(20) NOT NULL UNIQUE,
    warna VARCHAR(30),
    harga_sewa_per_hari DECIMAL(12, 2) NOT NULL,
    stok INT DEFAULT 1,
    stok_tersedia INT DEFAULT 1,
    gambar_url TEXT,
    deskripsi TEXT,
    status ENUM('Tersedia', 'Tidak Tersedia') DEFAULT 'Tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Pelanggan (Master)
CREATE TABLE IF NOT EXISTS pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nik VARCHAR(16) NOT NULL UNIQUE,
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    alamat TEXT,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tanggal_lahir DATE,
    no_sim VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Transaksi Penyewaan
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(20) NOT NULL UNIQUE,
    pelanggan_id INT NOT NULL,
    kendaraan_id INT NOT NULL,
    tanggal_sewa DATE NOT NULL,
    tanggal_kembali_rencana DATE NOT NULL,
    tanggal_kembali_aktual DATE,
    jumlah_hari INT NOT NULL,
    harga_per_hari DECIMAL(12, 2) NOT NULL,
    total_biaya DECIMAL(12, 2) NOT NULL,
    status ENUM('Menunggu', 'Disewa', 'Dikembalikan', 'Terlambat', 'Dibatalkan', 'Ditolak') DEFAULT 'Menunggu',
    catatan TEXT,
    admin_id INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE RESTRICT,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Tabel Denda
CREATE TABLE IF NOT EXISTS denda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL UNIQUE,
    jumlah_hari_terlambat INT NOT NULL,
    denda_per_hari DECIMAL(12, 2) NOT NULL,
    total_denda DECIMAL(12, 2) NOT NULL,
    status_pembayaran ENUM('Belum Dibayar', 'Lunas') DEFAULT 'Belum Dibayar',
    tanggal_bayar DATE,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE
);

-- Tabel Setting (untuk denda per hari dll)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password, nama_lengkap, nim, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '123456789', 'admin@vhrent.com')
ON DUPLICATE KEY UPDATE username = username;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('denda_per_hari_persen', '10', 'Persentase denda per hari dari harga sewa'),
('nama_aplikasi', 'VHRent', 'Nama aplikasi'),
('versi', '1.0.0', 'Versi aplikasi')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
