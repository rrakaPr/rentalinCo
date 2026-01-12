<?php
/**
 * VHRent - Transaksi (Transaction) API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'sewa':
        createSewa($conn);
        break;
    case 'kembali':
        processKembali($conn);
        break;
    case 'aktif':
        getTransaksiAktif($conn);
        break;
    case 'riwayat':
        getRiwayatTransaksi($conn);
        break;
    case 'pending':
        getPendingOrders($conn);
        break;
    case 'approve':
        approveOrder($conn);
        break;
    case 'reject':
        rejectOrder($conn);
        break;
    default:
        if ($method === 'GET') {
            if ($id) {
                getTransaksi($conn, $id);
            } else {
                getAllTransaksi($conn);
            }
        } else {
            errorResponse('Invalid action', 400);
        }
}

function getAllTransaksi($conn)
{
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $tanggal_dari = $_GET['tanggal_dari'] ?? '';
    $tanggal_sampai = $_GET['tanggal_sampai'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    if ($search) {
        $where[] = '(t.kode_transaksi LIKE ? OR p.nama LIKE ? OR k.nama LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($status) {
        $where[] = 't.status = ?';
        $params[] = $status;
    }

    if ($tanggal_dari) {
        $where[] = 't.tanggal_sewa >= ?';
        $params[] = $tanggal_dari;
    }

    if ($tanggal_sampai) {
        $where[] = 't.tanggal_sewa <= ?';
        $params[] = $tanggal_sampai;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM transaksi t 
                 LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
                 LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
                 $whereClause";
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get data with joins
    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis, k.gambar_url,
                   d.total_denda, d.status_pembayaran as status_denda
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            LEFT JOIN denda d ON t.id = d.transaksi_id
            $whereClause 
            ORDER BY t.created_at DESC 
            LIMIT $limit OFFSET $offset";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    successResponse([
        'items' => $data,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
}

function getTransaksi($conn, $id)
{
    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon, p.nik, p.alamat as pelanggan_alamat,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis, k.merk, k.gambar_url,
                   d.id as denda_id, d.jumlah_hari_terlambat, d.denda_per_hari, d.total_denda, d.status_pembayaran as status_denda
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            LEFT JOIN denda d ON t.id = d.transaksi_id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        errorResponse('Transaksi tidak ditemukan', 404);
    }

    successResponse($data);
}

function getTransaksiAktif($conn)
{
    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis, k.gambar_url,
                   DATEDIFF(CURDATE(), t.tanggal_kembali_rencana) as hari_terlambat,
                   DATEDIFF(t.tanggal_kembali_rencana, CURDATE()) as sisa_hari
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            WHERE t.status IN ('Disewa', 'Menunggu')
            ORDER BY 
                CASE WHEN t.status = 'Menunggu' THEN 0 ELSE 1 END,
                t.tanggal_kembali_rencana ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll();

    // Mark late transactions
    foreach ($data as &$item) {
        $item['is_terlambat'] = $item['hari_terlambat'] > 0 && $item['status'] === 'Disewa';
    }

    successResponse($data);
}

function getRiwayatTransaksi($conn)
{
    $pelanggan_id = $_GET['pelanggan_id'] ?? '';
    $kendaraan_id = $_GET['kendaraan_id'] ?? '';

    $where = ["t.status != 'Disewa'"];
    $params = [];

    if ($pelanggan_id) {
        $where[] = 't.pelanggan_id = ?';
        $params[] = $pelanggan_id;
    }

    if ($kendaraan_id) {
        $where[] = 't.kendaraan_id = ?';
        $params[] = $kendaraan_id;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama,
                   k.nama as kendaraan_nama, k.plat_nomor,
                   d.total_denda, d.status_pembayaran as status_denda
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            LEFT JOIN denda d ON t.id = d.transaksi_id
            $whereClause 
            ORDER BY t.created_at DESC 
            LIMIT 100";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    successResponse($data);
}

function createSewa($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();

    $required = ['pelanggan_id', 'kendaraan_id', 'tanggal_sewa', 'tanggal_kembali_rencana'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            errorResponse("Field $field wajib diisi");
        }
    }

    // Check pelanggan exists
    $stmt = $conn->prepare('SELECT id FROM pelanggan WHERE id = ?');
    $stmt->execute([$data['pelanggan_id']]);
    if (!$stmt->fetch()) {
        errorResponse('Pelanggan tidak ditemukan');
    }

    // Check kendaraan exists and available
    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ?');
    $stmt->execute([$data['kendaraan_id']]);
    $kendaraan = $stmt->fetch();

    if (!$kendaraan) {
        errorResponse('Kendaraan tidak ditemukan');
    }

    if ($kendaraan['stok_tersedia'] <= 0) {
        errorResponse('Stok kendaraan tidak tersedia');
    }

    // Calculate days and total
    $tanggal_sewa = new DateTime($data['tanggal_sewa']);
    $tanggal_kembali = new DateTime($data['tanggal_kembali_rencana']);
    $jumlah_hari = $tanggal_kembali->diff($tanggal_sewa)->days;

    if ($jumlah_hari < 1) {
        $jumlah_hari = 1;
    }

    $harga_per_hari = $kendaraan['harga_sewa_per_hari'];
    $total_biaya = $harga_per_hari * $jumlah_hari;

    // Generate transaction code
    $kode_transaksi = generateTransactionCode();

    // Get admin ID from session (default to 1 if not logged in for testing)
    $admin_id = $_SESSION['admin_id'] ?? 1;

    try {
        $conn->beginTransaction();

        // Insert transaction
        $sql = 'INSERT INTO transaksi (kode_transaksi, pelanggan_id, kendaraan_id, tanggal_sewa, tanggal_kembali_rencana, jumlah_hari, harga_per_hari, total_biaya, status, catatan, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $kode_transaksi,
            $data['pelanggan_id'],
            $data['kendaraan_id'],
            $data['tanggal_sewa'],
            $data['tanggal_kembali_rencana'],
            $jumlah_hari,
            $harga_per_hari,
            $total_biaya,
            'Disewa',
            $data['catatan'] ?? null,
            $admin_id
        ]);

        // Update stock
        $stmt = $conn->prepare('UPDATE kendaraan SET stok_tersedia = stok_tersedia - 1 WHERE id = ?');
        $stmt->execute([$data['kendaraan_id']]);

        $conn->commit();

        $newId = $conn->lastInsertId();

        // Get full transaction data
        $sql = "SELECT t.*, p.nama as pelanggan_nama, k.nama as kendaraan_nama, k.plat_nomor 
                FROM transaksi t 
                LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
                LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
                WHERE t.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newId]);
        $newData = $stmt->fetch();

        successResponse($newData, 'Transaksi penyewaan berhasil dibuat');

    } catch (Exception $e) {
        $conn->rollBack();
        errorResponse('Gagal membuat transaksi: ' . $e->getMessage());
    }
}

function processKembali($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();

    if (empty($data['transaksi_id'])) {
        errorResponse('Transaksi ID wajib diisi');
    }

    // Get transaction
    $stmt = $conn->prepare('SELECT t.*, k.harga_sewa_per_hari FROM transaksi t LEFT JOIN kendaraan k ON t.kendaraan_id = k.id WHERE t.id = ?');
    $stmt->execute([$data['transaksi_id']]);
    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        errorResponse('Transaksi tidak ditemukan');
    }

    if ($transaksi['status'] !== 'Disewa') {
        errorResponse('Transaksi sudah dikembalikan atau dibatalkan');
    }

    $tanggal_kembali_aktual = $data['tanggal_kembali'] ?? date('Y-m-d');

    // Calculate late days and penalty
    $tanggal_rencana = new DateTime($transaksi['tanggal_kembali_rencana']);
    $tanggal_aktual = new DateTime($tanggal_kembali_aktual);
    $hari_terlambat = 0;
    $total_denda = 0;

    if ($tanggal_aktual > $tanggal_rencana) {
        $hari_terlambat = $tanggal_aktual->diff($tanggal_rencana)->days;

        // Get penalty percentage from settings
        $stmt = $conn->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute(['denda_per_hari_persen']);
        $setting = $stmt->fetch();
        $persen_denda = $setting ? floatval($setting['setting_value']) : 10;

        $denda_per_hari = ($transaksi['harga_sewa_per_hari'] * $persen_denda) / 100;
        $total_denda = $denda_per_hari * $hari_terlambat;
    }

    try {
        $conn->beginTransaction();

        // Update transaction
        $status = $hari_terlambat > 0 ? 'Terlambat' : 'Dikembalikan';
        $stmt = $conn->prepare('UPDATE transaksi SET tanggal_kembali_aktual = ?, status = ? WHERE id = ?');
        $stmt->execute([$tanggal_kembali_aktual, $status, $data['transaksi_id']]);

        // Create penalty if late
        if ($hari_terlambat > 0) {
            $stmt = $conn->prepare('INSERT INTO denda (transaksi_id, jumlah_hari_terlambat, denda_per_hari, total_denda, keterangan) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $data['transaksi_id'],
                $hari_terlambat,
                $denda_per_hari,
                $total_denda,
                $data['keterangan'] ?? 'Denda keterlambatan pengembalian'
            ]);
        }

        // Update stock
        $stmt = $conn->prepare('UPDATE kendaraan SET stok_tersedia = stok_tersedia + 1 WHERE id = ?');
        $stmt->execute([$transaksi['kendaraan_id']]);

        $conn->commit();

        successResponse([
            'hari_terlambat' => $hari_terlambat,
            'total_denda' => $total_denda,
            'status' => $status
        ], 'Pengembalian berhasil diproses');

    } catch (Exception $e) {
        $conn->rollBack();
        errorResponse('Gagal memproses pengembalian: ' . $e->getMessage());
    }
}

/**
 * Get all pending orders (status = 'Menunggu')
 */
