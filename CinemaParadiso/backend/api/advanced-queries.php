<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    // Demonstration of all SQL operations from all_sqls_list.txt
    
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
        
        switch ($operation) {
            case 'distinct':
                // SELECT DISTINCT example
                $result = $db->fetchAll("SELECT DISTINCT genre FROM movies");
                echo json_encode(['success' => true, 'operation' => 'DISTINCT', 'data' => $result]);
                break;
                
            case 'between':
                // BETWEEN example
                $result = $db->fetchAll("SELECT * FROM movies WHERE release_date BETWEEN '2000-01-01' AND '2020-12-31'");
                echo json_encode(['success' => true, 'operation' => 'BETWEEN', 'data' => $result]);
                break;
                
            case 'in':
                // IN example
                $result = $db->fetchAll("SELECT * FROM movies WHERE genre IN ('Action', 'Sci-Fi', 'Drama')");
                echo json_encode(['success' => true, 'operation' => 'IN', 'data' => $result]);
                break;
                
            case 'like':
                // LIKE example
                $result = $db->fetchAll("SELECT * FROM movies WHERE title LIKE '%the%'");
                echo json_encode(['success' => true, 'operation' => 'LIKE', 'data' => $result]);
                break;
                
            case 'aggregates':
                // COUNT, SUM, AVG, MIN, MAX
                $stats = [
                    'total_movies' => $db->fetchOne("SELECT COUNT(*) as count FROM movies")['count'],
                    'avg_rating' => $db->fetchOne("SELECT AVG(rating) as avg_rating FROM movies")['avg_rating'],
                    'max_rating' => $db->fetchOne("SELECT MAX(rating) as max_rating FROM movies")['max_rating'],
                    'min_duration' => $db->fetchOne("SELECT MIN(duration) as min_duration FROM movies")['min_duration'],
                    'total_ratings' => $db->fetchOne("SELECT SUM(total_ratings) as sum FROM movies")['sum']
                ];
                echo json_encode(['success' => true, 'operation' => 'AGGREGATES', 'data' => $stats]);
                break;
                
            case 'group_by':
                // GROUP BY example
                $result = $db->fetchAll("SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating 
                    FROM movies 
                    GROUP BY genre 
                    ORDER BY count DESC");
                echo json_encode(['success' => true, 'operation' => 'GROUP BY', 'data' => $result]);
                break;
                
            case 'having':
                // HAVING example
                $result = $db->fetchAll("SELECT genre, COUNT(*) as count 
                    FROM movies 
                    GROUP BY genre 
                    HAVING count > 1");
                echo json_encode(['success' => true, 'operation' => 'HAVING', 'data' => $result]);
                break;
                
            case 'inner_join':
                // INNER JOIN example
                $result = $db->fetchAll("SELECT m.title, c.name as director 
                    FROM movies m 
                    INNER JOIN celebrities c ON m.director_id = c.celebrity_id");
                echo json_encode(['success' => true, 'operation' => 'INNER JOIN', 'data' => $result]);
                break;
                
            case 'left_join':
                // LEFT JOIN example
                $result = $db->fetchAll("SELECT m.title, c.name as director 
                    FROM movies m 
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id");
                echo json_encode(['success' => true, 'operation' => 'LEFT JOIN', 'data' => $result]);
                break;
                
            case 'subquery_where':
                // Subquery in WHERE
                $result = $db->fetchAll("SELECT * FROM movies 
                    WHERE rating > (SELECT AVG(rating) FROM movies)");
                echo json_encode(['success' => true, 'operation' => 'SUBQUERY WHERE', 'data' => $result]);
                break;
                
            case 'subquery_select':
                // Subquery in SELECT
                $result = $db->fetchAll("SELECT title, rating, 
                    (SELECT AVG(rating) FROM movies) as avg_rating 
                    FROM movies");
                echo json_encode(['success' => true, 'operation' => 'SUBQUERY SELECT', 'data' => $result]);
                break;
                
            case 'union':
                // UNION example
                $result = $db->fetchAll("SELECT title, 'movie' as type FROM movies 
                    UNION 
                    SELECT title, 'series' as type FROM tv_series");
                echo json_encode(['success' => true, 'operation' => 'UNION', 'data' => $result]);
                break;
                
            case 'union_all':
                // UNION ALL example
                $result = $db->fetchAll("SELECT title FROM movies 
                    UNION ALL 
                    SELECT title FROM tv_series");
                echo json_encode(['success' => true, 'operation' => 'UNION ALL', 'data' => $result]);
                break;
                
            case 'cross_join':
                // CROSS JOIN example (limited)
                $result = $db->fetchAll("SELECT m.title, c.name 
                    FROM (SELECT * FROM movies LIMIT 2) m 
                    CROSS JOIN (SELECT * FROM celebrities LIMIT 2) c");
                echo json_encode(['success' => true, 'operation' => 'CROSS JOIN', 'data' => $result]);
                break;
                
            case 'self_join':
                // Self join example - users following each other
                $result = $db->fetchAll("SELECT u1.username as follower, u2.username as following 
                    FROM user_follows uf 
                    JOIN users u1 ON uf.follower_id = u1.user_id 
                    JOIN users u2 ON uf.following_id = u2.user_id");
                echo json_encode(['success' => true, 'operation' => 'SELF JOIN', 'data' => $result]);
                break;
                
            case 'order_by':
                // ORDER BY example
                $result = $db->fetchAll("SELECT title, rating FROM movies 
                    ORDER BY rating DESC, title ASC 
                    LIMIT 10");
                echo json_encode(['success' => true, 'operation' => 'ORDER BY', 'data' => $result]);
                break;
                
            case 'complex_query':
                // Complex query with multiple operations
                $result = $db->fetchAll("SELECT 
                    m.title,
                    m.rating,
                    c.name as director,
                    COUNT(mc.cast_id) as cast_count,
                    (SELECT AVG(rating) FROM movies) as avg_rating
                FROM movies m
                LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
                LEFT JOIN movie_cast mc ON m.movie_id = mc.movie_id
                WHERE m.rating > 8.0
                GROUP BY m.movie_id, m.title, m.rating, c.name
                HAVING cast_count > 0
                ORDER BY m.rating DESC
                LIMIT 10");
                echo json_encode(['success' => true, 'operation' => 'COMPLEX QUERY', 'data' => $result]);
                break;
                
            case 'top_rated_by_genre':
                // Top rated movies by genre
                $result = $db->fetchAll("SELECT genre, title, rating 
                    FROM movies m1 
                    WHERE rating = (
                        SELECT MAX(rating) 
                        FROM movies m2 
                        WHERE m1.genre = m2.genre
                    )");
                echo json_encode(['success' => true, 'operation' => 'TOP RATED BY GENRE', 'data' => $result]);
                break;
                
            case 'insert_select':
                // INSERT INTO SELECT example (creating a view-like query)
                $result = $db->fetchAll("SELECT m.title, m.rating, 'high_rated' as category 
                    FROM movies m 
                    WHERE m.rating > 8.5
                    UNION
                    SELECT m.title, m.rating, 'medium_rated' as category 
                    FROM movies m 
                    WHERE m.rating BETWEEN 7.0 AND 8.5");
                echo json_encode(['success' => true, 'operation' => 'INSERT SELECT PATTERN', 'data' => $result]);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Unknown operation']);
        }
    } else {
        // List all available operations
        $operations = [
            'distinct', 'between', 'in', 'like', 'aggregates', 'group_by', 'having',
            'inner_join', 'left_join', 'subquery_where', 'subquery_select', 
            'union', 'union_all', 'cross_join', 'self_join', 'order_by',
            'complex_query', 'top_rated_by_genre', 'insert_select'
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Available SQL operations',
            'operations' => $operations,
            'usage' => 'Add ?operation=<operation_name> to see examples'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
