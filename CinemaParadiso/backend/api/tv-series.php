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
                $series = $db->fetchOne("SELECT ts.*, c.name as creator_name 
                    FROM tv_series ts 
                    LEFT JOIN celebrities c ON ts.creator_id = c.celebrity_id 
                    WHERE ts.series_id = ?", [$id]);
                
                if ($series) {
                    $cast = $db->fetchAll("SELECT sc.*, c.name, c.profile_image 
                        FROM series_cast sc 
                        JOIN celebrities c ON sc.celebrity_id = c.celebrity_id 
                        WHERE sc.series_id = ? 
                        ORDER BY sc.cast_order", [$id]);
                    
                    $series['cast'] = $cast;
                    echo json_encode(['success' => true, 'data' => $series]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Series not found']);
                }
            } else {
                $where = [];
                $params = [];
                
                if (isset($_GET['status'])) {
                    $where[] = "status = ?";
                    $params[] = $_GET['status'];
                }
                
                if (isset($_GET['search'])) {
                    $where[] = "title LIKE ?";
                    $params[] = '%' . $_GET['search'] . '%';
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                $sql = "SELECT ts.*, c.name as creator_name 
                    FROM tv_series ts 
                    LEFT JOIN celebrities c ON ts.creator_id = c.celebrity_id 
                    $whereClause 
                    ORDER BY rating DESC 
                    LIMIT $limit";
                
                $series = $db->fetchAll($sql, $params);
                echo json_encode(['success' => true, 'data' => $series]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sql = "INSERT INTO tv_series (title, first_air_date, last_air_date, 
                    number_of_seasons, number_of_episodes, plot_summary, poster_url, 
                    trailer_url, genre, language, country, status, rating, creator_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['title'],
                $data['first_air_date'] ?? null,
                $data['last_air_date'] ?? null,
                $data['number_of_seasons'] ?? null,
                $data['number_of_episodes'] ?? null,
                $data['plot_summary'] ?? null,
                $data['poster_url'] ?? null,
                $data['trailer_url'] ?? null,
                $data['genre'] ?? null,
                $data['language'] ?? null,
                $data['country'] ?? null,
                $data['status'] ?? 'Ongoing',
                $data['rating'] ?? null,
                $data['creator_id'] ?? null
            ];
            
            $result = $db->execute($sql, $params);
            echo json_encode([
                'success' => true,
                'message' => 'TV Series created successfully',
                'series_id' => $result['insert_id']
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['series_id'])) {
                throw new Exception('Series ID is required');
            }
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['title', 'first_air_date', 'last_air_date', 
                             'number_of_seasons', 'number_of_episodes', 'plot_summary', 
                             'poster_url', 'trailer_url', 'genre', 'language', 
                             'country', 'status', 'rating', 'creator_id'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $data['series_id'];
            $sql = "UPDATE tv_series SET " . implode(', ', $updates) . " WHERE series_id = ?";
            
            $db->execute($sql, $params);
            echo json_encode(['success' => true, 'message' => 'TV Series updated successfully']);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['series_id'])) {
                throw new Exception('Series ID is required');
            }
            
            $db->execute("DELETE FROM tv_series WHERE series_id = ?", [$data['series_id']]);
            echo json_encode(['success' => true, 'message' => 'TV Series deleted successfully']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
