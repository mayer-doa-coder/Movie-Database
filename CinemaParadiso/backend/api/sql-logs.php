<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get recent SQL queries
    $logger = SQLLogger::getInstance();
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    $queries = $logger->readLogs($limit);
    
    echo json_encode([
        'success' => true,
        'data' => array_reverse($queries) // Most recent first
    ]);
    
} elseif ($method === 'DELETE') {
    // Clear logs
    $logger = SQLLogger::getInstance();
    $logger->clearLogs();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logs cleared successfully'
    ]);
    
} elseif ($method === 'POST') {
    // Get statistics
    $logger = SQLLogger::getInstance();
    $stats = $logger->getStats();
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}
?>
