<?php
class SQLLogger {
    private static $instance = null;
    private $logFile;
    private $queries = [];
    private $maxQueries = 100; // Keep last 100 queries in memory

    private function __construct() {
        $this->logFile = __DIR__ . '/../logs/sql_queries.json';
        $this->ensureLogFileExists();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function ensureLogFileExists() {
        if (!file_exists($this->logFile)) {
            file_put_contents($this->logFile, json_encode([]));
        }
    }

    public function log($sql, $params = [], $type = 'query') {
        $logEntry = [
            'id' => uniqid(),
            'timestamp' => microtime(true),
            'datetime' => date('Y-m-d H:i:s'),
            'query' => $this->formatQuery($sql, $params),
            'raw_query' => $sql,
            'params' => $params,
            'type' => $this->detectQueryType($sql),
            'status' => 'success'
        ];

        $this->queries[] = $logEntry;
        
        // Keep only last N queries in memory
        if (count($this->queries) > $this->maxQueries) {
            array_shift($this->queries);
        }

        // Write to file
        $this->writeToFile($logEntry);

        return $logEntry;
    }

    public function logError($sql, $error) {
        $logEntry = [
            'id' => uniqid(),
            'timestamp' => microtime(true),
            'datetime' => date('Y-m-d H:i:s'),
            'query' => $sql,
            'type' => $this->detectQueryType($sql),
            'status' => 'error',
            'error' => $error
        ];

        $this->queries[] = $logEntry;
        $this->writeToFile($logEntry);

        return $logEntry;
    }

    private function formatQuery($sql, $params) {
        $formatted = $sql;
        
        if (!empty($params)) {
            foreach ($params as $param) {
                $value = is_string($param) ? "'" . addslashes($param) . "'" : $param;
                $formatted = preg_replace('/\?/', $value, $formatted, 1);
            }
        }

        return $formatted;
    }

    private function detectQueryType($sql) {
        $sql = strtoupper(trim($sql));
        
        if (strpos($sql, 'SELECT') === 0) return 'SELECT';
        if (strpos($sql, 'INSERT') === 0) return 'INSERT';
        if (strpos($sql, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($sql, 'DELETE') === 0) return 'DELETE';
        if (strpos($sql, 'CREATE') === 0) return 'CREATE';
        if (strpos($sql, 'DROP') === 0) return 'DROP';
        if (strpos($sql, 'ALTER') === 0) return 'ALTER';
        if (strpos($sql, 'TRUNCATE') === 0) return 'TRUNCATE';
        
        return 'OTHER';
    }

    private function writeToFile($logEntry) {
        // Read existing logs
        $logs = $this->readLogs();
        
        // Add new entry
        $logs[] = $logEntry;
        
        // Keep only last 100 in file
        if (count($logs) > $this->maxQueries) {
            $logs = array_slice($logs, -$this->maxQueries);
        }
        
        // Write back
        file_put_contents($this->logFile, json_encode($logs, JSON_PRETTY_PRINT));
    }

    public function readLogs($limit = null) {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $content = file_get_contents($this->logFile);
        $logs = json_decode($content, true) ?: [];

        if ($limit) {
            return array_slice($logs, -$limit);
        }

        return $logs;
    }

    public function getRecentQueries($limit = 50) {
        return array_slice($this->queries, -$limit);
    }

    public function clearLogs() {
        $this->queries = [];
        file_put_contents($this->logFile, json_encode([]));
    }

    public function getStats() {
        $logs = $this->readLogs();
        
        $stats = [
            'total_queries' => count($logs),
            'by_type' => [],
            'success_rate' => 0,
            'recent_errors' => []
        ];

        $successCount = 0;
        
        foreach ($logs as $log) {
            $type = $log['type'];
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = 0;
            }
            $stats['by_type'][$type]++;

            if ($log['status'] === 'success') {
                $successCount++;
            } else {
                $stats['recent_errors'][] = [
                    'datetime' => $log['datetime'],
                    'query' => $log['query'],
                    'error' => $log['error'] ?? 'Unknown error'
                ];
            }
        }

        if (count($logs) > 0) {
            $stats['success_rate'] = round(($successCount / count($logs)) * 100, 2);
        }

        // Keep only last 10 errors
        $stats['recent_errors'] = array_slice($stats['recent_errors'], -10);

        return $stats;
    }
}
?>
