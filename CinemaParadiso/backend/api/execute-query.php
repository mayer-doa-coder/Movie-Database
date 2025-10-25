<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['query']) || empty(trim($data['query']))) {
        echo json_encode([
            'success' => false,
            'error' => 'No SQL query provided'
        ]);
        exit;
    }

    $query = trim($data['query']);
    
    // Security: Basic validation
    $dangerousPatterns = [
        '/DROP\s+DATABASE/i',
        '/GRANT\s+/i',
        '/REVOKE\s+/i',
        '/CREATE\s+USER/i',
        '/DROP\s+USER/i'
    ];

    foreach ($dangerousPatterns as $pattern) {
        if (preg_match($pattern, $query)) {
            echo json_encode([
                'success' => false,
                'error' => 'This query contains restricted operations'
            ]);
            exit;
        }
    }

    try {
        $db = Database::getInstance();
        $startTime = microtime(true);
        
        // Determine query type
        $queryType = strtoupper(substr(ltrim($query), 0, 6));
        
        if (strpos($queryType, 'SELECT') === 0 || strpos($queryType, 'SHOW') === 0 || 
            strpos($queryType, 'DESCRI') === 0 || strpos($queryType, 'EXPLAI') === 0) {
            // SELECT query - return results
            $result = $db->fetchAll($query);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            echo json_encode([
                'success' => true,
                'type' => 'select',
                'data' => $result,
                'rows' => count($result),
                'execution_time' => $executionTime . 'ms'
            ]);
            
        } else {
            // INSERT, UPDATE, DELETE, etc.
            $result = $db->execute($query);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            echo json_encode([
                'success' => true,
                'type' => 'modification',
                'affected_rows' => $result['affected_rows'],
                'insert_id' => $result['insert_id'],
                'execution_time' => $executionTime . 'ms'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>
