<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    if ($method === 'GET') {
        $operation = $_GET['operation'] ?? 'list';
        
        switch ($operation) {
            case 'list':
                // List all available set operations
                echo json_encode([
                    'success' => true,
                    'message' => 'Available set operations (INTERSECT and MINUS simulations)',
                    'operations' => [
                        'intersect_high_rated' => 'Movies that are both high-rated AND in specific genre',
                        'intersect_user_common' => 'Content in both user watchlists (common items)',
                        'minus_all_not_watched' => 'All movies MINUS watched ones (unwatched)',
                        'minus_watchlist_not_favorites' => 'Watchlist items NOT in favorites',
                        'intersect_genre_year' => 'Movies in specific genre AND year range',
                        'minus_movies_no_reviews' => 'Movies with NO reviews'
                    ]
                ]);
                break;
                
            // INTERSECT Operations (A AND B)
            case 'intersect_high_rated':
                // INTERSECT: Movies that are Action genre AND rating > 8.0
                $genre = $_GET['genre'] ?? 'Action';
                $minRating = (float)($_GET['min_rating'] ?? 8.0);
                
                $result = $db->fetchAll("
                    SELECT movie_id, title, genre, rating, release_date
                    FROM movies
                    WHERE genre LIKE ?
                    AND movie_id IN (
                        SELECT movie_id FROM movies WHERE rating >= ?
                    )
                    ORDER BY rating DESC
                ", ["%$genre%", $minRating]);
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'INTERSECT',
                    'description' => "Movies in genre '$genre' AND rating >= $minRating",
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'intersect_user_common':
                // INTERSECT: Content in both user watchlists
                $user1 = (int)($_GET['user1_id'] ?? 1);
                $user2 = (int)($_GET['user2_id'] ?? 2);
                
                $result = $db->fetchAll("
                    SELECT w1.content_type, w1.content_id,
                        CASE 
                            WHEN w1.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = w1.content_id)
                            ELSE (SELECT title FROM tv_series WHERE series_id = w1.content_id)
                        END as title
                    FROM watchlist w1
                    WHERE w1.user_id = ?
                    AND w1.content_id IN (
                        SELECT content_id FROM watchlist 
                        WHERE user_id = ? AND content_type = w1.content_type
                    )
                ", [$user1, $user2]);
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'INTERSECT',
                    'description' => "Content in both User $user1 and User $user2 watchlists",
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'intersect_genre_year':
                // INTERSECT: Movies in genre AND year range
                $genre = $_GET['genre'] ?? 'Drama';
                $startYear = (int)($_GET['start_year'] ?? 2010);
                $endYear = (int)($_GET['end_year'] ?? 2020);
                
                $result = $db->fetchAll("
                    SELECT movie_id, title, genre, rating, release_date
                    FROM movies
                    WHERE genre LIKE ?
                    AND movie_id IN (
                        SELECT movie_id FROM movies 
                        WHERE YEAR(release_date) BETWEEN ? AND ?
                    )
                    ORDER BY release_date DESC
                ", ["%$genre%", $startYear, $endYear]);
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'INTERSECT',
                    'description' => "Movies in genre '$genre' AND years $startYear-$endYear",
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            // MINUS Operations (A NOT IN B)
            case 'minus_all_not_watched':
                // MINUS: All movies MINUS watched movies for a user
                $userId = (int)($_GET['user_id'] ?? 1);
                
                $result = $db->fetchAll("
                    SELECT movie_id, title, genre, rating, release_date
                    FROM movies
                    WHERE movie_id NOT IN (
                        SELECT content_id FROM watchlist 
                        WHERE user_id = ? AND content_type = 'movie'
                    )
                    ORDER BY rating DESC
                    LIMIT 20
                ", [$userId]);
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'MINUS',
                    'description' => "All movies MINUS User $userId's watchlist (unwatched movies)",
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'minus_watchlist_not_favorites':
                // MINUS: User's watchlist MINUS favorites
                $userId = (int)($_GET['user_id'] ?? 1);
                
                $result = $db->fetchAll("
                    SELECT w.content_type, w.content_id,
                        CASE 
                            WHEN w.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = w.content_id)
                            ELSE (SELECT title FROM tv_series WHERE series_id = w.content_id)
                        END as title
                    FROM watchlist w
                    WHERE w.user_id = ?
                    AND w.content_id NOT IN (
                        SELECT content_id FROM favorites 
                        WHERE user_id = ? AND content_type = w.content_type
                    )
                ", [$userId, $userId]);
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'MINUS',
                    'description' => "User $userId's watchlist MINUS favorites (to-watch but not favorites)",
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            case 'minus_movies_no_reviews':
                // MINUS: All movies MINUS movies with reviews
                $result = $db->fetchAll("
                    SELECT movie_id, title, genre, rating, release_date
                    FROM movies
                    WHERE movie_id NOT IN (
                        SELECT DISTINCT content_id FROM reviews 
                        WHERE content_type = 'movie'
                    )
                    ORDER BY rating DESC
                    LIMIT 20
                ");
                
                echo json_encode([
                    'success' => true,
                    'operation' => 'MINUS',
                    'description' => 'Movies with NO reviews yet',
                    'count' => count($result),
                    'data' => $result
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'error' => 'Unknown operation. Use ?operation=list to see available operations'
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
