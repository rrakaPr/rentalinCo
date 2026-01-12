<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="rentalinCo by rakaRent - Layanan Penyewaan Kendaraan Terpercaya. Tersedia berbagai jenis kendaraan dengan harga terjangkau.">
    <meta name="keywords" content="rental mobil, sewa motor, rental kendaraan, rentalinCo, rakaRent">
    <title>rentalinCo by rakaRent - Rental Kendaraan Terpercaya</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing.css">



    <!-- PDF Generation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body class="landing-body">
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="container">
            <div class="nav-content">
                <a href="#" class="nav-brand">
                    <div class="nav-brand">
                        <img src="img/logo.png" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
                        <div class="nav-brand-text" style="display: flex; flex-direction: column; line-height: 1.1;">
                            <span style="color: var(--primary-600); font-weight: 700;">rentalinCo</span>
                            <span style="font-size: 0.55em; opacity: 0.7; font-weight: 400; text-transform: none;">by
                                rakaRent</span>
                        </div>
                    </div>
                </a>
                <div class="nav-links" id="navLinks">
                    <a href="#home" class="nav-link">Beranda</a>
                    <a href="#vehicles" class="nav-link">Kendaraan</a>
                    <a href="#features" class="nav-link">Keunggulan</a>
                    <a href="#contact" class="nav-link">Kontak</a>
                </div>
                <div class="nav-actions" id="navActions">
                    <!-- Dynamic: Login/User info -->
                    <a href="index.html" class="btn btn-ghost btn-sm" title="Panel Admin">
                        <i class="ri-settings-3-line"></i> Admin
                    </a>
                    <button class="btn btn-secondary" onclick="openModal('loginModal')" id="btnLogin">
                        <i class="ri-login-box-line"></i> Login
                    </button>
                    <button class="btn btn-primary" onclick="openModal('registerModal')" id="btnRegister">
                        <i class="ri-user-add-line"></i> Daftar
                    </button>
                    <div class="user-menu hidden" id="userMenu">
                        <span class="user-greeting">Halo, <strong id="userName"></strong></span>
                        <button class="btn btn-ghost btn-sm" onclick="logout()">
                            <i class="ri-logout-box-r-line"></i> Keluar
                        </button>
                    </div>
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="ri-menu-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="ri-shield-check-line"></i> Layanan Terpercaya
                </div>
                <h1 class="hero-title">
                    Sewa Kendaraan <span class="text-gradient">Mudah & Cepat</span>
                </h1>
                <p class="hero-subtitle">
                    Tersedia berbagai pilihan kendaraan berkualitas dengan harga terjangkau.
                    Proses mudah, armada terawat, dan layanan 24 jam.
                </p>
                <div class="hero-actions">
                    <a href="#vehicles" class="btn btn-primary btn-lg">
                        <i class="ri-car-line"></i> Lihat Kendaraan
                    </a>
                    <a href="#contact" class="btn btn-secondary btn-lg">
                        <i class="ri-phone-line"></i> Hubungi Kami
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value" id="statVehicles">0</div>
                        <div class="hero-stat-label">Unit Kendaraan</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value" id="statCustomers">0</div>
                        <div class="hero-stat-label">Pelanggan Puas</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">24/7</div>
                        <div class="hero-stat-label">Layanan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicles Section -->
    <section class="section vehicles-section" id="vehicles">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Armada Kami</div>
                <h2 class="section-title">Pilihan Kendaraan Tersedia</h2>
                <p class="section-subtitle">
                    Berbagai jenis kendaraan siap menemani perjalanan Anda
                </p>
            </div>

            <!-- Filter -->
            <div class="vehicle-filters">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="Mobil">Mobil</button>
                <button class="filter-btn" data-filter="Motor">Motor</button>
                <button class="filter-btn" data-filter="Sepeda">Sepeda</button>
            </div>

            <!-- Vehicle Grid -->
            <div class="vehicle-grid" id="vehicleGrid">
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features-section" id="features">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Mengapa Kami?</div>
                <h2 class="section-title">Keunggulan rentalinCo</h2>
                <p class="section-subtitle">
                    Kami berkomitmen memberikan layanan terbaik untuk Anda
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-wallet-3-line"></i></div>
                    <h3 class="feature-title">Harga Transparan</h3>
                    <p class="feature-desc">Tanpa biaya tersembunyi. Harga yang tertera sudah termasuk semua biaya
                        dasar.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-shield-check-line"></i></div>
                    <h3 class="feature-title">Kendaraan Terawat</h3>
                    <p class="feature-desc">Semua kendaraan rutin diservis dan dicek kelayakan jalan sebelum disewakan.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-customer-service-2-line"></i></div>
                    <h3 class="feature-title">Support 24/7</h3>
                    <p class="feature-desc">Tim kami siap membantu kapan saja jika Anda mengalami kendala.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-file-list-3-line"></i></div>
                    <h3 class="feature-title">Proses Mudah</h3>
                    <p class="feature-desc">Cukup siapkan KTP dan SIM, proses sewa cepat dan tidak ribet.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-map-pin-line"></i></div>
                    <h3 class="feature-title">Antar Jemput</h3>
                    <p class="feature-desc">Layanan antar jemput kendaraan tersedia untuk kemudahan Anda.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-secure-payment-line"></i></div>
                    <h3 class="feature-title">Pembayaran Fleksibel</h3>
                    <p class="feature-desc">Terima berbagai metode pembayaran: tunai, transfer, dan e-wallet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title" style="color: white;">Keunggulan rentalinCo</h2>
                <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9);">Mengapa memilih layanan kami untuk
                    kebutuhan transportasi Anda?</p>
            </div>
            <div class="cta-content">
                <h2 class="cta-title">Siap untuk menyewa?</h2>
                <p class="cta-subtitle">Daftar sekarang dan pesan kendaraan favorit Anda!</p>
                <button class="btn btn-primary btn-lg" onclick="openModal('registerModal')">
                    <i class="ri-user-add-line"></i> Daftar Sekarang
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact-section" id="contact">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Hubungi Kami</div>
                <h2 class="section-title">Informasi Kontak</h2>
                <p class="section-subtitle">Silakan hubungi kami untuk reservasi atau pertanyaan</p>
            </div>

            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon"><i class="ri-map-pin-2-line"></i></div>
                    <h3>Alamat</h3>
                    <p>Jl. Raya Utama No. 123<br>Jakarta Pusat, Indonesia</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon"><i class="ri-phone-line"></i></div>
                    <h3>Telepon</h3>
                    <p>+62 812 3456 7890<br>+62 21 1234 5678</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon"><i class="ri-mail-line"></i></div>
                    <h3>Email</h3>
                    <p>info@vhrent.com<br>cs@vhrent.com</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon"><i class="ri-time-line"></i></div>
                    <h3>Jam Operasional</h3>
                    <p>Senin - Sabtu: 08:00 - 20:00<br>Minggu: 09:00 - 17:00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="nav-brand">
                        <img src="img/logo.png" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
                        <div class="nav-brand-text" style="display: flex; flex-direction: column; line-height: 1.1;">
                            <span style="color: #60a5fa; font-weight: 700;">rentalinCo</span>
                            <span
                                style="font-size: 0.55em; opacity: 0.7; font-weight: 400; text-transform: none; color: rgba(255,255,255,0.6);">by
                                rakaRent</span>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Layanan penyewaan kendaraan terpercaya dengan armada berkualitas dan harga kompetitif.
                    </p>
                </div>
                <div class="footer-links">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#vehicles">Kendaraan</a></li>
                        <li><a href="#features">Keunggulan</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="#">Sewa Mobil</a></li>
                        <li><a href="#">Sewa Motor</a></li>
                        <li><a href="#">Antar Jemput</a></li>
                        <li><a href="#">Paket Wisata</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Ikuti Kami</h4>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="ri-instagram-line"></i></a>
                        <a href="#" class="social-link"><i class="ri-facebook-circle-line"></i></a>
                        <a href="#" class="social-link"><i class="ri-twitter-x-line"></i></a>
                        <a href="#" class="social-link"><i class="ri-whatsapp-line"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 rentalinCo by rakaRent - Vehicle Rental Management System</p>
                <p class="footer-developer">Developed by <strong>M. Rizky Raka Pratama</strong> | NIM:
                    <strong>312210397</strong>
                </p>
            </div>
        </div>
    </footer>

    <!-- ==================== MODALS ==================== -->

    <!-- Login Modal -->
    <div id="loginModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Login Pelanggan</h3>
                <button class="modal-close" onclick="closeModal('loginModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-6">Masuk ke akun Anda untuk memesan kendaraan.</p>
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="email@contoh.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <i class="ri-login-box-line"></i> Masuk
                    </button>
                </form>
            </div>
            <div class="modal-footer" style="justify-content:center; border-top:none; background:transparent;">
                <span class="text-muted">Belum punya akun?</span>
                <a href="#" onclick="closeModal('loginModal'); openModal('registerModal'); return false;"
                    style="margin-left:4px">
                    Daftar di sini
                </a>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal-overlay">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 class="modal-title">Daftar Akun Pelanggan</h3>
                <button class="modal-close" onclick="closeModal('registerModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-6">Daftar untuk memesan kendaraan dengan mudah.</p>
                <form id="registerForm" onsubmit="handleRegister(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" placeholder="Nama lengkap Anda"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">NIK (16 digit)</label>
                            <input type="text" class="form-control" name="nik" placeholder="Nomor Induk Kependudukan"
                                maxlength="16" pattern="[0-9]{16}" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">No. Telepon</label>
                            <input type="tel" class="form-control" name="no_telepon" placeholder="08xxxxxxxxxx"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="email@contoh.com"
                                required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter"
                                minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2"
                            placeholder="Alamat lengkap (opsional)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <i class="ri-user-add-line"></i> Daftar
                    </button>
                </form>
            </div>
            <div class="modal-footer" style="justify-content:center; border-top:none; background:transparent;">
                <span class="text-muted">Sudah punya akun?</span>
                <a href="#" onclick="closeModal('registerModal'); openModal('loginModal'); return false;"
                    style="margin-left:4px">
                    Login di sini
                </a>
            </div>
        </div>
    </div>

    <!-- Vehicle Detail & Order Modal -->
    <div id="orderModal" class="modal-overlay">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 class="modal-title">Pesan Kendaraan</h3>
                <button class="modal-close" onclick="closeModal('orderModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Vehicle Info -->
                <div class="order-vehicle-info" id="orderVehicleInfo">
                    <!-- Dynamic content -->
                </div>

                <!-- Order Form -->
                <form id="orderForm" onsubmit="handleOrder(event)">
                    <input type="hidden" name="kendaraan_id" id="orderKendaraanId">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Tanggal Sewa</label>
                            <input type="date" class="form-control" name="tanggal_sewa" id="orderTglSewa" required
                                onchange="calculateTotal()">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Tanggal Kembali</label>
                            <input type="date" class="form-control" name="tanggal_kembali" id="orderTglKembali" required
                                onchange="calculateTotal()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2"
                            placeholder="Catatan tambahan untuk pemesanan"></textarea>
                    </div>

                    <!-- Price Preview -->
                    <div class="order-summary" id="orderSummary">
                        <div class="summary-row">
                            <span>Harga per hari</span>
                            <span id="summaryPrice">Rp 0</span>
                        </div>
                        <div class="summary-row">
                            <span>Durasi</span>
                            <span id="summaryDuration">0 hari</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total Biaya</span>
                            <span id="summaryTotal">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <i class="ri-shopping-cart-line"></i> Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="modal-overlay">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 class="modal-title"><i class="ri-file-list-3-line"></i> Invoice Pemesanan</h3>
                <button class="modal-close" onclick="closeModal('invoiceModal')">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body" id="invoiceContent">
                <!-- Dynamic invoice content -->
            </div>
            <div class="modal-footer" style="flex-direction: column; gap: 12px;">
                <div class="invoice-actions"
                    style="display: flex; gap: 10px; width: 100%; justify-content: center; flex-wrap: wrap;">
                    <button class="btn btn-secondary" onclick="closeModal('invoiceModal')">
                        <i class="ri-close-line"></i> Tutup
                    </button>
                    <button class="btn btn-primary" onclick="downloadInvoicePDF()" id="downloadPdfBtn">
                        <i class="ri-file-pdf-line"></i> Download PDF
                    </button>
                    <a href="javascript:void(0)" id="whatsappBtn" class="btn btn-success" target="_blank"
                        rel="noopener noreferrer" onclick="openWhatsApp()">
                        <i class="ri-whatsapp-line"></i> Hubungi WhatsApp
                    </a>
                </div>
                <div class="invoice-note-wa"
                    style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); padding: 12px 16px; border-radius: 8px; text-align: center; width: 100%;">
                    <p style="margin: 0; font-size: 13px; color: #166534;">
                        <i class="ri-information-line"></i>
                        <strong>Tips:</strong> Download PDF terlebih dahulu, lalu lampirkan saat chat WhatsApp untuk
                        konfirmasi pemesanan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Config
        const WHATSAPP_NUMBER = '6289656202076'; // Nomor WhatsApp pemilik rental

        // State
        let customer = null;
        let allVehicles = [];
        let selectedVehicle = null;

        // ==================== Toast ====================
        function showToast(message, type = 'success', title = '') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icons = {
                success: 'ri-check-line',
                error: 'ri-error-warning-line',
                warning: 'ri-alert-line',
                info: 'ri-information-line'
            };

            toast.innerHTML = `
                <i class="toast-icon ${icons[type]}"></i>
                <div class="toast-content">
                    ${title ? `<div class="toast-title">${title}</div>` : ''}
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="ri-close-line"></i>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ==================== Modal ====================
        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // ==================== Authentication ====================
        async function checkSession() {
            try {
                const res = await fetch('backend/auth.php?action=customer-check');
                const data = await res.json();
                if (data.success) {
                    customer = data.data;
                    updateUI();
                }
            } catch (e) {
                console.log('Not logged in');
            }
        }

        function updateUI() {
            const btnLogin = document.getElementById('btnLogin');
            const btnRegister = document.getElementById('btnRegister');
            const userMenu = document.getElementById('userMenu');
            const userName = document.getElementById('userName');

            if (customer) {
                btnLogin.classList.add('hidden');
                btnRegister.classList.add('hidden');
                userMenu.classList.remove('hidden');
                userName.textContent = customer.nama;
            } else {
                btnLogin.classList.remove('hidden');
                btnRegister.classList.remove('hidden');
                userMenu.classList.add('hidden');
            }
        }

        async function handleLogin(e) {
            e.preventDefault();
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;

            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';

            try {
                const res = await fetch('backend/auth.php?action=customer-login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();

                if (data.success) {
                    customer = data.data;
                    updateUI();
                    closeModal('loginModal');
                    form.reset();
                    showToast('Selamat datang, ' + customer.nama);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="ri-login-box-line"></i> Masuk';
        }

        async function handleRegister(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            if (data.password !== data.confirm_password) {
                showToast('Password tidak cocok', 'error');
                return;
            }

            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';

            try {
                const res = await fetch('backend/auth.php?action=customer-register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    closeModal('registerModal');
                    form.reset();
                    openModal('loginModal');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="ri-user-add-line"></i> Daftar';
        }

        async function logout() {
            await fetch('backend/auth.php?action=customer-logout');
            customer = null;
            updateUI();
            showToast('Anda telah keluar');
        }

        // ==================== Vehicles ====================
        async function loadVehicles() {
            try {
                const response = await fetch('backend/kendaraan.php?limit=100');
                const data = await response.json();
                if (data.success && data.data.items) {
                    allVehicles = data.data.items;
                    filterVehicles('all');
                    document.getElementById('statVehicles').textContent = data.data.total;
                }
            } catch (error) {
                console.error('Error loading vehicles:', error);
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('backend/laporan.php?action=dashboard');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('statVehicles').textContent = data.data.total_stok_kendaraan || 0;
                    document.getElementById('statCustomers').textContent = data.data.total_pelanggan || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function filterVehicles(filter) {
            const grid = document.getElementById('vehicleGrid');
            let filtered = allVehicles;

            if (filter !== 'all') {
                filtered = allVehicles.filter(v => v.jenis === filter);
            }

            filtered = filtered.filter(v => v.stok_tersedia > 0);

            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div class="empty-message">
                        <i class="ri-car-line"></i>
                        <p>Tidak ada kendaraan tersedia untuk kategori ini</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = filtered.map(v => `
                <div class="vehicle-card-landing" data-type="${v.jenis}" onclick="openOrderModal(${v.id})">
                    <div class="vehicle-image-wrapper">
                        <img src="${v.gambar_url || 'https://via.placeholder.com/400x250?text=No+Image'}" 
                             alt="${v.nama}" class="vehicle-img"
                             onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'">
                        <span class="vehicle-type-badge">${v.jenis}</span>
                        <span class="vehicle-available">Tersedia</span>
                    </div>
                    <div class="vehicle-info-landing">
                        <h3 class="vehicle-name">${v.nama}</h3>
                        <p class="vehicle-meta">${v.merk} • ${v.tahun} • ${v.warna || '-'}</p>
                        <p class="vehicle-plate"><i class="ri-car-line"></i> ${v.plat_nomor}</p>
                        <div class="vehicle-footer-landing">
                            <div class="vehicle-price-landing">
                                <span class="price-amount">${formatCurrency(v.harga_sewa_per_hari)}</span>
                                <span class="price-period">/ hari</span>
                            </div>
                            <button class="btn btn-primary btn-sm">
                                <i class="ri-shopping-cart-line"></i> Pesan
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterVehicles(this.dataset.filter);
            });
        });

        // ==================== Order ====================
        function openOrderModal(vehicleId) {
            if (!customer) {
                showToast('Silakan login terlebih dahulu', 'warning');
                openModal('loginModal');
                return;
            }

            selectedVehicle = allVehicles.find(v => v.id == vehicleId);
            if (!selectedVehicle) return;

            // Set vehicle info
            document.getElementById('orderVehicleInfo').innerHTML = `
                <div class="order-vehicle-card">
                    <img src="${selectedVehicle.gambar_url || 'https://via.placeholder.com/200x120?text=No+Image'}" 
                         alt="${selectedVehicle.nama}" class="order-vehicle-img">
                    <div class="order-vehicle-details">
                        <h4>${selectedVehicle.nama}</h4>
                        <p>${selectedVehicle.merk} • ${selectedVehicle.tahun} • ${selectedVehicle.warna || '-'}</p>
                        <p><i class="ri-car-line"></i> ${selectedVehicle.plat_nomor}</p>
                        <p class="order-vehicle-price">${formatCurrency(selectedVehicle.harga_sewa_per_hari)} / hari</p>
                    </div>
                </div>
            `;

            document.getElementById('orderKendaraanId').value = vehicleId;

            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('orderTglSewa').min = today;
            document.getElementById('orderTglKembali').min = today;

            calculateTotal();
            openModal('orderModal');
        }

        function calculateTotal() {
            if (!selectedVehicle) return;

            const tglSewa = document.getElementById('orderTglSewa').value;
            const tglKembali = document.getElementById('orderTglKembali').value;

            if (tglSewa && tglKembali) {
                const start = new Date(tglSewa);
                const end = new Date(tglKembali);
                let days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                if (days < 1) days = 1;

                const total = selectedVehicle.harga_sewa_per_hari * days;

                document.getElementById('summaryPrice').textContent = formatCurrency(selectedVehicle.harga_sewa_per_hari);
                document.getElementById('summaryDuration').textContent = days + ' hari';
                document.getElementById('summaryTotal').textContent = formatCurrency(total);
            }
        }

        async function handleOrder(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';

            try {
                const res = await fetch('backend/pemesanan.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    closeModal('orderModal');
                    form.reset();
                    showInvoice(result.data);
                } else {
                    showToast(result.message, 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="ri-shopping-cart-line"></i> Pesan Sekarang';
        }

        function showInvoice(invoice) {
            const content = document.getElementById('invoiceContent');
            content.innerHTML = `
                <div class="invoice">
                    <div class="invoice-header">
                        <div class="invoice-brand">
                            <div class="nav-logo"><i class="ri-car-line"></i></div>
                            <span class="nav-brand-text">VHRent</span>
                        </div>
                        <div class="invoice-code">
                            <span class="label">Kode Pemesanan</span>
                            <span class="code">${invoice.kode_pemesanan}</span>
                        </div>
                    </div>
                    
                    <div class="invoice-section">
                        <h4><i class="ri-user-line"></i> Data Pemesan</h4>
                        <div class="invoice-grid">
                            <div><span>Nama</span><strong>${invoice.pelanggan.nama}</strong></div>
                            <div><span>No. Telepon</span><strong>${invoice.pelanggan.no_telepon}</strong></div>
                            <div><span>Email</span><strong>${invoice.pelanggan.email}</strong></div>
                        </div>
                    </div>
                    
                    <div class="invoice-section">
                        <h4><i class="ri-car-line"></i> Data Kendaraan</h4>
                        <div class="invoice-vehicle">
                            <img src="${invoice.kendaraan.gambar_url || 'https://via.placeholder.com/150x100?text=No+Image'}" alt="${invoice.kendaraan.nama}">
                            <div>
                                <strong>${invoice.kendaraan.nama}</strong>
                                <p>${invoice.kendaraan.merk} • ${invoice.kendaraan.jenis}</p>
                                <p>Plat: ${invoice.kendaraan.plat_nomor}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="invoice-section">
                        <h4><i class="ri-calendar-line"></i> Detail Sewa</h4>
                        <div class="invoice-grid">
                            <div><span>Tanggal Sewa</span><strong>${formatDate(invoice.tanggal_sewa)}</strong></div>
                            <div><span>Tanggal Kembali</span><strong>${formatDate(invoice.tanggal_kembali)}</strong></div>
                            <div><span>Durasi</span><strong>${invoice.lama_hari} hari</strong></div>
                        </div>
                    </div>
                    
                    <div class="invoice-total">
                        <div class="invoice-row">
                            <span>Harga per hari</span>
                            <span>${formatCurrency(invoice.harga_per_hari)}</span>
                        </div>
                        <div class="invoice-row">
                            <span>Durasi</span>
                            <span>${invoice.lama_hari} hari</span>
                        </div>
                        <div class="invoice-row total">
                            <span>Total Biaya</span>
                            <span>${formatCurrency(invoice.total_biaya)}</span>
                        </div>
                    </div>
                    
                    <div class="invoice-note" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                        <i class="ri-time-line" style="color: #d97706;"></i>
                        <div>
                            <p style="margin: 0; font-weight: 600; color: #92400e;">Status: Menunggu Konfirmasi Admin</p>
                            <p style="margin: 4px 0 0 0; color: #b45309;">Pemesanan Anda akan diproses oleh admin. Hubungi via WhatsApp untuk mempercepat konfirmasi.</p>
                        </div>
                    </div>
                    </div>
                </div>
            `;

            // Store WhatsApp URL globally for the button
            window.currentWhatsAppUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(invoice.whatsapp_message || '')}`;
            document.getElementById('whatsappBtn').href = window.currentWhatsAppUrl;

            // Store invoice data globally for PDF generation
            window.currentInvoice = invoice;

            openModal('invoiceModal');
        }

        // Function to open WhatsApp in new tab
        function openWhatsApp() {
            if (window.currentWhatsAppUrl) {
                window.open(window.currentWhatsAppUrl, '_blank');
            } else {
                window.open(`https://wa.me/${WHATSAPP_NUMBER}`, '_blank');
            }
        }

        // Function to download invoice as PDF
        async function downloadInvoicePDF() {
            const btn = document.getElementById('downloadPdfBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Generating...';

            try {
                const invoiceElement = document.querySelector('#invoiceContent .invoice');

                if (!invoiceElement) {
                    showToast('Invoice tidak ditemukan', 'error');
                    return;
                }

                // Temporarily adjust styles for better PDF output
                invoiceElement.style.padding = '20px';
                invoiceElement.style.backgroundColor = '#ffffff';

                // Use html2canvas to capture the invoice
                const canvas = await html2canvas(invoiceElement, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                // Reset styles
                invoiceElement.style.padding = '';
                invoiceElement.style.backgroundColor = '';

                // Create PDF using jsPDF
                const { jsPDF } = window.jspdf;
                const imgWidth = 210; // A4 width in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                const pdf = new jsPDF({
                    orientation: imgHeight > 297 ? 'portrait' : 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 0, 10, imgWidth, imgHeight);

                // Generate filename using invoice code
                const invoiceCode = window.currentInvoice?.kode_pemesanan || 'Invoice';
                const filename = `Invoice_${invoiceCode}.pdf`;

                // Download the PDF
                pdf.save(filename);

                showToast('PDF berhasil didownload! Silakan lampirkan saat chat WhatsApp.', 'success');

            } catch (error) {
                console.error('Error generating PDF:', error);
                showToast('Gagal generate PDF. Silakan coba lagi.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        // ==================== Utilities ====================
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        function formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // ==================== Navigation ====================
        document.getElementById('mobileToggle').addEventListener('click', function () {
            document.getElementById('navLinks').classList.toggle('active');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('navLinks').classList.remove('active');
                }
            });
        });

        window.addEventListener('scroll', function () {
            const nav = document.querySelector('.landing-nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // ==================== Initialize ====================
        document.addEventListener('DOMContentLoaded', function () {
            checkSession();
            loadVehicles();
            loadStats();
        });
    </script>
</body>

</html>