function getPendingOrders($conn)
{
    $sql = "SELECT t.*, 
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon, p.email as pelanggan_email,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis, k.gambar_url, k.merk
            FROM transaksi t 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            WHERE t.status = 'Menunggu'
            ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll();

    successResponse($data);
}

/**
 * Approve a pending order
 */
function approveOrder($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();

    if (empty($data['transaksi_id'])) {
        errorResponse('Transaksi ID wajib diisi');
    }

    // Get transaction
    $stmt = $conn->prepare('SELECT t.*, k.stok_tersedia FROM transaksi t LEFT JOIN kendaraan k ON t.kendaraan_id = k.id WHERE t.id = ?');
    $stmt->execute([$data['transaksi_id']]);
    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        errorResponse('Transaksi tidak ditemukan');
    }

    if ($transaksi['status'] !== 'Menunggu') {
        errorResponse('Transaksi sudah diproses sebelumnya');
    }

    // Check stock availability
    if ($transaksi['stok_tersedia'] <= 0) {
        errorResponse('Stok kendaraan tidak tersedia');
    }

    // Get admin ID from session
    $admin_id = $_SESSION['admin_id'] ?? 1;

    try {
        $conn->beginTransaction();

        // Update transaction status to 'Disewa'
        $stmt = $conn->prepare('UPDATE transaksi SET status = ?, admin_id = ? WHERE id = ?');
        $stmt->execute(['Disewa', $admin_id, $data['transaksi_id']]);

        // Decrease stock
        $stmt = $conn->prepare('UPDATE kendaraan SET stok_tersedia = stok_tersedia - 1 WHERE id = ?');
        $stmt->execute([$transaksi['kendaraan_id']]);

        $conn->commit();

        successResponse([
            'id' => $data['transaksi_id'],
            'status' => 'Disewa'
        ], 'Pemesanan berhasil disetujui');

    } catch (Exception $e) {
        $conn->rollBack();
        errorResponse('Gagal menyetujui pemesanan: ' . $e->getMessage());
    }
}

