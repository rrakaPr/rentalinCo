<?php
/**
 * VHRent - Authentication API
 */

require_once 'config.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check':
        checkSession();
        break;
    case 'forgot-password':
        handleForgotPassword($conn);
        break;
    case 'reset-password':
        handleResetPassword($conn);
        break;
    case 'register':
        handleRegister($conn);
        break;
    // Customer authentication
    case 'customer-login':
        handleCustomerLogin($conn);
        break;
    case 'customer-register':
        handleCustomerRegister($conn);
        break;
    case 'customer-check':
        checkCustomerSession();
        break;
    case 'customer-logout':
        handleCustomerLogout();
        break;
    default:
        errorResponse('Invalid action', 400);
}

function handleLogin($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        errorResponse('Username dan password wajib diisi');
    }

    $stmt = $conn->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        errorResponse('Username atau password salah');
    }

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    $_SESSION['logged_in'] = true;

    successResponse([
        'id' => $admin['id'],
        'username' => $admin['username'],
        'nama_lengkap' => $admin['nama_lengkap']
    ], 'Login berhasil');
}

function handleLogout()
{
    session_destroy();
    successResponse(null, 'Logout berhasil');
}

function checkSession()
{
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        successResponse([
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'nama_lengkap' => $_SESSION['admin_nama']
        ], 'Session valid');
    } else {
        errorResponse('Unauthorized', 401);
    }
}

function handleForgotPassword($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $username = $data['username'] ?? '';

    if (empty($username)) {
        errorResponse('Username wajib diisi');
    }

    $stmt = $conn->prepare('SELECT nim FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin) {
        errorResponse('Username tidak ditemukan');
    }

    // Provide hint for NIM (9 digits)
    $nim = $admin['nim'];
    $hint = substr($nim, 0, 3) . '***' . substr($nim, -3);

    successResponse([
        'hint' => 'NIM Anda: ' . $hint . ' (9 angka)'
    ], 'Hint password dikirim');
}

function handleResetPassword($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $username = $data['username'] ?? '';
    $nim = $data['nim'] ?? '';
    $new_password = $data['new_password'] ?? '';

    if (empty($username) || empty($nim) || empty($new_password)) {
        errorResponse('Semua field wajib diisi');
    }

    if (strlen($nim) !== 9 || !ctype_digit($nim)) {
        errorResponse('NIM harus 9 angka');
    }

    if (strlen($new_password) < 6) {
        errorResponse('Password minimal 6 karakter');
    }

    $stmt = $conn->prepare('SELECT * FROM admins WHERE username = ? AND nim = ?');
    $stmt->execute([$username, $nim]);
    $admin = $stmt->fetch();

    if (!$admin) {
        errorResponse('Username atau NIM tidak cocok');
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE admins SET password = ? WHERE id = ?');
    $stmt->execute([$hashed, $admin['id']]);

    successResponse(null, 'Password berhasil direset');
}

function handleRegister($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $nama_lengkap = trim($data['nama_lengkap'] ?? '');
    $nim = trim($data['nim'] ?? '');
    $email = trim($data['email'] ?? '');

    // Validate required fields
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($nim)) {
        errorResponse('Username, password, nama lengkap, dan NIM wajib diisi');
    }

    // Validate username format (alphanumeric, 3-30 chars)
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        errorResponse('Username harus 3-30 karakter (huruf, angka, underscore)');
    }

    // Validate NIM (9 digits)
    if (strlen($nim) !== 9 || !ctype_digit($nim)) {
        errorResponse('NIM harus 9 angka');
    }

    // Validate password length
    if (strlen($password) < 6) {
        errorResponse('Password minimal 6 karakter');
    }

    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Format email tidak valid');
    }

    // Check if username already exists
    $stmt = $conn->prepare('SELECT id FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        errorResponse('Username sudah digunakan');
    }

    // Check if NIM already exists
    $stmt = $conn->prepare('SELECT id FROM admins WHERE nim = ?');
    $stmt->execute([$nim]);
    if ($stmt->fetch()) {
        errorResponse('NIM sudah terdaftar');
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin
    $sql = 'INSERT INTO admins (username, password, nama_lengkap, nim, email) VALUES (?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $hashed, $nama_lengkap, $nim, $email ?: null]);

    $newId = $conn->lastInsertId();

    successResponse([
        'id' => $newId,
        'username' => $username,
        'nama_lengkap' => $nama_lengkap
    ], 'Akun berhasil dibuat! Silakan login.');
}

