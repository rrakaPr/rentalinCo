<?php
/**
 * VHRent - Denda (Penalty) API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'bayar':
        bayarDenda($conn);
        break;
    default:
        if ($method === 'GET') {
            if ($id) {
                getDenda($conn, $id);
            } else {
                getAllDenda($conn);
            }
        } else {
            errorResponse('Invalid action', 400);
        }
}

function getAllDenda($conn)
{
    $status = $_GET['status'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    if ($status) {
        $where[] = 'd.status_pembayaran = ?';
        $params[] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM denda d $whereClause";
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get data with joins
    $sql = "SELECT d.*, 
                   t.kode_transaksi, t.tanggal_kembali_rencana, t.tanggal_kembali_aktual,
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon,
                   k.nama as kendaraan_nama, k.plat_nomor
            FROM denda d 
            LEFT JOIN transaksi t ON d.transaksi_id = t.id 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            $whereClause 
            ORDER BY d.created_at DESC 
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

function getDenda($conn, $id)
{
    $sql = "SELECT d.*, 
                   t.kode_transaksi, t.tanggal_sewa, t.tanggal_kembali_rencana, t.tanggal_kembali_aktual, t.total_biaya,
                   p.nama as pelanggan_nama, p.no_telepon as pelanggan_telepon, p.alamat as pelanggan_alamat,
                   k.nama as kendaraan_nama, k.plat_nomor, k.jenis as kendaraan_jenis
            FROM denda d 
            LEFT JOIN transaksi t ON d.transaksi_id = t.id 
            LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
            LEFT JOIN kendaraan k ON t.kendaraan_id = k.id 
            WHERE d.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        errorResponse('Denda tidak ditemukan', 404);
    }

    successResponse($data);
}

function bayarDenda($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();

    if (empty($data['denda_id'])) {
        errorResponse('Denda ID wajib diisi');
    }

    // Check denda exists
    $stmt = $conn->prepare('SELECT * FROM denda WHERE id = ?');
    $stmt->execute([$data['denda_id']]);
    $denda = $stmt->fetch();

    if (!$denda) {
        errorResponse('Denda tidak ditemukan');
    }

    if ($denda['status_pembayaran'] === 'Lunas') {
        errorResponse('Denda sudah lunas');
    }

    // Update payment status
    $stmt = $conn->prepare('UPDATE denda SET status_pembayaran = ?, tanggal_bayar = CURDATE() WHERE id = ?');
    $stmt->execute(['Lunas', $data['denda_id']]);

    successResponse(null, 'Pembayaran denda berhasil');
}
?>