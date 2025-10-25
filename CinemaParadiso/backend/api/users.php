<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $user = $db->fetchOne("SELECT user_id, username, email, full_name, bio, 
                    avatar_url, date_of_birth, country, created_at, last_login, is_active 
                    FROM users WHERE user_id = ?", [$id]);
                
                if ($user) {
                    // Get user statistics
                    $stats = [
                        'total_reviews' => $db->fetchOne("SELECT COUNT(*) as count FROM reviews WHERE user_id = ?", [$id])['count'],
                        'watchlist_count' => $db->fetchOne("SELECT COUNT(*) as count FROM watchlist WHERE user_id = ?", [$id])['count'],
                        'favorites_count' => $db->fetchOne("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?", [$id])['count'],
                        'followers' => $db->fetchOne("SELECT COUNT(*) as count FROM user_follows WHERE following_id = ?", [$id])['count'],
                        'following' => $db->fetchOne("SELECT COUNT(*) as count FROM user_follows WHERE follower_id = ?", [$id])['count']
                    ];
                    
                    $user['stats'] = $stats;
                    echo json_encode(['success' => true, 'data' => $user]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'User not found']);
                }
            } else {
                $where = [];
                $params = [];
                
                if (isset($_GET['search'])) {
                    $where[] = "(username LIKE ? OR full_name LIKE ?)";
                    $params[] = '%' . $_GET['search'] . '%';
                    $params[] = '%' . $_GET['search'] . '%';
                }
                
                if (isset($_GET['country'])) {
                    $where[] = "country = ?";
                    $params[] = $_GET['country'];
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                $sql = "SELECT user_id, username, email, full_name, country, created_at, is_active 
                    FROM users $whereClause ORDER BY created_at DESC LIMIT $limit";
                
                $users = $db->fetchAll($sql, $params);
                echo json_encode(['success' => true, 'data' => $users]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Simple password hash for demo
            $passwordHash = password_hash($data['password'] ?? 'password123', PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password_hash, full_name, 
                    bio, date_of_birth, country) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['username'],
                $data['email'],
                $passwordHash,
                $data['full_name'] ?? null,
                $data['bio'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['country'] ?? null
            ];
            
            $result = $db->execute($sql, $params);
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $result['insert_id']
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id'])) {
                throw new Exception('User ID is required');
            }
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['username', 'email', 'full_name', 'bio', 
                             'avatar_url', 'date_of_birth', 'country', 'is_active'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $data['user_id'];
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
            
            $db->execute($sql, $params);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id'])) {
                throw new Exception('User ID is required');
            }
            
            $db->execute("DELETE FROM users WHERE user_id = ?", [$data['user_id']]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
