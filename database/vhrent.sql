-- =====================================================
-- VHRent - Vehicle Rental Management System Database
-- =====================================================
-- Note: Jalankan SQL ini di Railway MySQL setelah database terbuat
-- Railway akan membuat database dengan nama sendiri (railway)

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
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '123456789', 'admin@vhrent.com');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('denda_per_hari_persen', '10', 'Persentase denda per hari dari harga sewa'),
('nama_aplikasi', 'VHRent', 'Nama aplikasi'),
('versi', '1.0.0', 'Versi aplikasi');

-- Insert sample kendaraan
INSERT INTO kendaraan (nama, jenis, merk, tahun, plat_nomor, warna, harga_sewa_per_hari, stok, stok_tersedia, gambar_url, deskripsi) VALUES
('Avanza Veloz', 'Mobil', 'Toyota', 2023, 'B 1234 ABC', 'Putih', 450000.00, 3, 3, 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=500', 'Toyota Avanza Veloz 2023 dengan fitur lengkap dan nyaman untuk keluarga'),
('Innova Reborn', 'Mobil', 'Toyota', 2022, 'B 5678 DEF', 'Hitam', 650000.00, 2, 2, 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=500', 'Toyota Innova Reborn dengan interior premium'),
('Beat Street', 'Motor', 'Honda', 2023, 'B 9012 GHI', 'Merah', 75000.00, 5, 5, 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=500', 'Honda Beat Street cocok untuk mobilitas harian'),
('NMAX', 'Motor', 'Yamaha', 2023, 'B 3456 JKL', 'Biru', 125000.00, 4, 4, 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=500', 'Yamaha NMAX dengan fitur ABS dan teknologi terbaru'),
('HR-V', 'Mobil', 'Honda', 2023, 'B 7890 MNO', 'Silver', 550000.00, 2, 2, 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=500', 'Honda HR-V SUV stylish dengan konsumsi BBM efisien');

-- Insert sample pelanggan
INSERT INTO pelanggan (nama, nik, no_telepon, email, alamat, jenis_kelamin, tanggal_lahir, no_sim) VALUES
('Hadi Permana', '3201234567890001', '081234567890', 'budi@email.com', 'Jl. Merdeka No. 123, Jakarta Pusat', 'Laki-laki', '1990-05-15', 'A123456789'),
('Akram Satya', '3201234567890002', '082345678901', 'siti@email.com', 'Jl. Sudirman No. 456, Jakarta Selatan', 'Perempuan', '1995-08-20', 'B234567890'),
('Navin Rivaldo', '3201234567890003', '083456789012', 'ahmad@email.com', 'Jl. Gatot Subroto No. 789, Jakarta Barat', 'Laki-laki', '1988-12-10', 'C345678901');
