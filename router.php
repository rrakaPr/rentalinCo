<?php
/**
 * VHRent - Router Script for PHP Built-in Server
 * This handles static files and routing for Railway deployment
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if it's a static file that exists
$staticFile = __DIR__ . $uri;

// If the file exists and is not a directory, serve it
if ($uri !== '/' && file_exists($staticFile) && !is_dir($staticFile)) {
    // Get file extension
    $extension = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));

    // Set proper content type for static files
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'html' => 'text/html',
        'htm' => 'text/html',
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }

    // Return false to let PHP serve the file directly
    return false;
}

// Handle PHP files directly
if (preg_match('/\.php$/', $uri)) {
    $phpFile = __DIR__ . $uri;
    if (file_exists($phpFile)) {
        include $phpFile;
        exit;
    }
}

// Health check endpoint for Railway
if ($uri === '/health' || $uri === '/health.php') {
    include __DIR__ . '/health.php';
    exit;
}

// Default: serve index.php for the landing page
if ($uri === '/' || $uri === '/index.php') {
    include __DIR__ . '/index.php';
    exit;
}

// Serve index.html for admin panel
if ($uri === '/index.html' || $uri === '/admin' || $uri === '/admin/') {
    include __DIR__ . '/index.html';
    exit;
}

// Allow direct access to backend API endpoints
if (strpos($uri, '/backend/') === 0) {
    $backendFile = __DIR__ . $uri;
    if (file_exists($backendFile)) {
        include $backendFile;
        exit;
    }
}

// 404 for everything else
http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Not Found']);
