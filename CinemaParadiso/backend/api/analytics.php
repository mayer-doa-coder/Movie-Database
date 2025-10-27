<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    // Advanced Analytics & User Operations
    // SQL operations work behind the scenes
    
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        switch ($action) {
            // ========== SEARCH & DISCOVERY ==========
            
            case 'search_content':
                // Search across movies and series (UNION behind the scenes)
                $keyword = $_GET['keyword'] ?? '';
                $searchTerm = "%$keyword%";
                $result = $db->fetchAll("
                    SELECT title, 'Movie' as type, rating, release_date as date, genre 
                    FROM movies 
                    WHERE title LIKE ?
                    UNION 
                    SELECT title, 'TV Series' as type, rating, first_air_date as date, genre 
                    FROM tv_series
                    WHERE title LIKE ?
                    ORDER BY rating DESC
                ", [$searchTerm, $searchTerm]);
                echo json_encode(['success' => true, 'action' => 'Search Results', 'data' => $result]);
                break;
            
            case 'find_similar':
                // Find similar content by genre (SELF JOIN behind scenes)
                $movieId = (int)($_GET['movie_id'] ?? 0);
                $result = $db->fetchAll("
                    SELECT m2.movie_id, m2.title, m2.rating, m2.genre, m2.release_date
                    FROM movies m1
                    JOIN movies m2 ON m1.genre = m2.genre AND m1.movie_id != m2.movie_id
                    WHERE m1.movie_id = ?
                    ORDER BY m2.rating DESC
                    LIMIT 10
                ", [$movieId]);
                echo json_encode(['success' => true, 'action' => 'Similar Content', 'data' => $result]);
                break;
            
            case 'discover_top_rated':
                // Discover top rated content (Subquery for above average)
                $minRating = (float)($_GET['min_rating'] ?? 8.0);
                $result = $db->fetchAll("
                    SELECT title, rating, genre, release_date,
                        (SELECT AVG(rating) FROM movies) as avg_rating
                    FROM movies 
                    WHERE rating >= ? AND rating > (SELECT AVG(rating) FROM movies)
                    ORDER BY rating DESC
                    LIMIT 20
                ", [$minRating]);
                echo json_encode(['success' => true, 'action' => 'Top Rated Discovery', 'data' => $result]);
                break;
            
            case 'browse_by_year':
                // Browse content by release year (GROUP BY behind scenes)
                $startYear = (int)($_GET['start_year'] ?? 2010);
                $endYear = (int)($_GET['end_year'] ?? 2024);
                $result = $db->fetchAll("
                    SELECT 
                        YEAR(release_date) as year,
                        COUNT(*) as total_movies,
                        AVG(rating) as avg_rating,
                        MAX(rating) as best_rating,
                        GROUP_CONCAT(DISTINCT genre ORDER BY genre SEPARATOR ', ') as genres
                    FROM movies
                    WHERE YEAR(release_date) BETWEEN ? AND ?
                    GROUP BY YEAR(release_date)
                    HAVING total_movies > 0
                    ORDER BY year DESC
                ", [$startYear, $endYear]);
                echo json_encode(['success' => true, 'action' => 'Browse by Year', 'data' => $result]);
                break;
            
            // ========== FILTERING & SORTING ==========
            
            case 'filter_by_genre':
                // Filter content by genre with sorting (WHERE + ORDER BY)
                $genre = $_GET['genre'] ?? '';
                $sortBy = $_GET['sort'] ?? 'rating';
                $order = $_GET['order'] ?? 'DESC';
                
                $allowedSort = ['rating', 'release_date', 'title'];
                $sortColumn = in_array($sortBy, $allowedSort) ? $sortBy : 'rating';
                $sortOrder = ($order === 'ASC') ? 'ASC' : 'DESC';
                
                $result = $db->fetchAll("
                    SELECT movie_id, title, rating, genre, release_date, duration
                    FROM movies
                    WHERE genre = ?
                    ORDER BY $sortColumn $sortOrder
                ", [$genre]);
                echo json_encode(['success' => true, 'action' => 'Genre Filter', 'data' => $result]);
                break;
            
            case 'filter_by_rating_range':
                // Filter by rating range (BETWEEN)
                $minRating = (float)($_GET['min'] ?? 0);
                $maxRating = (float)($_GET['max'] ?? 10);
                $result = $db->fetchAll("
                    SELECT title, rating, genre, release_date
                    FROM movies
                    WHERE rating BETWEEN ? AND ?
                    ORDER BY rating DESC
                ", [$minRating, $maxRating]);
                echo json_encode(['success' => true, 'action' => 'Rating Range Filter', 'data' => $result]);
                break;
            
            case 'filter_multiple_genres':
                // Filter by multiple genres (IN clause)
                $genres = $_GET['genres'] ?? 'Action,Drama';
                $genreArray = array_map('trim', explode(',', $genres));
                
                // Build IN clause with placeholders
                $placeholders = implode(',', array_fill(0, count($genreArray), '?'));
                
                // Build SQL query
                $sql = "
                    SELECT title, rating, genre, release_date
                    FROM movies
                    WHERE genre IN ($placeholders)
                    ORDER BY rating DESC
                ";
                
                // Use Database class query method with mysqli
                $conn = $db->getConnection();
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    // Bind parameters dynamically
                    $types = str_repeat('s', count($genreArray));
                    $stmt->bind_param($types, ...$genreArray);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    echo json_encode(['success' => true, 'action' => 'Multiple Genre Filter', 'data' => $result]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
                }
                break;
            
            // ========== ANALYTICS & STATISTICS ==========
            
            case 'genre_statistics':
                // Comprehensive genre stats (GROUP BY + Aggregates)
                $result = $db->fetchAll("
                    SELECT 
                        genre,
                        COUNT(*) as total_movies,
                        AVG(rating) as avg_rating,
                        MIN(rating) as min_rating,
                        MAX(rating) as max_rating,
                        SUM(CASE WHEN rating >= 8.0 THEN 1 ELSE 0 END) as highly_rated_count,
                        ROUND(AVG(duration), 0) as avg_duration
                    FROM movies
                    GROUP BY genre
                    HAVING total_movies > 0
                    ORDER BY avg_rating DESC
                ");
                echo json_encode(['success' => true, 'action' => 'Genre Analytics', 'data' => $result]);
                break;
            
            case 'director_performance':
                // Director performance analysis (JOIN + GROUP BY)
                $result = $db->fetchAll("
                    SELECT 
                        c.celebrity_id,
                        c.name as director_name,
                        c.nationality,
                        COUNT(m.movie_id) as total_movies,
                        AVG(m.rating) as avg_rating,
                        MAX(m.rating) as best_movie_rating,
                        GROUP_CONCAT(m.title ORDER BY m.rating DESC SEPARATOR ' | ') as movies
                    FROM celebrities c
                    INNER JOIN movies m ON c.celebrity_id = m.director_id
                    WHERE c.profession LIKE '%Director%'
                    GROUP BY c.celebrity_id, c.name, c.nationality
                    HAVING total_movies > 0
                    ORDER BY avg_rating DESC
                    LIMIT 20
                ");
                echo json_encode(['success' => true, 'action' => 'Director Performance', 'data' => $result]);
                break;
            
            case 'content_distribution':
                // Content distribution analysis (UNION + GROUP BY)
                $result = $db->fetchAll("
                    SELECT 
                        genre,
                        SUM(movie_count) as total_items,
                        AVG(avg_rating) as overall_avg_rating
                    FROM (
                        SELECT genre, COUNT(*) as movie_count, AVG(rating) as avg_rating
                        FROM movies
                        GROUP BY genre
                        UNION ALL
                        SELECT genre, COUNT(*) as series_count, AVG(rating) as avg_rating
                        FROM tv_series
                        GROUP BY genre
                    ) as content
                    GROUP BY genre
                    ORDER BY total_items DESC
                ");
                echo json_encode(['success' => true, 'action' => 'Content Distribution', 'data' => $result]);
                break;
            
            case 'rating_distribution':
                // Rating distribution with categories (CASE statement)
                $result = $db->fetchAll("
                    SELECT 
                        CASE 
                            WHEN rating >= 9.0 THEN 'Masterpiece (9.0+)'
                            WHEN rating >= 8.0 THEN 'Excellent (8.0-8.9)'
                            WHEN rating >= 7.0 THEN 'Good (7.0-7.9)'
                            WHEN rating >= 6.0 THEN 'Average (6.0-6.9)'
                            ELSE 'Below Average (<6.0)'
                        END as rating_category,
                        COUNT(*) as count,
                        AVG(rating) as avg_in_category
                    FROM movies
                    GROUP BY rating_category
                    ORDER BY avg_in_category DESC
                ");
                echo json_encode(['success' => true, 'action' => 'Rating Distribution', 'data' => $result]);
                break;
            
            // ========== COMPARISONS ==========
            
            case 'compare_genres':
                // Compare two genres (Subqueries + JOINs)
                $genre1 = $_GET['genre1'] ?? 'Action';
                $genre2 = $_GET['genre2'] ?? 'Drama';
                
                $stats1 = $db->fetchOne("
                    SELECT 
                        genre,
                        COUNT(*) as count,
                        AVG(rating) as avg_rating,
                        MAX(rating) as max_rating,
                        AVG(duration) as avg_duration
                    FROM movies
                    WHERE genre = ?
                    GROUP BY genre
                ", [$genre1]);
                
                $stats2 = $db->fetchOne("
                    SELECT 
                        genre,
                        COUNT(*) as count,
                        AVG(rating) as avg_rating,
                        MAX(rating) as max_rating,
                        AVG(duration) as avg_duration
                    FROM movies
                    WHERE genre = ?
                    GROUP BY genre
                ", [$genre2]);
                
                echo json_encode([
                    'success' => true, 
                    'action' => 'Genre Comparison',
                    'data' => [
                        'genre1' => $stats1 ?: ['genre' => $genre1, 'count' => 0],
                        'genre2' => $stats2 ?: ['genre' => $genre2, 'count' => 0]
                    ]
                ]);
                break;
            
            case 'movies_vs_series':
                // Compare movies vs TV series (UNION with stats)
                $movieStats = $db->fetchOne("
                    SELECT 
                        COUNT(*) as total,
                        AVG(rating) as avg_rating,
                        MAX(rating) as max_rating,
                        MIN(rating) as min_rating
                    FROM movies
                ");
                
                $seriesStats = $db->fetchOne("
                    SELECT 
                        COUNT(*) as total,
                        AVG(rating) as avg_rating,
                        MAX(rating) as max_rating,
                        MIN(rating) as min_rating
                    FROM tv_series
                ");
                
                echo json_encode([
                    'success' => true,
                    'action' => 'Movies vs Series Comparison',
                    'data' => [
                        'movies' => $movieStats,
                        'series' => $seriesStats
                    ]
                ]);
                break;
            
            // ========== RECOMMENDATIONS ==========
            
            case 'get_recommendations':
                // Personalized recommendations (Complex query with multiple JOINs)
                $userId = (int)($_GET['user_id'] ?? 1);
                $result = $db->fetchAll("
                    SELECT DISTINCT
                        m.movie_id,
                        m.title,
                        m.rating,
                        m.genre,
                        m.release_date,
                        (SELECT AVG(rating) FROM movies WHERE genre = m.genre) as genre_avg_rating
                    FROM movies m
                    WHERE m.rating >= 7.5
                    AND m.movie_id NOT IN (
                        SELECT content_id FROM watchlist WHERE user_id = ? AND content_type = 'movie'
                    )
                    ORDER BY m.rating DESC, genre_avg_rating DESC
                    LIMIT 15
                ", [$userId]);
                echo json_encode(['success' => true, 'action' => 'Recommendations', 'data' => $result]);
                break;
            
            case 'trending_now':
                // Get trending content (Recent + highly rated - CTEs)
                $result = $db->fetchAll("
                    WITH RecentContent AS (
                        SELECT title, rating, genre, release_date, 'Movie' as type
                        FROM movies
                        WHERE release_date >= DATE_SUB(NOW(), INTERVAL 2 YEAR)
                        UNION ALL
                        SELECT title, rating, genre, first_air_date as release_date, 'Series' as type
                        FROM tv_series
                        WHERE first_air_date >= DATE_SUB(NOW(), INTERVAL 2 YEAR)
                    )
                    SELECT * FROM RecentContent
                    WHERE rating >= 7.0
                    ORDER BY rating DESC, release_date DESC
                    LIMIT 20
                ");
                echo json_encode(['success' => true, 'action' => 'Trending Now', 'data' => $result]);
                break;
            
            case 'hidden_gems':
                // Find hidden gems (Good ratings, less popular)
                $result = $db->fetchAll("
                    SELECT 
                        m.title,
                        m.rating,
                        m.genre,
                        m.release_date,
                        COALESCE(m.total_ratings, 0) as popularity
                    FROM movies m
                    WHERE m.rating >= 7.5
                    AND m.total_ratings < (SELECT AVG(total_ratings) FROM movies WHERE total_ratings > 0)
                    ORDER BY m.rating DESC
                    LIMIT 15
                ");
                echo json_encode(['success' => true, 'action' => 'Hidden Gems', 'data' => $result]);
                break;
            
            // ========== CAST & CREW ==========
            
            case 'movie_with_cast':
                // Get movie with full cast (Multiple JOINs)
                $movieId = (int)($_GET['movie_id'] ?? 1);
                $movie = $db->fetchOne("
                    SELECT m.*, c.name as director_name
                    FROM movies m
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
                    WHERE m.movie_id = ?
                ", [$movieId]);
                
                $cast = $db->fetchAll("
                    SELECT 
                        ce.celebrity_id,
                        ce.name,
                        ce.profile_image,
                        mc.role,
                        mc.cast_type
                    FROM movie_cast mc
                    INNER JOIN celebrities ce ON mc.celebrity_id = ce.celebrity_id
                    WHERE mc.movie_id = ?
                    ORDER BY mc.cast_order
                ", [$movieId]);
                
                echo json_encode([
                    'success' => true,
                    'action' => 'Movie Details with Cast',
                    'data' => [
                        'movie' => $movie,
                        'cast' => $cast
                    ]
                ]);
                break;
            
            case 'celebrity_filmography':
                // Get celebrity's complete work (LEFT JOIN to show all work)
                $celebId = (int)($_GET['celebrity_id'] ?? 1);
                
                $celebrity = $db->fetchOne("
                    SELECT * FROM celebrities WHERE celebrity_id = ?
                ", [$celebId]);
                
                $asDirector = $db->fetchAll("
                    SELECT title, rating, release_date, 'Director' as role
                    FROM movies
                    WHERE director_id = ?
                    ORDER BY release_date DESC
                ", [$celebId]);
                
                $asActor = $db->fetchAll("
                    SELECT m.title, m.rating, m.release_date, mc.role, 'Actor' as role_type
                    FROM movie_cast mc
                    INNER JOIN movies m ON mc.movie_id = m.movie_id
                    WHERE mc.celebrity_id = ?
                    ORDER BY m.release_date DESC
                ", [$celebId]);
                
                echo json_encode([
                    'success' => true,
                    'action' => 'Celebrity Filmography',
                    'data' => [
                        'celebrity' => $celebrity,
                        'as_director' => $asDirector,
                        'as_actor' => $asActor
                    ]
                ]);
                break;
            
            // ========== TIMELINE & TRENDS ==========
            
            case 'release_timeline':
                // Timeline analysis (GROUP BY with date functions)
                $result = $db->fetchAll("
                    SELECT 
                        YEAR(release_date) as year,
                        MONTH(release_date) as month,
                        COUNT(*) as releases,
                        AVG(rating) as avg_rating
                    FROM movies
                    WHERE release_date IS NOT NULL
                    AND YEAR(release_date) >= 2015
                    GROUP BY YEAR(release_date), MONTH(release_date)
                    ORDER BY year DESC, month DESC
                ");
                echo json_encode(['success' => true, 'action' => 'Release Timeline', 'data' => $result]);
                break;
            
            case 'decade_analysis':
                // Analyze by decade (Complex GROUP BY)
                $result = $db->fetchAll("
                    SELECT 
                        CONCAT(FLOOR(YEAR(release_date)/10)*10, 's') as decade,
                        COUNT(*) as total_movies,
                        AVG(rating) as avg_rating,
                        MAX(rating) as best_rating,
                        COUNT(DISTINCT genre) as genre_diversity
                    FROM movies
                    WHERE release_date IS NOT NULL
                    GROUP BY FLOOR(YEAR(release_date)/10)
                    ORDER BY decade DESC
                ");
                echo json_encode(['success' => true, 'action' => 'Decade Analysis', 'data' => $result]);
                break;
            
            // ========== USER INSIGHTS ==========
            
            case 'user_watchlist_analysis':
                // Analyze user's watchlist (Subqueries + JOINs)
                $userId = (int)($_GET['user_id'] ?? 1);
                $result = $db->fetchAll("
                    SELECT 
                        m.genre,
                        COUNT(*) as movies_in_watchlist,
                        AVG(m.rating) as avg_rating_in_watchlist
                    FROM watchlist w
                    INNER JOIN movies m ON w.content_id = m.movie_id
                    WHERE w.user_id = ? AND w.content_type = 'movie'
                    GROUP BY m.genre
                    ORDER BY movies_in_watchlist DESC
                ", [$userId]);
                echo json_encode(['success' => true, 'action' => 'Watchlist Analysis', 'data' => $result]);
                break;
            
            // ========== DATA QUALITY ==========
            
            case 'content_completeness':
                // Check data completeness (NULL handling + CASE)
                $result = $db->fetchAll("
                    SELECT 
                        'Movies' as content_type,
                        COUNT(*) as total,
                        SUM(CASE WHEN director_id IS NULL THEN 1 ELSE 0 END) as missing_director,
                        SUM(CASE WHEN plot_summary IS NULL OR plot_summary = '' THEN 1 ELSE 0 END) as missing_plot,
                        SUM(CASE WHEN poster_url IS NULL OR poster_url = '' THEN 1 ELSE 0 END) as missing_poster
                    FROM movies
                    UNION ALL
                    SELECT 
                        'TV Series' as content_type,
                        COUNT(*) as total,
                        SUM(CASE WHEN creator_id IS NULL THEN 1 ELSE 0 END) as missing_creator,
                        SUM(CASE WHEN plot_summary IS NULL OR plot_summary = '' THEN 1 ELSE 0 END) as missing_plot,
                        SUM(CASE WHEN poster_url IS NULL OR poster_url = '' THEN 1 ELSE 0 END) as missing_poster
                    FROM tv_series
                ");
                echo json_encode(['success' => true, 'action' => 'Content Completeness Check', 'data' => $result]);
                break;
            
            default:
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
        }
    } else {
        // List all available actions
        $actions = [
            'search_discovery' => [
                'search_content' => 'Search across all content',
                'find_similar' => 'Find similar movies/series',
                'discover_top_rated' => 'Discover highly rated content',
                'browse_by_year' => 'Browse by release year'
            ],
            'filtering_sorting' => [
                'filter_by_genre' => 'Filter by genre with sorting',
                'filter_by_rating_range' => 'Filter by rating range',
                'filter_multiple_genres' => 'Filter by multiple genres'
            ],
            'analytics' => [
                'genre_statistics' => 'Comprehensive genre statistics',
                'director_performance' => 'Director performance analysis',
                'content_distribution' => 'Content distribution analysis',
                'rating_distribution' => 'Rating distribution breakdown'
            ],
            'comparisons' => [
                'compare_genres' => 'Compare two genres',
                'movies_vs_series' => 'Movies vs TV Series comparison'
            ],
            'recommendations' => [
                'get_recommendations' => 'Personalized recommendations',
                'trending_now' => 'Trending content',
                'hidden_gems' => 'Discover hidden gems'
            ],
            'cast_crew' => [
                'movie_with_cast' => 'Movie details with full cast',
                'celebrity_filmography' => 'Celebrity complete filmography'
            ],
            'timeline' => [
                'release_timeline' => 'Release timeline analysis',
                'decade_analysis' => 'Analyze content by decade'
            ],
            'insights' => [
                'user_watchlist_analysis' => 'User watchlist analysis',
                'content_completeness' => 'Data completeness check'
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Cinema Paradiso Analytics & Operations',
            'total_actions' => array_sum(array_map('count', $actions)),
            'categories' => $actions,
            'usage' => 'Add ?action=<action_name> with required parameters'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>