/**
 * Reject a pending order
 */
function rejectOrder($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();

    if (empty($data['transaksi_id'])) {
        errorResponse('Transaksi ID wajib diisi');
    }

    // Get transaction
    $stmt = $conn->prepare('SELECT * FROM transaksi WHERE id = ?');
    $stmt->execute([$data['transaksi_id']]);
    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        errorResponse('Transaksi tidak ditemukan');
    }

    if ($transaksi['status'] !== 'Menunggu') {
        errorResponse('Transaksi sudah diproses sebelumnya');
    }

    // Get admin ID from session
    $admin_id = $_SESSION['admin_id'] ?? 1;
    $alasan = $data['alasan'] ?? 'Pemesanan ditolak oleh admin';

    try {
        $conn->beginTransaction();

        // Update transaction status to 'Ditolak'
        $stmt = $conn->prepare('UPDATE transaksi SET status = ?, admin_id = ?, catatan = CONCAT(IFNULL(catatan, ""), "\n[DITOLAK] ", ?) WHERE id = ?');
        $stmt->execute(['Ditolak', $admin_id, $alasan, $data['transaksi_id']]);

        $conn->commit();

        successResponse([
            'id' => $data['transaksi_id'],
            'status' => 'Ditolak'
        ], 'Pemesanan telah ditolak');

    } catch (Exception $e) {
        $conn->rollBack();
        errorResponse('Gagal menolak pemesanan: ' . $e->getMessage());
    }
}
?>