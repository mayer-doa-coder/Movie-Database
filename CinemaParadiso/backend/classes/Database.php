<?php
require_once __DIR__ . '/../config.php';

class Database {
    private static $instance = null;
    private $conn;
    private $logger;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
            $this->logger = SQLLogger::getInstance();
        } catch (Exception $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        // Log the query
        $this->logger->log($sql, $params);
        
        if (empty($params)) {
            $result = $this->conn->query($sql);
            if ($result === false) {
                $this->logger->logError($sql, $this->conn->error);
                throw new Exception($this->conn->error);
            }
            return $result;
        }
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            $this->logger->logError($sql, $this->conn->error);
            throw new Exception($this->conn->error);
        }
        
        if (!empty($params)) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            $stmt->bind_param($types, ...$values);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            // For INSERT, UPDATE, DELETE
            return $stmt;
        }
        
        return $result;
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result instanceof mysqli_result) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    public function execute($sql, $params = []) {
        $this->query($sql, $params);
        return [
            'affected_rows' => $this->conn->affected_rows,
            'insert_id' => $this->conn->insert_id
        ];
    }

    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    public function getAffectedRows() {
        return $this->conn->affected_rows;
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
