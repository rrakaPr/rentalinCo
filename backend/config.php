<?php
/**
 * VHRent - Database Configuration
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 0);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
// ⚠️ UBAH NILAI INI SESUAI DENGAN HOSTING ANDA
define('DB_HOST', 'localhost');          // Host database (biasanya 'localhost')
define('DB_USER', 'root');               // Username database dari cPanel
define('DB_PASS', '');                   // Password database dari cPanel
define('DB_NAME', 'vhrent');             // Nama database yang Anda buat
define('DB_PORT', '3306');               // Port MySQL (biasanya 3306)

// Session configuration
session_start();

// Database connection class
class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $port = DB_PORT;

    private $conn;
    private $error;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            throw new Exception('Database connection failed: ' . $this->error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

// Response helper functions
function jsonResponse($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function successResponse($data = null, $message = 'Success')
{
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

function errorResponse($message = 'Error', $status = 400)
{
    jsonResponse([
        'success' => false,
        'message' => $message,
        'data' => null
    ], $status);
}

// Get JSON input
function getJsonInput()
{
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? [];
}

// Generate unique transaction code
function generateTransactionCode()
{
    return 'TRX' . date('Ymd') . strtoupper(substr(uniqid(), -5));
}
?>