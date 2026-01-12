<?php
/**
 * VHRent - Pelanggan (Customer) API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            getPelanggan($conn, $id);
        } else {
            getAllPelanggan($conn);
        }
        break;
    case 'POST':
        createPelanggan($conn);
        break;
    case 'PUT':
        if ($id) {
            updatePelanggan($conn, $id);
        } else {
            errorResponse('ID required for update');
        }
        break;
    case 'DELETE':
        if ($id) {
            deletePelanggan($conn, $id);
        } else {
            errorResponse('ID required for delete');
        }
        break;
    default:
        errorResponse('Method not allowed', 405);
}

function getAllPelanggan($conn)
{
    $search = $_GET['search'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    if ($search) {
        $where[] = '(nama LIKE ? OR nik LIKE ? OR no_telepon LIKE ? OR email LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM pelanggan $whereClause";
    $stmt = $conn->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get data
    $sql = "SELECT * FROM pelanggan $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
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

function getPelanggan($conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE id = ?');
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        errorResponse('Pelanggan tidak ditemukan', 404);
    }

    successResponse($data);
}

function createPelanggan($conn)
{
    $data = getJsonInput();

    $required = ['nama', 'nik', 'no_telepon', 'jenis_kelamin'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            errorResponse("Field $field wajib diisi");
        }
    }

    // Validate NIK
    if (strlen($data['nik']) !== 16 || !ctype_digit($data['nik'])) {
        errorResponse('NIK harus 16 angka');
    }

    // Check unique NIK
    $stmt = $conn->prepare('SELECT id FROM pelanggan WHERE nik = ?');
    $stmt->execute([$data['nik']]);
    if ($stmt->fetch()) {
        errorResponse('NIK sudah terdaftar');
    }

    $sql = 'INSERT INTO pelanggan (nama, nik, no_telepon, email, alamat, jenis_kelamin, tanggal_lahir, no_sim) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $data['nama'],
        $data['nik'],
        $data['no_telepon'],
        $data['email'] ?? null,
        $data['alamat'] ?? null,
        $data['jenis_kelamin'],
        $data['tanggal_lahir'] ?? null,
        $data['no_sim'] ?? null
    ]);

    $newId = $conn->lastInsertId();

    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE id = ?');
    $stmt->execute([$newId]);
    $newData = $stmt->fetch();

    successResponse($newData, 'Pelanggan berhasil ditambahkan');
}

function updatePelanggan($conn, $id)
{
    $data = getJsonInput();

    // Check exists
    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE id = ?');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        errorResponse('Pelanggan tidak ditemukan', 404);
    }

    // Check unique NIK if changed
    if (isset($data['nik']) && $data['nik'] !== $existing['nik']) {
        if (strlen($data['nik']) !== 16 || !ctype_digit($data['nik'])) {
            errorResponse('NIK harus 16 angka');
        }

        $stmt = $conn->prepare('SELECT id FROM pelanggan WHERE nik = ? AND id != ?');
        $stmt->execute([$data['nik'], $id]);
        if ($stmt->fetch()) {
            errorResponse('NIK sudah terdaftar');
        }
    }

    $fields = ['nama', 'nik', 'no_telepon', 'email', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'no_sim'];
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
    $sql = 'UPDATE pelanggan SET ' . implode(', ', $updates) . ' WHERE id = ?';

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE id = ?');
    $stmt->execute([$id]);
    $updated = $stmt->fetch();

    successResponse($updated, 'Pelanggan berhasil diupdate');
}

function deletePelanggan($conn, $id)
{
    // Check if has active transactions
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM transaksi WHERE pelanggan_id = ? AND status = "Disewa"');
    $stmt->execute([$id]);
    if ($stmt->fetch()['count'] > 0) {
        errorResponse('Tidak dapat menghapus pelanggan yang memiliki transaksi aktif');
    }

    $stmt = $conn->prepare('DELETE FROM pelanggan WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        errorResponse('Pelanggan tidak ditemukan', 404);
    }

    successResponse(null, 'Pelanggan berhasil dihapus');
}
?>