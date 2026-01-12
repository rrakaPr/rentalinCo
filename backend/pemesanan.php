<?php
/**
 * VHRent - Pemesanan API
 * Handle customer orders/bookings
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        createPemesanan($conn);
        break;
    case 'history':
        getHistory($conn);
        break;
    case 'detail':
        getDetail($conn);
        break;
    default:
        errorResponse('Invalid action', 400);
}

function createPemesanan($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    // Check customer session
    if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
        errorResponse('Silakan login terlebih dahulu', 401);
    }

    $data = getJsonInput();
    $kendaraan_id = intval($data['kendaraan_id'] ?? 0);
    $tanggal_sewa = $data['tanggal_sewa'] ?? '';
    $tanggal_kembali = $data['tanggal_kembali'] ?? '';
    $catatan = trim($data['catatan'] ?? '');

    // Validate
    if (empty($kendaraan_id) || empty($tanggal_sewa) || empty($tanggal_kembali)) {
        errorResponse('Kendaraan, tanggal sewa, dan tanggal kembali wajib diisi');
    }

    // Validate dates
    $sewa = new DateTime($tanggal_sewa);
    $kembali = new DateTime($tanggal_kembali);
    $today = new DateTime('today');

    if ($sewa < $today) {
        errorResponse('Tanggal sewa tidak boleh di masa lampau');
    }

    if ($kembali <= $sewa) {
        errorResponse('Tanggal kembali harus setelah tanggal sewa');
    }

    // Get vehicle data
    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ? AND status = "Tersedia"');
    $stmt->execute([$kendaraan_id]);
    $kendaraan = $stmt->fetch();

    if (!$kendaraan) {
        errorResponse('Kendaraan tidak ditemukan atau tidak tersedia');
    }

    if ($kendaraan['stok_tersedia'] <= 0) {
        errorResponse('Stok kendaraan habis');
    }

    // Calculate rental duration and cost
    $lama_hari = $kembali->diff($sewa)->days;
    if ($lama_hari < 1)
        $lama_hari = 1;

    $total_biaya = $kendaraan['harga_sewa_per_hari'] * $lama_hari;

    // Generate booking code
    $kode_pemesanan = 'PO' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));

    // Get customer data
    $pelanggan_id = $_SESSION['customer_id'];
    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE id = ?');
    $stmt->execute([$pelanggan_id]);
    $pelanggan = $stmt->fetch();

    try {
        $conn->beginTransaction();

        // Insert transaction with status 'Menunggu' (Pending)
        $sql = "INSERT INTO transaksi (
                    kode_transaksi, pelanggan_id, kendaraan_id, 
                    tanggal_sewa, tanggal_kembali_rencana, jumlah_hari,
                    harga_per_hari, total_biaya, status, catatan
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu', ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $kode_pemesanan,
            $pelanggan_id,
            $kendaraan_id,
            $tanggal_sewa,
            $tanggal_kembali,
            $lama_hari,
            $kendaraan['harga_sewa_per_hari'],
            $total_biaya,
            $catatan
        ]);

        $transaksi_id = $conn->lastInsertId();

        $conn->commit();

        // Return invoice data
        successResponse([
            'id' => $transaksi_id,
            'kode_pemesanan' => $kode_pemesanan,
            'pelanggan' => [
                'id' => $pelanggan['id'],
                'nama' => $pelanggan['nama'],
                'no_telepon' => $pelanggan['no_telepon'],
                'email' => $pelanggan['email']
            ],
            'kendaraan' => [
                'id' => $kendaraan['id'],
                'nama' => $kendaraan['nama'],
                'jenis' => $kendaraan['jenis'],
                'merk' => $kendaraan['merk'],
                'plat_nomor' => $kendaraan['plat_nomor'],
                'warna' => $kendaraan['warna'],
                'gambar_url' => $kendaraan['gambar_url']
            ],
            'tanggal_sewa' => $tanggal_sewa,
            'tanggal_kembali' => $tanggal_kembali,
            'lama_hari' => $lama_hari,
            'harga_per_hari' => floatval($kendaraan['harga_sewa_per_hari']),
            'total_biaya' => floatval($total_biaya),
            'status' => 'Menunggu',
            'catatan' => $catatan,
            'whatsapp_message' => generateWhatsAppMessage(
                $kode_pemesanan,
                $pelanggan['nama'],
                $kendaraan['nama'],
                $kendaraan['plat_nomor'],
                $tanggal_sewa,
                $tanggal_kembali,
                $lama_hari,
                $total_biaya
            )
        ], 'Pemesanan berhasil dibuat. Menunggu konfirmasi admin.');

    } catch (PDOException $e) {
        $conn->rollBack();
        errorResponse('Gagal menyimpan pemesanan: ' . $e->getMessage());
    }
}

function generateWhatsAppMessage($kode, $nama_pelanggan, $nama_kendaraan, $plat, $tgl_sewa, $tgl_kembali, $lama_hari, $total)
{
    $total_formatted = 'Rp ' . number_format($total, 0, ',', '.');

    $message = "Halo rentalinCo by rakaRent!\n\n";
    $message .= "Saya ingin melakukan pemesanan:\n\n";
    $message .= "📋 *Kode Pemesanan:* {$kode}\n";
    $message .= "👤 *Nama:* {$nama_pelanggan}\n";
    $message .= "🚗 *Kendaraan:* {$nama_kendaraan}\n";
    $message .= "🔢 *Plat Nomor:* {$plat}\n";
    $message .= "📅 *Tanggal Sewa:* {$tgl_sewa}\n";
    $message .= "📅 *Tanggal Kembali:* {$tgl_kembali}\n";
    $message .= "⏱️ *Durasi:* {$lama_hari} hari\n";
    $message .= "💰 *Total Biaya:* {$total_formatted}\n\n";
    $message .= "Mohon konfirmasi ketersediaan. Terima kasih!";

    return $message;
}

function getHistory($conn)
{
    // Check customer session
    if (!isset($_SESSION['customer_logged_in']) || !$_SESSION['customer_logged_in']) {
        errorResponse('Silakan login terlebih dahulu', 401);
    }

    $pelanggan_id = $_SESSION['customer_id'];

    $sql = "SELECT t.*, k.nama as kendaraan_nama, k.jenis, k.plat_nomor 
            FROM transaksi t 
            JOIN kendaraan k ON t.kendaraan_id = k.id 
            WHERE t.pelanggan_id = ? 
            ORDER BY t.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$pelanggan_id]);
    $transactions = $stmt->fetchAll();

    successResponse($transactions);
}

function getDetail($conn)
{
    $id = intval($_GET['id'] ?? 0);

    if (!$id) {
        errorResponse('ID tidak valid');
    }

    $sql = "SELECT t.*, k.nama as kendaraan_nama, k.jenis, k.merk, k.plat_nomor, k.warna,
                   p.nama as pelanggan_nama, p.no_telepon, p.email
            FROM transaksi t 
            JOIN kendaraan k ON t.kendaraan_id = k.id 
            JOIN pelanggan p ON t.pelanggan_id = p.id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        errorResponse('Transaksi tidak ditemukan');
    }

    successResponse($transaction);
}
?>