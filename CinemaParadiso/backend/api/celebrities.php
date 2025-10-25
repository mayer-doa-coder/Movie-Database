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
                $celebrity = $db->fetchOne("SELECT * FROM celebrities WHERE celebrity_id = ?", [$id]);
                
                if ($celebrity) {
                    // Get movies
                    $movies = $db->fetchAll("SELECT m.*, mc.role, mc.cast_type 
                        FROM movies m 
                        JOIN movie_cast mc ON m.movie_id = mc.movie_id 
                        WHERE mc.celebrity_id = ?", [$id]);
                    
                    $celebrity['movies'] = $movies;
                    echo json_encode(['success' => true, 'data' => $celebrity]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Celebrity not found']);
                }
            } else {
                $where = [];
                $params = [];
                
                if (isset($_GET['profession'])) {
                    $where[] = "profession = ?";
                    $params[] = $_GET['profession'];
                }
                
                if (isset($_GET['search'])) {
                    $where[] = "name LIKE ?";
                    $params[] = '%' . $_GET['search'] . '%';
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                $sql = "SELECT * FROM celebrities $whereClause ORDER BY name LIMIT $limit";
                $celebrities = $db->fetchAll($sql, $params);
                echo json_encode(['success' => true, 'data' => $celebrities]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sql = "INSERT INTO celebrities (name, birth_date, biography, profile_image, 
                    nationality, profession) VALUES (?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['name'],
                $data['birth_date'] ?? null,
                $data['biography'] ?? null,
                $data['profile_image'] ?? null,
                $data['nationality'] ?? null,
                $data['profession'] ?? null
            ];
            
            $result = $db->execute($sql, $params);
            echo json_encode([
                'success' => true,
                'message' => 'Celebrity created successfully',
                'celebrity_id' => $result['insert_id']
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['celebrity_id'])) {
                throw new Exception('Celebrity ID is required');
            }
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['name', 'birth_date', 'biography', 'profile_image', 
                             'nationality', 'profession'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $data['celebrity_id'];
            $sql = "UPDATE celebrities SET " . implode(', ', $updates) . " WHERE celebrity_id = ?";
            
            $db->execute($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Celebrity updated successfully']);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['celebrity_id'])) {
                throw new Exception('Celebrity ID is required');
            }
            
            $db->execute("DELETE FROM celebrities WHERE celebrity_id = ?", [$data['celebrity_id']]);
            echo json_encode(['success' => true, 'message' => 'Celebrity deleted successfully']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
