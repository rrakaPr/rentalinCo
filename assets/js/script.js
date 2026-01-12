/**
 * Vehicle Rental Management System
 * Main JavaScript Application
 */

// ==================== Configuration ====================
const API_BASE = 'backend';
const APP_CONFIG = {
    developerName: 'M. Rizky Raka Pratama',
    developerNIM: '312210397',
    appName: 'rentalinCo',
    version: '1.0.0'
};

// ==================== State Management ====================
const state = {
    currentPage: 'dashboard',
    user: null,
    isLoggedIn: false,
    kendaraan: { items: [], total: 0, page: 1 },
    pelanggan: { items: [], total: 0, page: 1 },
    transaksi: { items: [], total: 0, page: 1 },
    denda: { items: [], total: 0, page: 1 },
    dashboard: {}
};

// ==================== API Helper ====================
async function api(endpoint, options = {}) {
    const url = `${API_BASE}/${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        ...options
    };

    if (options.body && typeof options.body === 'object') {
        config.body = JSON.stringify(options.body);
    }

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok && response.status === 401) {
            logout();
            return { success: false, message: 'Sesi berakhir, silakan login kembali' };
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Terjadi kesalahan koneksi' };
    }
}

// ==================== Toast Notification ====================
function showToast(message, type = 'success', title = '') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'ri-check-line',
        error: 'ri-error-warning-line',
        warning: 'ri-alert-line',
        info: 'ri-information-line'
    };

    const titles = {
        success: 'Berhasil',
        error: 'Error',
        warning: 'Peringatan',
        info: 'Info'
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="toast-icon ${icons[type]}"></i>
        <div class="toast-content">
            <div class="toast-title">${title || titles[type]}</div>
            <p class="toast-message">${message}</p>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="ri-close-line"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// ==================== Modal Functions ====================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function closeAllModals() {
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.classList.remove('active');
    });
    document.body.style.overflow = '';
}

// ==================== Authentication ====================
async function checkAuth() {
    const result = await api('auth.php?action=check');
    if (result.success) {
        state.user = result.data;
        state.isLoggedIn = true;
        showApp();
    } else {
        showLogin();
    }
}

async function login(event) {
    event.preventDefault();
    const form = event.target;
    const username = form.username.value;
    const password = form.password.value;

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';

    const result = await api('auth.php?action=login', {
        method: 'POST',
        body: { username, password }
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="ri-login-box-line"></i> Masuk';

    if (result.success) {
        state.user = result.data;
        state.isLoggedIn = true;
        showToast('Selamat datang, ' + result.data.nama_lengkap);
        showApp();
    } else {
        showToast(result.message, 'error');
    }
}

async function logout() {
    await api('auth.php?action=logout');
    state.user = null;
    state.isLoggedIn = false;
    showLogin();
    showToast('Anda telah keluar');
}

async function forgotPassword(event) {
    event.preventDefault();
    const form = event.target;
    const username = form.username.value;

    const result = await api('auth.php?action=forgot-password', {
        method: 'POST',
        body: { username }
    });

    if (result.success) {
        showToast(result.data.hint, 'info', 'Hint Password');
        document.getElementById('forgotStep1').classList.add('hidden');
        document.getElementById('forgotStep2').classList.remove('hidden');
        document.getElementById('resetUsername').value = username;
    } else {
        showToast(result.message, 'error');
    }
}

async function resetPassword(event) {
    event.preventDefault();
    const form = event.target;
    const username = document.getElementById('resetUsername').value;
    const nim = form.nim.value;
    const new_password = form.new_password.value;
    const confirm_password = form.confirm_password.value;

    if (new_password !== confirm_password) {
        showToast('Password tidak cocok', 'error');
        return;
    }

    const result = await api('auth.php?action=reset-password', {
        method: 'POST',
        body: { username, nim, new_password }
    });

    if (result.success) {
        showToast('Password berhasil direset, silakan login');
        closeModal('forgotModal');
        resetForgotForm();
    } else {
        showToast(result.message, 'error');
    }
}

function resetForgotForm() {
    document.getElementById('forgotForm').reset();
    document.getElementById('resetForm').reset();
    document.getElementById('forgotStep1').classList.remove('hidden');
    document.getElementById('forgotStep2').classList.add('hidden');
}

// ==================== Register ====================
async function register(event) {
    event.preventDefault();
    const form = event.target;

    const nama_lengkap = form.nama_lengkap.value.trim();
    const username = form.username.value.trim();
    const nim = form.nim.value.trim();
    const email = form.email.value.trim();
    const password = form.password.value;
    const confirm_password = form.confirm_password.value;

    // Validate password match
    if (password !== confirm_password) {
        showToast('Password tidak cocok', 'error');
        return;
    }

    // Validate NIM format
    if (nim.length !== 9 || !/^\d{9}$/.test(nim)) {
        showToast('NIM harus 9 angka', 'error');
        return;
    }

    // Validate username format
    if (!/^[a-zA-Z0-9_]{3,30}$/.test(username)) {
        showToast('Username harus 3-30 karakter (huruf, angka, underscore)', 'error');
        return;
    }

    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';

    const result = await api('auth.php?action=register', {
        method: 'POST',
        body: { nama_lengkap, username, nim, email, password }
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="ri-user-add-line"></i> Daftar';

    if (result.success) {
        showToast(result.message, 'success');
        closeModal('registerModal');
        form.reset();
        // Focus on login form
        document.getElementById('loginUsername').value = username;
        document.getElementById('loginPassword').focus();
    } else {
        showToast(result.message, 'error');
    }
}

// ==================== Navigation ====================
function showLogin() {
    document.getElementById('loginPage').classList.remove('hidden');
    document.getElementById('appLayout').classList.add('hidden');
}

function showApp() {
    document.getElementById('loginPage').classList.add('hidden');
    document.getElementById('appLayout').classList.remove('hidden');
    updateUserInfo();
    navigateTo('dashboard');
}

function navigateTo(page) {
    state.currentPage = page;

    // Update sidebar active state
    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.dataset.page === page) {
            item.classList.add('active');
        }
    });

    // Update header title
    const titles = {
        dashboard: 'Dashboard',
        kendaraan: 'Master Kendaraan',
        pelanggan: 'Master Pelanggan',
        transaksi: 'Transaksi Penyewaan',
        pengembalian: 'Pengembalian',
        denda: 'Pengelolaan Denda',
        laporan: 'Laporan'
    };
    document.getElementById('headerTitle').textContent = titles[page] || 'Dashboard';

    // Hide all pages and show selected
    document.querySelectorAll('.page').forEach(p => p.classList.add('hidden'));
    const pageEl = document.getElementById(`page-${page}`);
    if (pageEl) {
        pageEl.classList.remove('hidden');
        loadPageData(page);
    }

    // Close mobile sidebar
    document.querySelector('.sidebar').classList.remove('active');
    document.querySelector('.sidebar-overlay').classList.remove('active');
}

function loadPageData(page) {
    switch (page) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'kendaraan':
            loadKendaraan();
            break;
        case 'pelanggan':
            loadPelanggan();
            break;
        case 'transaksi':
            loadTransaksiAktif();
            break;
        case 'pengembalian':
            loadTransaksiAktif();
            break;
        case 'denda':
            loadDenda();
            break;
        case 'laporan':
            loadLaporan();
            break;
    }
}

function updateUserInfo() {
    if (state.user) {
        document.getElementById('userName').textContent = state.user.nama_lengkap;
        document.getElementById('userAvatar').textContent = state.user.nama_lengkap.charAt(0).toUpperCase();
    }
}

// ==================== Dashboard ====================
async function loadDashboard() {
    const result = await api('laporan.php?action=dashboard');
    if (result.success) {
        state.dashboard = result.data;
        renderDashboard();
    }
}

function renderDashboard() {
    const data = state.dashboard;

    // Stats
    document.getElementById('statKendaraan').textContent = data.total_stok_kendaraan || 0;
    document.getElementById('statPelanggan').textContent = data.total_pelanggan || 0;
    document.getElementById('statTransaksi').textContent = data.transaksi_aktif || 0;
    document.getElementById('statPendapatan').textContent = formatCurrency(data.pendapatan_bulan_ini || 0);

    // Late vehicles warning
    const lateCount = data.kendaraan_terlambat || 0;
    const lateWarning = document.getElementById('lateWarning');
    if (lateCount > 0) {
        lateWarning.classList.remove('hidden');
        lateWarning.querySelector('.alert-message').textContent =
            `Ada ${lateCount} kendaraan yang melewati tanggal pengembalian!`;
    } else {
        lateWarning.classList.add('hidden');
    }

    // Unpaid fines
    const unpaidFines = data.denda_belum_dibayar || 0;
    const fineWarning = document.getElementById('fineWarning');
    if (unpaidFines > 0) {
        fineWarning.classList.remove('hidden');
        fineWarning.querySelector('.alert-message').textContent =
            `Ada ${unpaidFines} denda belum dibayar (${formatCurrency(data.total_denda_belum_dibayar || 0)})`;
    } else {
        fineWarning.classList.add('hidden');
    }

    // Recent transactions
    const tbody = document.getElementById('recentTransactions');
    if (data.recent_transactions && data.recent_transactions.length > 0) {
        tbody.innerHTML = data.recent_transactions.map(t => `
            <tr>
                <td><span class="font-medium">${t.kode_transaksi}</span></td>
                <td>${t.pelanggan_nama}</td>
                <td>${t.kendaraan_nama}</td>
                <td>${formatDate(t.tanggal_sewa)}</td>
                <td>${formatCurrency(t.total_biaya)}</td>
                <td>${renderBadge(t.status)}</td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada transaksi</td></tr>';
    }
}

