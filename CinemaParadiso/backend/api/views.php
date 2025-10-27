<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'list':
                // List all available views
                echo json_encode([
                    'success' => true,
                    'message' => 'Available database views',
                    'views' => [
                        'movies_with_directors' => 'Movies with director information',
                        'series_with_creators' => 'TV Series with creator information',
                        'top_rated_content' => 'Top rated movies and series (rating >= 8.0)',
                        'user_statistics' => 'User activity statistics',
                        'celebrity_filmography' => 'Celebrity filmography summary',
                        'recent_reviews' => 'Recent reviews with details'
                    ]
                ]);
                break;
                
            case 'movies_with_directors':
                // Query: Movies with Directors View
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $result = $db->fetchAll("SELECT * FROM v_movies_with_directors ORDER BY rating DESC LIMIT $limit");
                echo json_encode([
                    'success' => true,
                    'view' => 'v_movies_with_directors',
                    'description' => 'Movies with director information',
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'series_with_creators':
                // Query: TV Series with Creators View
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $result = $db->fetchAll("SELECT * FROM v_series_with_creators ORDER BY rating DESC LIMIT $limit");
                echo json_encode([
                    'success' => true,
                    'view' => 'v_series_with_creators',
                    'description' => 'TV Series with creator information',
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'top_rated_content':
                // Query: Top Rated Content View
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 30;
                $result = $db->fetchAll("SELECT * FROM v_top_rated_content ORDER BY rating DESC LIMIT $limit");
                echo json_encode([
                    'success' => true,
                    'view' => 'v_top_rated_content',
                    'description' => 'Top rated content (movies and series)',
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'user_statistics':
                // Query: User Statistics View
                $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
                
                if ($userId) {
                    $result = $db->fetchOne("SELECT * FROM v_user_statistics WHERE user_id = ?", [$userId]);
                    echo json_encode([
                        'success' => true,
                        'view' => 'v_user_statistics',
                        'description' => 'User activity statistics',
                        'data' => $result
                    ]);
                } else {
                    $result = $db->fetchAll("SELECT * FROM v_user_statistics ORDER BY total_reviews DESC");
                    echo json_encode([
                        'success' => true,
                        'view' => 'v_user_statistics',
                        'description' => 'All users activity statistics',
                        'count' => count($result),
                        'data' => $result
                    ]);
                }
                break;
                
            case 'celebrity_filmography':
                // Query: Celebrity Filmography View
                $celebId = isset($_GET['celebrity_id']) ? (int)$_GET['celebrity_id'] : null;
                
                if ($celebId) {
                    $result = $db->fetchOne("SELECT * FROM v_celebrity_filmography WHERE celebrity_id = ?", [$celebId]);
                    echo json_encode([
                        'success' => true,
                        'view' => 'v_celebrity_filmography',
                        'description' => 'Celebrity filmography summary',
                        'data' => $result
                    ]);
                } else {
                    $result = $db->fetchAll("SELECT * FROM v_celebrity_filmography WHERE total_movies_directed > 0 ORDER BY avg_movie_rating DESC LIMIT 20");
                    echo json_encode([
                        'success' => true,
                        'view' => 'v_celebrity_filmography',
                        'description' => 'Celebrity filmography summaries',
                        'count' => count($result),
                        'data' => $result
                    ]);
                }
                break;
                
            case 'recent_reviews':
                // Query: Recent Reviews View
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
                $result = $db->fetchAll("SELECT * FROM v_recent_reviews ORDER BY created_at DESC LIMIT $limit");
                echo json_encode([
                    'success' => true,
                    'view' => 'v_recent_reviews',
                    'description' => 'Recent reviews with user and content details',
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Unknown action. Use ?action=list to see available views'
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Only GET method is supported'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
