<?php
/**
 * VHRent - Kendaraan (Vehicle) API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            getKendaraan($conn, $id);
        } else {
            getAllKendaraan($conn);
        }
        break;
    case 'POST':
        createKendaraan($conn);
        break;
    case 'PUT':
        if ($id) {
            updateKendaraan($conn, $id);
        } else {
            errorResponse('ID required for update');
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteKendaraan($conn, $id);
        } else {
            errorResponse('ID required for delete');
        }
        break;
    default:
        errorResponse('Method not allowed', 405);
}

function getAllKendaraan($conn)
{
    $search = $_GET['search'] ?? '';
    $jenis = $_GET['jenis'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    if ($search) {
        $where[] = '(nama LIKE ? OR merk LIKE ? OR plat_nomor LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($jenis) {
        $where[] = 'jenis = ?';
        $params[] = $jenis;
    }

    if ($status) {
        $where[] = 'status = ?';
        $params[] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM kendaraan $whereClause";
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get data
    $sql = "SELECT * FROM kendaraan $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
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

function getKendaraan($conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ?');
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        errorResponse('Kendaraan tidak ditemukan', 404);
    }

    successResponse($data);
}

function createKendaraan($conn)
{
    $data = getJsonInput();

    $required = ['nama', 'jenis', 'merk', 'tahun', 'plat_nomor', 'harga_sewa_per_hari'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            errorResponse("Field $field wajib diisi");
        }
    }

    // Check unique plat_nomor
    $stmt = $conn->prepare('SELECT id FROM kendaraan WHERE plat_nomor = ?');
    $stmt->execute([$data['plat_nomor']]);
    if ($stmt->fetch()) {
        errorResponse('Plat nomor sudah terdaftar');
    }

    $sql = 'INSERT INTO kendaraan (nama, jenis, merk, tahun, plat_nomor, warna, harga_sewa_per_hari, stok, stok_tersedia, gambar_url, deskripsi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stok = intval($data['stok'] ?? 1);

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $data['nama'],
        $data['jenis'],
        $data['merk'],
        $data['tahun'],
        $data['plat_nomor'],
        $data['warna'] ?? null,
        $data['harga_sewa_per_hari'],
        $stok,
        $stok,
        $data['gambar_url'] ?? null,
        $data['deskripsi'] ?? null,
        $data['status'] ?? 'Tersedia'
    ]);

    $newId = $conn->lastInsertId();

    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ?');
    $stmt->execute([$newId]);
    $newData = $stmt->fetch();

    successResponse($newData, 'Kendaraan berhasil ditambahkan');
}

function updateKendaraan($conn, $id)
{
    $data = getJsonInput();

    // Check exists
    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        errorResponse('Kendaraan tidak ditemukan', 404);
    }

    // Check unique plat_nomor if changed
    if (isset($data['plat_nomor']) && $data['plat_nomor'] !== $existing['plat_nomor']) {
        $stmt = $conn->prepare('SELECT id FROM kendaraan WHERE plat_nomor = ? AND id != ?');
        $stmt->execute([$data['plat_nomor'], $id]);
        if ($stmt->fetch()) {
            errorResponse('Plat nomor sudah terdaftar');
        }
    }

    $fields = ['nama', 'jenis', 'merk', 'tahun', 'plat_nomor', 'warna', 'harga_sewa_per_hari', 'stok', 'gambar_url', 'deskripsi', 'status'];
    $updates = [];
    $params = [];

    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }

    if (empty($updates)) {
        errorResponse('Tidak ada data yang diubah');
    }

    $params[] = $id;
    $sql = 'UPDATE kendaraan SET ' . implode(', ', $updates) . ' WHERE id = ?';

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Update stok_tersedia if stok changed
    if (isset($data['stok'])) {
        $stmt = $conn->prepare('SELECT COUNT(*) as rented FROM transaksi WHERE kendaraan_id = ? AND status = "Disewa"');
        $stmt->execute([$id]);
        $rented = $stmt->fetch()['rented'];
        $tersedia = max(0, intval($data['stok']) - $rented);

        $stmt = $conn->prepare('UPDATE kendaraan SET stok_tersedia = ? WHERE id = ?');
        $stmt->execute([$tersedia, $id]);
    }

    $stmt = $conn->prepare('SELECT * FROM kendaraan WHERE id = ?');
    $stmt->execute([$id]);
    $updated = $stmt->fetch();

    successResponse($updated, 'Kendaraan berhasil diupdate');
}

function deleteKendaraan($conn, $id)
{
    // Check if has active transactions
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM transaksi WHERE kendaraan_id = ? AND status = "Disewa"');
    $stmt->execute([$id]);
    if ($stmt->fetch()['count'] > 0) {
        errorResponse('Tidak dapat menghapus kendaraan yang sedang disewa');
    }

    $stmt = $conn->prepare('DELETE FROM kendaraan WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        errorResponse('Kendaraan tidak ditemukan', 404);
    }

    successResponse(null, 'Kendaraan berhasil dihapus');
}
?>