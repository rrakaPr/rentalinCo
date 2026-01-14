<?php
/**
 * VHRent Router - Handles URL routing for PHP built-in server
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove trailing slash except for root
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Static file extensions
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'webp'];
$ext = pathinfo($path, PATHINFO_EXTENSION);

// Serve static files directly
if (in_array($ext, $staticExtensions) && file_exists(__DIR__ . $path)) {
    return false; // Let PHP built-in server handle it
}

// Route: /admin -> serve index.html (admin panel)
if ($path === '/admin' || strpos($path, '/admin') === 0) {
    // If accessing /admin without file, serve index.html
    if ($path === '/admin') {
        require __DIR__ . '/index.html';
        exit;
    }
}

// Route: /backend/* -> serve PHP API files
if (strpos($path, '/backend/') === 0) {
    $file = __DIR__ . $path;
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Route: / -> serve index.php (customer landing page)
if ($path === '/' || $path === '') {
    require __DIR__ . '/index.php';
    exit;
}

// Try to serve exact file if exists
$filePath = __DIR__ . $path;
if (file_exists($filePath)) {
    if (is_dir($filePath)) {
        // Check for index files in directory
        if (file_exists($filePath . '/index.php')) {
            require $filePath . '/index.php';
            exit;
        }
        if (file_exists($filePath . '/index.html')) {
            require $filePath . '/index.html';
            exit;
        }
    } else {
        // Serve the file
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($ext === 'php') {
            require $filePath;
            exit;
        }
        return false;
    }
}

// Fallback to index.php for SPA routing
require __DIR__ . '/index.php';
