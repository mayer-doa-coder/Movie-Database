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
                // Get single movie
                $id = (int)$_GET['id'];
                $movie = $db->fetchOne("SELECT m.*, c.name as director_name 
                    FROM movies m 
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id 
                    WHERE m.movie_id = ?", [$id]);
                
                if ($movie) {
                    // Get cast
                    $cast = $db->fetchAll("SELECT mc.*, c.name, c.profile_image 
                        FROM movie_cast mc 
                        JOIN celebrities c ON mc.celebrity_id = c.celebrity_id 
                        WHERE mc.movie_id = ? 
                        ORDER BY mc.cast_order", [$id]);
                    
                    $movie['cast'] = $cast;
                    
                    echo json_encode(['success' => true, 'data' => $movie]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Movie not found']);
                }
            } else {
                // Get all movies with filters
                $where = [];
                $params = [];
                
                if (isset($_GET['genre'])) {
                    $where[] = "genre LIKE ?";
                    $params[] = '%' . $_GET['genre'] . '%';
                }
                
                if (isset($_GET['year'])) {
                    $where[] = "YEAR(release_date) = ?";
                    $params[] = (int)$_GET['year'];
                }
                
                if (isset($_GET['search'])) {
                    $where[] = "title LIKE ?";
                    $params[] = '%' . $_GET['search'] . '%';
                }
                
                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                $orderBy = isset($_GET['order']) ? $_GET['order'] : 'rating DESC';
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                $sql = "SELECT m.*, c.name as director_name 
                    FROM movies m 
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id 
                    $whereClause 
                    ORDER BY $orderBy 
                    LIMIT $limit";
                
                $movies = $db->fetchAll($sql, $params);
                echo json_encode(['success' => true, 'data' => $movies, 'count' => count($movies)]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sql = "INSERT INTO movies (title, release_date, duration, plot_summary, 
                    poster_url, trailer_url, genre, language, country, rating, director_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['title'],
                $data['release_date'] ?? null,
                $data['duration'] ?? null,
                $data['plot_summary'] ?? null,
                $data['poster_url'] ?? null,
                $data['trailer_url'] ?? null,
                $data['genre'] ?? null,
                $data['language'] ?? null,
                $data['country'] ?? null,
                $data['rating'] ?? null,
                $data['director_id'] ?? null
            ];
            
            $result = $db->execute($sql, $params);
            echo json_encode([
                'success' => true,
                'message' => 'Movie created successfully',
                'movie_id' => $result['insert_id']
            ]);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['movie_id'])) {
                throw new Exception('Movie ID is required');
            }
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['title', 'release_date', 'duration', 'plot_summary', 
                             'poster_url', 'trailer_url', 'genre', 'language', 
                             'country', 'rating', 'director_id'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $data['movie_id'];
            $sql = "UPDATE movies SET " . implode(', ', $updates) . " WHERE movie_id = ?";
            
            $db->execute($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Movie updated successfully']);
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['movie_id'])) {
                throw new Exception('Movie ID is required');
            }
            
            $db->execute("DELETE FROM movies WHERE movie_id = ?", [$data['movie_id']]);
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