// ==================== Customer Authentication ====================

function handleCustomerLogin($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        errorResponse('Email dan password wajib diisi');
    }

    $stmt = $conn->prepare('SELECT * FROM pelanggan WHERE email = ? AND is_registered = 1');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if (!$customer || !password_verify($password, $customer['password'])) {
        errorResponse('Email atau password salah');
    }

    $_SESSION['customer_id'] = $customer['id'];
    $_SESSION['customer_nama'] = $customer['nama'];
    $_SESSION['customer_email'] = $customer['email'];
    $_SESSION['customer_logged_in'] = true;

    successResponse([
        'id' => $customer['id'],
        'nama' => $customer['nama'],
        'email' => $customer['email'],
        'no_telepon' => $customer['no_telepon']
    ], 'Login berhasil');
}

function handleCustomerRegister($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        errorResponse('Method not allowed', 405);
    }

    $data = getJsonInput();
    $nama = trim($data['nama'] ?? '');
    $nik = trim($data['nik'] ?? '');
    $jenis_kelamin = $data['jenis_kelamin'] ?? '';
    $no_telepon = trim($data['no_telepon'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $alamat = trim($data['alamat'] ?? '');

    // Validate required fields
    if (empty($nama) || empty($nik) || empty($no_telepon) || empty($email) || empty($password)) {
        errorResponse('Nama, NIK, No Telepon, Email, dan Password wajib diisi');
    }

    // Validate NIK (16 digits)
    if (strlen($nik) !== 16 || !ctype_digit($nik)) {
        errorResponse('NIK harus 16 angka');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        errorResponse('Format email tidak valid');
    }

    // Validate password length
    if (strlen($password) < 6) {
        errorResponse('Password minimal 6 karakter');
    }

    // Check if email already registered
    $stmt = $conn->prepare('SELECT id FROM pelanggan WHERE email = ? AND is_registered = 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        errorResponse('Email sudah terdaftar');
    }

    // Check if NIK already exists
    $stmt = $conn->prepare('SELECT id, is_registered FROM pelanggan WHERE nik = ?');
    $stmt->execute([$nik]);
    $existing = $stmt->fetch();

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    if ($existing) {
        if ($existing['is_registered']) {
            errorResponse('NIK sudah terdaftar dengan akun lain');
        }
        // Update existing record with password
        $sql = 'UPDATE pelanggan SET nama = ?, jenis_kelamin = ?, no_telepon = ?, email = ?, password = ?, alamat = ?, is_registered = 1 WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nama, $jenis_kelamin ?: 'Laki-laki', $no_telepon, $email, $hashed, $alamat, $existing['id']]);
        $newId = $existing['id'];
    } else {
        // Insert new customer
        $sql = 'INSERT INTO pelanggan (nama, nik, jenis_kelamin, no_telepon, email, password, alamat, is_registered) VALUES (?, ?, ?, ?, ?, ?, ?, 1)';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nama, $nik, $jenis_kelamin ?: 'Laki-laki', $no_telepon, $email, $hashed, $alamat]);
        $newId = $conn->lastInsertId();
    }

    successResponse([
        'id' => $newId,
        'nama' => $nama,
        'email' => $email
    ], 'Pendaftaran berhasil! Silakan login.');
}

function checkCustomerSession()
{
    if (isset($_SESSION['customer_logged_in']) && $_SESSION['customer_logged_in'] === true) {
        successResponse([
            'id' => $_SESSION['customer_id'],
            'nama' => $_SESSION['customer_nama'],
            'email' => $_SESSION['customer_email']
        ], 'Session valid');
    } else {
        errorResponse('Unauthorized', 401);
    }
}

function handleCustomerLogout()
{
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_nama']);
    unset($_SESSION['customer_email']);
    unset($_SESSION['customer_logged_in']);
    successResponse(null, 'Logout berhasil');
}
?>