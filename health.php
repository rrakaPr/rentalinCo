<?php
/**
 * Health Check Endpoint for Railway
 * Returns 200 OK if the application is running
 */
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'VHRent API'
]);