// ==================== Kendaraan (Vehicle) ====================
async function loadKendaraan(page = 1) {
    const search = document.getElementById('searchKendaraan')?.value || '';
    const jenis = document.getElementById('filterJenis')?.value || '';

    const result = await api(`kendaraan.php?page=${page}&limit=12&search=${search}&jenis=${jenis}`);
    if (result.success) {
        state.kendaraan = result.data;
        renderKendaraan();
    }
}

function renderKendaraan() {
    const container = document.getElementById('kendaraanGrid');
    const { items, total, page, total_pages } = state.kendaraan;

    if (items.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1">
                <div class="empty-state-icon"><i class="ri-car-line"></i></div>
                <h3 class="empty-state-title">Belum ada kendaraan</h3>
                <p class="empty-state-text">Mulai dengan menambahkan kendaraan pertama</p>
                <button class="btn btn-primary" onclick="openKendaraanForm()">
                    <i class="ri-add-line"></i> Tambah Kendaraan
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = items.map(k => `
        <div class="vehicle-card">
            <img src="${k.gambar_url || 'https://via.placeholder.com/400x200?text=No+Image'}" 
                 alt="${k.nama}" class="vehicle-image" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
            <div class="vehicle-content">
                <div class="vehicle-header">
                    <div>
                        <h4 class="vehicle-title">${k.nama}</h4>
                        <div class="vehicle-type">${k.merk} • ${k.tahun}</div>
                    </div>
                    ${renderBadge(k.jenis)}
                </div>
                <div class="vehicle-info">
                    <div class="vehicle-info-item">
                        <i class="ri-palette-line"></i> ${k.warna || '-'}
                    </div>
                    <div class="vehicle-info-item">
                        <i class="ri-car-line"></i> ${k.plat_nomor}
                    </div>
                </div>
                <div class="vehicle-price">
                    ${formatCurrency(k.harga_sewa_per_hari)} <span>/ hari</span>
                </div>
                <div class="vehicle-stock">
                    <i class="ri-stack-line"></i>
                    Stok tersedia: <strong>${k.stok_tersedia}</strong> / ${k.stok}
                </div>
            </div>
            <div class="vehicle-footer">
                <button class="btn btn-secondary btn-sm" onclick="editKendaraan(${k.id})">
                    <i class="ri-edit-line"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm" onclick="confirmDeleteKendaraan(${k.id}, '${k.nama}')">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    `).join('');

    // Render pagination
    renderPagination('kendaraanPagination', page, total_pages, (p) => loadKendaraan(p));
}

function openKendaraanForm(id = null) {
    const modal = document.getElementById('kendaraanModal');
    const form = document.getElementById('kendaraanForm');
    const title = document.getElementById('kendaraanModalTitle');

    form.reset();
    form.dataset.id = id || '';
    title.textContent = id ? 'Edit Kendaraan' : 'Tambah Kendaraan';

    if (id) {
        loadKendaraanData(id);
    }

    openModal('kendaraanModal');
}

async function loadKendaraanData(id) {
    const result = await api(`kendaraan.php?id=${id}`);
    if (result.success) {
        const k = result.data;
        const form = document.getElementById('kendaraanForm');
        form.nama.value = k.nama;
        form.jenis.value = k.jenis;
        form.merk.value = k.merk;
        form.tahun.value = k.tahun;
        form.plat_nomor.value = k.plat_nomor;
        form.warna.value = k.warna || '';
        form.harga_sewa_per_hari.value = k.harga_sewa_per_hari;
        form.stok.value = k.stok;
        form.gambar_url.value = k.gambar_url || '';
        form.deskripsi.value = k.deskripsi || '';
        form.status.value = k.status;
    }
}

async function saveKendaraan(event) {
    event.preventDefault();
    const form = event.target;
    const id = form.dataset.id;

    const data = {
        nama: form.nama.value,
        jenis: form.jenis.value,
        merk: form.merk.value,
        tahun: parseInt(form.tahun.value),
        plat_nomor: form.plat_nomor.value,
        warna: form.warna.value,
        harga_sewa_per_hari: parseFloat(form.harga_sewa_per_hari.value),
        stok: parseInt(form.stok.value),
        gambar_url: form.gambar_url.value,
        deskripsi: form.deskripsi.value,
        status: form.status.value
    };

    const result = await api(`kendaraan.php${id ? `?id=${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        body: data
    });

    if (result.success) {
        showToast(result.message);
        closeModal('kendaraanModal');
        loadKendaraan();
    } else {
        showToast(result.message, 'error');
    }
}

function editKendaraan(id) {
    openKendaraanForm(id);
}

function confirmDeleteKendaraan(id, nama) {
    if (confirm(`Hapus kendaraan "${nama}"?`)) {
        deleteKendaraan(id);
    }
}

async function deleteKendaraan(id) {
    const result = await api(`kendaraan.php?id=${id}`, { method: 'DELETE' });
    if (result.success) {
        showToast(result.message);
        loadKendaraan();
    } else {
        showToast(result.message, 'error');
    }
}

// ==================== Pelanggan (Customer) ====================
async function loadPelanggan(page = 1) {
    const search = document.getElementById('searchPelanggan')?.value || '';

    const result = await api(`pelanggan.php?page=${page}&limit=10&search=${search}`);
    if (result.success) {
        state.pelanggan = result.data;
        renderPelanggan();
    }
}

function renderPelanggan() {
    const tbody = document.getElementById('pelangganTable');
    const { items, total, page, total_pages } = state.pelanggan;

    if (items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="ri-user-line"></i></div>
                        <h3 class="empty-state-title">Belum ada pelanggan</h3>
                        <p class="empty-state-text">Mulai dengan menambahkan pelanggan pertama</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = items.map(p => {
        const escapedNama = p.nama.replace(/'/g, "\\'").replace(/"/g, '\\"');
        return `
        <tr>
            <td>
                <div class="font-medium">${p.nama}</div>
                <small class="text-muted">${p.nik}</small>
            </td>
            <td>${p.jenis_kelamin}</td>
            <td>${p.no_telepon}</td>
            <td>${p.email || '-'}</td>
            <td>${p.no_sim || '-'}</td>
            <td><small class="text-muted">${p.alamat ? (p.alamat.length > 50 ? p.alamat.substring(0, 50) + '...' : p.alamat) : '-'}</small></td>
            <td>
                <div class="table-actions">
                    <button type="button" class="btn btn-ghost btn-icon btn-sm" onclick="editPelanggan(${p.id})" title="Edit">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button type="button" class="btn btn-ghost btn-icon btn-sm text-danger" onclick="hapusPelanggan(${p.id}, '${escapedNama}')" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    }).join('');

    renderPagination('pelangganPagination', page, total_pages, (p) => loadPelanggan(p));
}

function openPelangganForm(id = null) {
    const modal = document.getElementById('pelangganModal');
    const form = document.getElementById('pelangganForm');
    const title = document.getElementById('pelangganModalTitle');

    form.reset();
    form.dataset.id = id || '';
    title.textContent = id ? 'Edit Pelanggan' : 'Tambah Pelanggan';

    if (id) {
        loadPelangganData(id);
    }

    openModal('pelangganModal');
}

async function loadPelangganData(id) {
    const result = await api(`pelanggan.php?id=${id}`);
    if (result.success) {
        const p = result.data;
        const form = document.getElementById('pelangganForm');
        form.nama.value = p.nama;
        form.nik.value = p.nik;
        form.jenis_kelamin.value = p.jenis_kelamin;
        form.tanggal_lahir.value = p.tanggal_lahir || '';
        form.no_telepon.value = p.no_telepon;
        form.email.value = p.email || '';
        form.no_sim.value = p.no_sim || '';
        form.alamat.value = p.alamat || '';
    }
}

async function savePelanggan(event) {
    event.preventDefault();
    const form = event.target;
    const id = form.dataset.id;

    const data = {
        nama: form.nama.value,
        nik: form.nik.value,
        jenis_kelamin: form.jenis_kelamin.value,
        tanggal_lahir: form.tanggal_lahir.value,
        no_telepon: form.no_telepon.value,
        email: form.email.value,
        no_sim: form.no_sim.value,
        alamat: form.alamat.value
    };

    const result = await api(`pelanggan.php${id ? `?id=${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        body: data
    });

    if (result.success) {
        showToast(result.message);
        closeModal('pelangganModal');
        loadPelanggan();
    } else {
        showToast(result.message, 'error');
    }
}

function editPelanggan(id) {
    openPelangganForm(id);
}

function confirmDeletePelanggan(id, nama) {
    if (confirm(`Hapus pelanggan "${nama}"?`)) {
        deletePelanggan(id);
    }
}

// New simple delete function
function hapusPelanggan(id, nama) {
    console.log('hapusPelanggan called:', id, nama);
    if (confirm('Hapus pelanggan "' + nama + '"?')) {
        fetch('backend/pelanggan.php?id=' + id, {
            method: 'DELETE',
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                console.log('Delete result:', data);
                if (data.success) {
                    showToast(data.message || 'Pelanggan berhasil dihapus');
                    loadPelanggan();
                } else {
                    showToast(data.message || 'Gagal menghapus pelanggan', 'error');
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                showToast('Terjadi kesalahan', 'error');
            });
    }
}

async function deletePelanggan(id) {
    const result = await api(`pelanggan.php?id=${id}`, { method: 'DELETE' });
    if (result.success) {
        showToast(result.message);
        loadPelanggan();
    } else {
        showToast(result.message, 'error');
    }
}

// ==================== Transaksi (Transaction) ====================
async function loadTransaksiAktif() {
    const result = await api('transaksi.php?action=aktif');
    if (result.success) {
        renderTransaksiAktif(result.data);
    }
}

function renderTransaksiAktif(items) {
    const tbody = document.getElementById('transaksiTable');

    if (!items || items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="ri-file-list-3-line"></i></div>
                        <h3 class="empty-state-title">Tidak ada transaksi aktif</h3>
                        <p class="empty-state-text">Semua kendaraan sudah dikembalikan</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = items.map(t => {
        // Determine action buttons based on status
        let actionButtons = '';
        if (t.status === 'Menunggu') {
            actionButtons = `
                <button type="button" class="btn btn-success btn-icon btn-sm" onclick="approveTransaksi(${t.id})" title="Setujui">
                    <i class="ri-check-line"></i>
                </button>
                <button type="button" class="btn btn-danger btn-icon btn-sm" onclick="rejectTransaksi(${t.id})" title="Tolak">
                    <i class="ri-close-line"></i>
                </button>
            `;
        } else if (t.status === 'Disewa') {
            actionButtons = `
                <button type="button" class="btn btn-primary btn-sm" onclick="openKembalikanModal(${t.id}, '${t.kode_transaksi}')">
                    <i class="ri-arrow-go-back-line"></i> Kembalikan
                </button>
            `;
        }

        // Determine status badge
        let statusBadge = '';
        if (t.status === 'Menunggu') {
            statusBadge = `<span class="badge badge-warning">Menunggu</span>`;
        } else if (t.is_terlambat) {
            statusBadge = `<span class="badge badge-danger">Terlambat ${Math.abs(t.sisa_hari)} hari</span>`;
        } else {
            statusBadge = `<span class="badge badge-success">Sisa ${t.sisa_hari} hari</span>`;
        }

        return `
        <tr class="${t.is_terlambat ? 'bg-danger-light' : ''} ${t.status === 'Menunggu' ? 'bg-warning-light' : ''}">
            <td>
                <span class="font-medium">${t.kode_transaksi}</span>
            </td>
            <td>
                <div class="font-medium">${t.pelanggan_nama}</div>
                <small class="text-muted">${t.pelanggan_telepon || ''}</small>
            </td>
            <td>
                <div class="flex items-center gap-2">
                    <img src="${t.gambar_url || 'https://via.placeholder.com/40'}" 
                         alt="${t.kendaraan_nama}" 
                         style="width:40px;height:40px;border-radius:8px;object-fit:cover"
                         onerror="this.src='https://via.placeholder.com/40'">
                    <div>
                        <div class="font-medium">${t.kendaraan_nama}</div>
                        <small class="text-muted">${t.plat_nomor}</small>
                    </div>
                </div>
            </td>
            <td>${formatDate(t.tanggal_sewa)}</td>
            <td>${formatDate(t.tanggal_kembali_rencana)}</td>
            <td>${statusBadge}</td>
            <td>${formatCurrency(t.total_biaya)}</td>
            <td>
                <div class="table-actions">
                    ${actionButtons}
                </div>
            </td>
        </tr>
        `;
    }).join('');
}

// Approve transaction
function approveTransaksi(id) {
    console.log('approveTransaksi called:', id);
    if (!confirm('Setujui pemesanan ini? Stok kendaraan akan dikurangi.')) return;

    fetch('backend/transaksi.php?action=approve', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ transaksi_id: id })
    })
        .then(r => r.json())
        .then(data => {
            console.log('Approve result:', data);
            if (data.success) {
                showToast(data.message || 'Pemesanan disetujui', 'success');
                loadTransaksiAktif();
            } else {
                showToast(data.message || 'Gagal menyetujui', 'error');
            }
        })
        .catch(err => {
            console.error('Approve error:', err);
            showToast('Terjadi kesalahan', 'error');
        });
}

// Reject transaction
function rejectTransaksi(id) {
    console.log('rejectTransaksi called:', id);
    const alasan = prompt('Alasan penolakan (opsional):');
    if (alasan === null) return;

    fetch('backend/transaksi.php?action=reject', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ transaksi_id: id, alasan: alasan || 'Ditolak oleh admin' })
    })
        .then(r => r.json())
        .then(data => {
            console.log('Reject result:', data);
            if (data.success) {
                showToast(data.message || 'Pemesanan ditolak', 'warning');
                loadTransaksiAktif();
            } else {
                showToast(data.message || 'Gagal menolak', 'error');
            }
        })
        .catch(err => {
            console.error('Reject error:', err);
            showToast('Terjadi kesalahan', 'error');
        });
}

async function openSewaForm() {
    // Load pelanggan and kendaraan options
    const [pelangganRes, kendaraanRes] = await Promise.all([
        api('pelanggan.php?limit=100'),
        api('kendaraan.php?limit=100&status=Tersedia')
    ]);

    if (pelangganRes.success) {
        const select = document.getElementById('sewaPelanggan');
        select.innerHTML = '<option value="">Pilih Pelanggan</option>' +
            pelangganRes.data.items.map(p => `<option value="${p.id}">${p.nama} (${p.nik})</option>`).join('');
    }

    if (kendaraanRes.success) {
        const select = document.getElementById('sewaKendaraan');
        select.innerHTML = '<option value="">Pilih Kendaraan</option>' +
            kendaraanRes.data.items
                .filter(k => k.stok_tersedia > 0)
                .map(k => `<option value="${k.id}" data-harga="${k.harga_sewa_per_hari}">${k.nama} (${k.plat_nomor}) - Stok: ${k.stok_tersedia}</option>`)
                .join('');
    }

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('sewaTanggalSewa').value = today;
    document.getElementById('sewaTanggalKembali').value = '';

    document.getElementById('sewaForm').reset();
    document.getElementById('sewaTanggalSewa').value = today;
    document.getElementById('sewaPreview').classList.add('hidden');

    openModal('sewaModal');
}

function updateSewaPreview() {
    const kendaraanSelect = document.getElementById('sewaKendaraan');
    const tanggalSewa = document.getElementById('sewaTanggalSewa').value;
    const tanggalKembali = document.getElementById('sewaTanggalKembali').value;
    const preview = document.getElementById('sewaPreview');

    if (kendaraanSelect.value && tanggalSewa && tanggalKembali) {
        const harga = parseFloat(kendaraanSelect.options[kendaraanSelect.selectedIndex].dataset.harga);
        const start = new Date(tanggalSewa);
        const end = new Date(tanggalKembali);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

        if (days > 0) {
            const total = harga * days;
            document.getElementById('previewHari').textContent = days + ' hari';
            document.getElementById('previewHarga').textContent = formatCurrency(harga) + ' / hari';
            document.getElementById('previewTotal').textContent = formatCurrency(total);
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    } else {
        preview.classList.add('hidden');
    }
}

async function submitSewa(event) {
    event.preventDefault();
    const form = event.target;

    const data = {
        pelanggan_id: parseInt(form.pelanggan_id.value),
        kendaraan_id: parseInt(form.kendaraan_id.value),
        tanggal_sewa: form.tanggal_sewa.value,
        tanggal_kembali_rencana: form.tanggal_kembali.value,
        catatan: form.catatan.value
    };

    const result = await api('transaksi.php?action=sewa', {
        method: 'POST',
        body: data
    });

    if (result.success) {
        showToast(result.message);
        closeModal('sewaModal');
        loadTransaksiAktif();
        loadDashboard();
    } else {
        showToast(result.message, 'error');
    }
}

function openKembalikanModal(id, kode) {
    document.getElementById('kembalikanTransaksiId').value = id;
    document.getElementById('kembalikanKode').textContent = kode;
    document.getElementById('kembalikanTanggal').value = new Date().toISOString().split('T')[0];
    document.getElementById('kembalikanResult').classList.add('hidden');
    openModal('kembalikanModal');
}

async function submitKembalikan(event) {
    event.preventDefault();
    const transaksiId = document.getElementById('kembalikanTransaksiId').value;
    const tanggalKembali = document.getElementById('kembalikanTanggal').value;

    const result = await api('transaksi.php?action=kembali', {
        method: 'POST',
        body: {
            transaksi_id: parseInt(transaksiId),
            tanggal_kembali: tanggalKembali
        }
    });

    if (result.success) {
        const resultDiv = document.getElementById('kembalikanResult');
        resultDiv.classList.remove('hidden');

        if (result.data.hari_terlambat > 0) {
            resultDiv.innerHTML = `
                <div class="alert alert-warning">
                    <i class="alert-icon ri-alert-line"></i>
                    <div class="alert-content">
                        <div class="alert-title">Terlambat ${result.data.hari_terlambat} Hari</div>
                        <p class="alert-message">Denda: ${formatCurrency(result.data.total_denda)}</p>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="alert-icon ri-check-line"></i>
                    <div class="alert-content">
                        <div class="alert-title">Tepat Waktu</div>
                        <p class="alert-message">Tidak ada denda</p>
                    </div>
                </div>
            `;
        }

        showToast(result.message);
        loadTransaksiAktif();
        loadDashboard();

        setTimeout(() => {
            closeModal('kembalikanModal');
        }, 2000);
    } else {
        showToast(result.message, 'error');
    }
}

// ==================== Denda (Penalty) ====================
async function loadDenda(page = 1) {
    const status = document.getElementById('filterDendaStatus')?.value || '';

    const result = await api(`denda.php?page=${page}&limit=10&status=${status}`);
    if (result.success) {
        state.denda = result.data;
        renderDenda();
    }
}

function renderDenda() {
    const tbody = document.getElementById('dendaTable');
    const { items, total, page, total_pages } = state.denda;

    if (items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="ri-money-dollar-circle-line"></i></div>
                        <h3 class="empty-state-title">Tidak ada data denda</h3>
                        <p class="empty-state-text">Belum ada transaksi yang terlambat</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = items.map(d => `
        <tr>
            <td><span class="font-medium">${d.kode_transaksi}</span></td>
            <td>${d.pelanggan_nama}</td>
            <td>${d.kendaraan_nama} (${d.plat_nomor})</td>
            <td>${d.jumlah_hari_terlambat} hari</td>
            <td><strong class="text-danger">${formatCurrency(d.total_denda)}</strong></td>
            <td>${renderBadge(d.status_pembayaran === 'Lunas' ? 'Lunas' : 'Belum Dibayar', d.status_pembayaran === 'Lunas' ? 'success' : 'warning')}</td>
            <td>
                ${d.status_pembayaran !== 'Lunas' ? `
                    <button class="btn btn-success btn-sm" onclick="bayarDenda(${d.id})">
                        <i class="ri-checkbox-circle-line"></i> Bayar
                    </button>
                ` : `<small class="text-muted">${formatDate(d.tanggal_bayar)}</small>`}
            </td>
        </tr>
    `).join('');

    renderPagination('dendaPagination', page, total_pages, (p) => loadDenda(p));
}

async function bayarDenda(id) {
    if (!confirm('Konfirmasi pembayaran denda ini?')) return;

    const result = await api('denda.php?action=bayar', {
        method: 'POST',
        body: { denda_id: id }
    });

    if (result.success) {
        showToast(result.message);
        loadDenda();
        loadDashboard();
    } else {
        showToast(result.message, 'error');
    }
}

// ==================== Laporan (Report) ====================
async function loadLaporan() {
    const dari = document.getElementById('laporanDari')?.value || '';
    const sampai = document.getElementById('laporanSampai')?.value || '';

    // Set default dates if empty
    if (!dari || !sampai) {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        document.getElementById('laporanDari').value = firstDay.toISOString().split('T')[0];
        document.getElementById('laporanSampai').value = today.toISOString().split('T')[0];
    }

    const result = await api(`laporan.php?action=riwayat-transaksi&tanggal_dari=${document.getElementById('laporanDari').value}&tanggal_sampai=${document.getElementById('laporanSampai').value}`);

    if (result.success) {
        renderLaporan(result.data);
    }
}

function renderLaporan(data) {
    // Summary
    document.getElementById('laporanTotalTransaksi').textContent = data.summary.total_transaksi;
    document.getElementById('laporanPendapatan').textContent = formatCurrency(data.summary.total_pendapatan);
    document.getElementById('laporanTotalDenda').textContent = formatCurrency(data.summary.total_denda || 0);

    // Table
    const tbody = document.getElementById('laporanTable');

    if (data.items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">Tidak ada data pada periode ini</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.items.map(t => `
        <tr>
            <td><span class="font-medium">${t.kode_transaksi}</span></td>
            <td>${t.pelanggan_nama}</td>
            <td>${t.kendaraan_nama}</td>
            <td>${formatDate(t.tanggal_sewa)}</td>
            <td>${formatDate(t.tanggal_kembali_aktual || t.tanggal_kembali_rencana)}</td>
            <td>${formatCurrency(t.total_biaya)}</td>
            <td>${renderBadge(t.status)}</td>
        </tr>
    `).join('');
}

function printLaporan() {
    window.print();
}

// ==================== Helper Functions ====================
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(new Date(dateStr));
}

function renderBadge(status, type = null) {
    const types = {
        'Menunggu': 'warning',
        'Disewa': 'primary',
        'Dikembalikan': 'success',
        'Terlambat': 'danger',
        'Dibatalkan': 'secondary',
        'Ditolak': 'danger',
        'Lunas': 'success',
        'Belum Dibayar': 'warning',
        'Tersedia': 'success',
        'Tidak Tersedia': 'secondary',
        'Mobil': 'primary',
        'Motor': 'info',
        'Sepeda': 'success',
        'Truk': 'warning',
        'Bus': 'secondary'
    };

    const badgeType = type || types[status] || 'secondary';
    return `<span class="badge badge-${badgeType}">${status}</span>`;
}

function renderPagination(containerId, currentPage, totalPages, onPageChange) {
    const container = document.getElementById(containerId);
    if (!container || totalPages <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    let html = '<div class="pagination">';

    // Previous button
    html += `<button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="(${onPageChange.toString()})(${currentPage - 1})">
        <i class="ri-arrow-left-s-line"></i>
    </button>`;

    // Page numbers
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);

    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="(${onPageChange.toString()})(${i})">${i}</button>`;
    }

    // Next button
    html += `<button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="(${onPageChange.toString()})(${currentPage + 1})">
        <i class="ri-arrow-right-s-line"></i>
    </button>`;

    html += '</div>';
    container.innerHTML = html;
}

// ==================== Mobile Menu ====================
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}

function toggleDesktopSidebar() {
    document.body.classList.toggle('sidebar-collapsed');

    // Update icon
    const icon = document.querySelector('.desktop-menu-btn i');
    if (document.body.classList.contains('sidebar-collapsed')) {
        icon.classList.replace('ri-menu-fold-line', 'ri-menu-unfold-line');
    } else {
        icon.classList.replace('ri-menu-unfold-line', 'ri-menu-fold-line');
    }
}


// ==================== Event Listeners ====================
document.addEventListener('DOMContentLoaded', () => {
    // Update footer with developer info
    const footerDev = document.querySelector('.footer-developer');
    if (footerDev) {
        footerDev.innerHTML = `Developed by <strong>${APP_CONFIG.developerName}</strong> | NIM: <strong>${APP_CONFIG.developerNIM}</strong>`;
    }

    // Check authentication
    checkAuth();

    // Modal close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Close sidebar overlay click
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    // Escape key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
});
