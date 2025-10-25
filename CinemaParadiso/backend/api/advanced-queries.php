<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../classes/SQLLogger.php';
require_once __DIR__ . '/../classes/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

try {
    // Advanced SQL Operations - Operational implementation with all SQL queries
    
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
        
        switch ($operation) {
            // ========== SELECT OPERATIONS ==========
            
            case 'distinct':
                // SELECT DISTINCT - Get unique genres from movies
                $result = $db->fetchAll("SELECT DISTINCT genre FROM movies ORDER BY genre");
                echo json_encode(['success' => true, 'operation' => 'DISTINCT - Unique Genres', 'data' => $result]);
                break;
                
            case 'distinct_all':
                // SELECT ALL vs DISTINCT comparison
                $all = $db->fetchAll("SELECT genre FROM movies");
                $distinct = $db->fetchAll("SELECT DISTINCT genre FROM movies");
                echo json_encode([
                    'success' => true, 
                    'operation' => 'ALL vs DISTINCT', 
                    'data' => [
                        'all_count' => count($all),
                        'distinct_count' => count($distinct),
                        'distinct_genres' => $distinct
                    ]
                ]);
                break;
            
            case 'aliases':
                // AS - Column aliases
                $result = $db->fetchAll("SELECT 
                    title AS movie_name, 
                    rating AS score, 
                    release_date AS premiere_date,
                    duration AS runtime_minutes
                    FROM movies LIMIT 10");
                echo json_encode(['success' => true, 'operation' => 'ALIASES (AS)', 'data' => $result]);
                break;
                
            // ========== WHERE CLAUSE OPERATIONS ==========
                
            case 'between':
                // BETWEEN - Movies released in a specific range
                $result = $db->fetchAll("SELECT title, release_date, rating 
                    FROM movies 
                    WHERE release_date BETWEEN '2010-01-01' AND '2020-12-31' 
                    ORDER BY release_date DESC");
                echo json_encode(['success' => true, 'operation' => 'BETWEEN (2010-2020)', 'data' => $result]);
                break;
                
            case 'in':
                // IN - Movies in specific genres
                $result = $db->fetchAll("SELECT title, genre, rating 
                    FROM movies 
                    WHERE genre IN ('Action', 'Sci-Fi', 'Drama', 'Thriller') 
                    ORDER BY genre, rating DESC");
                echo json_encode(['success' => true, 'operation' => 'IN Clause', 'data' => $result]);
                break;
                
            case 'like':
                // LIKE - Pattern matching in titles
                $result = $db->fetchAll("SELECT title, genre, rating 
                    FROM movies 
                    WHERE title LIKE '%the%' 
                    ORDER BY rating DESC");
                echo json_encode(['success' => true, 'operation' => 'LIKE Pattern (%the%)', 'data' => $result]);
                break;
            
            case 'not_null':
                // NOT NULL - Movies with directors assigned
                $result = $db->fetchAll("SELECT m.title, c.name as director, m.rating 
                    FROM movies m 
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id 
                    WHERE m.director_id IS NOT NULL 
                    ORDER BY m.rating DESC LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'NOT NULL (Has Director)', 'data' => $result]);
                break;
            
            // ========== AGGREGATE FUNCTIONS ==========
                
            case 'aggregates':
                // COUNT, SUM, AVG, MIN, MAX
                $movie_stats = $db->fetchOne("SELECT 
                    COUNT(*) as total_movies,
                    AVG(rating) as avg_rating,
                    MAX(rating) as max_rating,
                    MIN(rating) as min_rating,
                    AVG(duration) as avg_duration,
                    MAX(duration) as longest_movie,
                    MIN(duration) as shortest_movie
                    FROM movies");
                
                $series_stats = $db->fetchOne("SELECT 
                    COUNT(*) as total_series,
                    AVG(rating) as avg_rating,
                    SUM(total_seasons) as total_seasons_all
                    FROM tv_series");
                
                echo json_encode([
                    'success' => true, 
                    'operation' => 'AGGREGATE Functions', 
                    'data' => [
                        'movies' => $movie_stats,
                        'tv_series' => $series_stats
                    ]
                ]);
                break;
            
            case 'count':
                // COUNT - Count items by category
                $result = $db->fetchAll("SELECT 
                    genre, 
                    COUNT(*) as movie_count,
                    COUNT(DISTINCT director_id) as unique_directors
                    FROM movies 
                    GROUP BY genre 
                    ORDER BY movie_count DESC");
                echo json_encode(['success' => true, 'operation' => 'COUNT by Genre', 'data' => $result]);
                break;
            
            // ========== GROUP BY & HAVING ==========
                
            case 'group_by':
                // GROUP BY - Statistics by genre
                $result = $db->fetchAll("SELECT 
                    genre, 
                    COUNT(*) as count, 
                    AVG(rating) as avg_rating,
                    MAX(rating) as best_rated,
                    MIN(rating) as lowest_rated
                    FROM movies 
                    GROUP BY genre 
                    ORDER BY avg_rating DESC");
                echo json_encode(['success' => true, 'operation' => 'GROUP BY Genre', 'data' => $result]);
                break;
                
            case 'having':
                // HAVING - Genres with multiple movies
                $result = $db->fetchAll("SELECT 
                    genre, 
                    COUNT(*) as count,
                    AVG(rating) as avg_rating
                    FROM movies 
                    GROUP BY genre 
                    HAVING count >= 2
                    ORDER BY count DESC");
                echo json_encode(['success' => true, 'operation' => 'HAVING (count >= 2)', 'data' => $result]);
                break;
            
            // ========== ORDER BY ==========
            
            case 'order_by':
                // ORDER BY - Multiple column sorting
                $result = $db->fetchAll("SELECT title, genre, rating, release_date 
                    FROM movies 
                    ORDER BY rating DESC, release_date DESC 
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'ORDER BY rating DESC, date DESC', 'data' => $result]);
                break;
            
            case 'order_asc_desc':
                // ORDER BY ASC and DESC
                $highest = $db->fetchAll("SELECT title, rating FROM movies ORDER BY rating DESC LIMIT 5");
                $lowest = $db->fetchAll("SELECT title, rating FROM movies ORDER BY rating ASC LIMIT 5");
                echo json_encode([
                    'success' => true, 
                    'operation' => 'ORDER BY ASC vs DESC', 
                    'data' => ['highest_rated' => $highest, 'lowest_rated' => $lowest]
                ]);
                break;
            
            // ========== JOINS ==========
                
            case 'inner_join':
                // INNER JOIN - Movies with their directors
                $result = $db->fetchAll("SELECT 
                    m.title, 
                    m.rating,
                    c.name as director,
                    c.nationality
                    FROM movies m 
                    INNER JOIN celebrities c ON m.director_id = c.celebrity_id
                    ORDER BY m.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'INNER JOIN (Movies + Directors)', 'data' => $result]);
                break;
                
            case 'left_join':
                // LEFT OUTER JOIN - All movies, with or without directors
                $result = $db->fetchAll("SELECT 
                    m.title, 
                    m.rating,
                    c.name as director,
                    CASE WHEN c.celebrity_id IS NULL THEN 'No Director' ELSE 'Has Director' END as status
                    FROM movies m 
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
                    ORDER BY c.celebrity_id IS NULL DESC, m.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'LEFT JOIN (All Movies)', 'data' => $result]);
                break;
            
            case 'right_join':
                // RIGHT OUTER JOIN - All celebrities, with or without movies
                $result = $db->fetchAll("SELECT 
                    c.name as celebrity,
                    c.profession,
                    m.title,
                    CASE WHEN m.movie_id IS NULL THEN 'No Movie' ELSE 'Has Movie' END as status
                    FROM movies m 
                    RIGHT JOIN celebrities c ON m.director_id = c.celebrity_id
                    ORDER BY m.movie_id IS NULL DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'RIGHT JOIN (All Celebrities)', 'data' => $result]);
                break;
            
            case 'cross_join':
                // CROSS JOIN - Cartesian product (limited for safety)
                $result = $db->fetchAll("SELECT 
                    m.title as movie, 
                    c.name as celebrity
                    FROM (SELECT * FROM movies ORDER BY rating DESC LIMIT 3) m 
                    CROSS JOIN (SELECT * FROM celebrities LIMIT 3) c");
                echo json_encode(['success' => true, 'operation' => 'CROSS JOIN (Limited)', 'data' => $result]);
                break;
            
            case 'self_join':
                // Self JOIN - Movies in same genre
                $result = $db->fetchAll("SELECT DISTINCT
                    m1.title as movie1,
                    m2.title as movie2,
                    m1.genre as shared_genre,
                    m1.rating as rating1,
                    m2.rating as rating2
                    FROM movies m1
                    JOIN movies m2 ON m1.genre = m2.genre AND m1.movie_id < m2.movie_id
                    WHERE m1.rating > 8.0 AND m2.rating > 8.0
                    ORDER BY m1.genre, m1.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'SELF JOIN (Same Genre)', 'data' => $result]);
                break;
            
            case 'natural_join':
                // NATURAL JOIN simulation with USING clause
                $result = $db->fetchAll("SELECT 
                    m.title,
                    COUNT(mc.celebrity_id) as cast_count
                    FROM movies m
                    LEFT JOIN movie_cast mc USING(movie_id)
                    GROUP BY m.movie_id, m.title
                    HAVING cast_count > 0
                    ORDER BY cast_count DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'USING Clause (Movie Cast)', 'data' => $result]);
                break;
            
            case 'multiple_joins':
                // Multiple JOINs - Movies with director and cast
                $result = $db->fetchAll("SELECT 
                    m.title,
                    m.rating,
                    d.name as director,
                    COUNT(DISTINCT mc.celebrity_id) as cast_count,
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as cast_members
                    FROM movies m
                    LEFT JOIN celebrities d ON m.director_id = d.celebrity_id
                    LEFT JOIN movie_cast mc ON m.movie_id = mc.movie_id
                    LEFT JOIN celebrities c ON mc.celebrity_id = c.celebrity_id
                    GROUP BY m.movie_id, m.title, m.rating, d.name
                    ORDER BY m.rating DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'Multiple JOINs', 'data' => $result]);
                break;
            
            // ========== SUBQUERIES ==========
                
            case 'subquery_where':
                // Subquery in WHERE - Above average rating
                $result = $db->fetchAll("SELECT title, rating, genre 
                    FROM movies 
                    WHERE rating > (SELECT AVG(rating) FROM movies)
                    ORDER BY rating DESC");
                echo json_encode(['success' => true, 'operation' => 'Subquery WHERE (Above Avg)', 'data' => $result]);
                break;
                
            case 'subquery_select':
                // Subquery in SELECT - Compare to average
                $result = $db->fetchAll("SELECT 
                    title, 
                    rating,
                    (SELECT AVG(rating) FROM movies) as avg_rating,
                    ROUND(rating - (SELECT AVG(rating) FROM movies), 2) as rating_diff
                    FROM movies
                    ORDER BY rating_diff DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'Subquery SELECT', 'data' => $result]);
                break;
            
            case 'subquery_from':
                // Subquery in FROM - Derived table
                $result = $db->fetchAll("SELECT 
                    genre_stats.genre,
                    genre_stats.avg_rating,
                    genre_stats.movie_count
                    FROM (
                        SELECT genre, AVG(rating) as avg_rating, COUNT(*) as movie_count
                        FROM movies
                        GROUP BY genre
                    ) as genre_stats
                    WHERE genre_stats.movie_count >= 2
                    ORDER BY genre_stats.avg_rating DESC");
                echo json_encode(['success' => true, 'operation' => 'Subquery FROM (Derived Table)', 'data' => $result]);
                break;
            
            // ========== SET OPERATIONS ==========
                
            case 'union':
                // UNION - Combine movies and series (removes duplicates)
                $result = $db->fetchAll("SELECT title, 'Movie' as type, rating, release_date as date FROM movies 
                    UNION 
                    SELECT title, 'TV Series' as type, rating, first_air_date as date FROM tv_series
                    ORDER BY rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'UNION (Movies + Series)', 'data' => $result]);
                break;
                
            case 'union_all':
                // UNION ALL - Keep all records including duplicates
                $result = $db->fetchAll("SELECT title, genre, 'Movie' as source FROM movies 
                    UNION ALL 
                    SELECT title, genre, 'Series' as source FROM tv_series
                    ORDER BY title
                    LIMIT 30");
                echo json_encode(['success' => true, 'operation' => 'UNION ALL (With Duplicates)', 'data' => $result]);
                break;
            
            // ========== COMMON TABLE EXPRESSION (CTE) ==========
            
            case 'cte':
                // WITH CTE - Common Table Expression
                $result = $db->fetchAll("WITH HighRated AS (
                        SELECT title, rating, genre FROM movies WHERE rating >= 8.0
                    ),
                    GenreStats AS (
                        SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating
                        FROM HighRated
                        GROUP BY genre
                    )
                    SELECT * FROM GenreStats ORDER BY avg_rating DESC");
                echo json_encode(['success' => true, 'operation' => 'CTE (WITH Clause)', 'data' => $result]);
                break;
            
            case 'cte_multiple':
                // Multiple CTEs
                $result = $db->fetchAll("WITH 
                    MovieStats AS (
                        SELECT genre, AVG(rating) as avg_rating, COUNT(*) as count
                        FROM movies GROUP BY genre
                    ),
                    SeriesStats AS (
                        SELECT genre, AVG(rating) as avg_rating, COUNT(*) as count
                        FROM tv_series GROUP BY genre
                    )
                    SELECT 
                        COALESCE(m.genre, s.genre) as genre,
                        m.count as movies_count,
                        m.avg_rating as movies_avg,
                        s.count as series_count,
                        s.avg_rating as series_avg
                    FROM MovieStats m
                    LEFT JOIN SeriesStats s ON m.genre = s.genre
                    ORDER BY genre");
                echo json_encode(['success' => true, 'operation' => 'Multiple CTEs', 'data' => $result]);
                break;
            
            // ========== COMPLEX QUERIES ==========
                
            case 'complex_query':
                // Complex query with SELECT, FROM, WHERE, GROUP BY, HAVING, ORDER BY
                $result = $db->fetchAll("SELECT 
                    m.genre,
                    COUNT(DISTINCT m.movie_id) as movie_count,
                    AVG(m.rating) as avg_rating,
                    MAX(m.rating) as best_rating,
                    COUNT(DISTINCT m.director_id) as director_count,
                    GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') as directors
                FROM movies m
                LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
                WHERE m.rating >= 7.0
                GROUP BY m.genre
                HAVING movie_count >= 2
                ORDER BY avg_rating DESC, movie_count DESC");
                echo json_encode(['success' => true, 'operation' => 'Complex Query (Full SQL)', 'data' => $result]);
                break;
                
            case 'top_rated_by_genre':
                // Correlated subquery - Top rated movie per genre
                $result = $db->fetchAll("SELECT 
                    m1.genre, 
                    m1.title, 
                    m1.rating,
                    m1.release_date
                    FROM movies m1 
                    WHERE m1.rating = (
                        SELECT MAX(m2.rating) 
                        FROM movies m2 
                        WHERE m1.genre = m2.genre
                    )
                    ORDER BY m1.genre, m1.rating DESC");
                echo json_encode(['success' => true, 'operation' => 'Top Rated by Genre (Correlated Subquery)', 'data' => $result]);
                break;
            
            case 'celebrity_statistics':
                // Celebrity work statistics
                $result = $db->fetchAll("SELECT 
                    c.name,
                    c.profession,
                    COUNT(DISTINCT m.movie_id) as movies_directed,
                    AVG(m.rating) as avg_movie_rating,
                    MAX(m.rating) as best_movie_rating,
                    GROUP_CONCAT(m.title ORDER BY m.rating DESC SEPARATOR ' | ') as movies
                    FROM celebrities c
                    LEFT JOIN movies m ON c.celebrity_id = m.director_id
                    WHERE c.profession LIKE '%Director%'
                    GROUP BY c.celebrity_id, c.name, c.profession
                    HAVING movies_directed > 0
                    ORDER BY avg_movie_rating DESC, movies_directed DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'Celebrity Statistics', 'data' => $result]);
                break;
            
            case 'rating_categories':
                // Categorize movies by rating using CASE
                $result = $db->fetchAll("SELECT 
                    title,
                    rating,
                    CASE 
                        WHEN rating >= 9.0 THEN 'Masterpiece'
                        WHEN rating >= 8.0 THEN 'Excellent'
                        WHEN rating >= 7.0 THEN 'Good'
                        WHEN rating >= 6.0 THEN 'Average'
                        ELSE 'Below Average'
                    END as rating_category,
                    genre,
                    release_date
                    FROM movies
                    ORDER BY rating DESC
                    LIMIT 25");
                echo json_encode(['success' => true, 'operation' => 'Rating Categories (CASE)', 'data' => $result]);
                break;
            
            case 'genre_comparison':
                // Compare genres with all statistics
                $result = $db->fetchAll("SELECT 
                    genre,
                    COUNT(*) as total_items,
                    AVG(rating) as avg_rating,
                    MIN(rating) as min_rating,
                    MAX(rating) as max_rating,
                    ROUND(AVG(rating), 2) as rounded_avg,
                    CASE 
                        WHEN AVG(rating) >= 8.0 THEN 'Premium'
                        WHEN AVG(rating) >= 7.0 THEN 'Quality'
                        ELSE 'Standard'
                    END as quality_tier
                    FROM (
                        SELECT genre, rating FROM movies
                        UNION ALL
                        SELECT genre, rating FROM tv_series
                    ) as all_content
                    GROUP BY genre
                    HAVING total_items >= 2
                    ORDER BY avg_rating DESC");
                echo json_encode(['success' => true, 'operation' => 'Genre Comparison', 'data' => $result]);
                break;
            
            case 'temporal_analysis':
                // Temporal analysis with date functions
                $result = $db->fetchAll("SELECT 
                    YEAR(release_date) as release_year,
                    COUNT(*) as movies_released,
                    AVG(rating) as avg_rating,
                    MAX(rating) as best_rating,
                    GROUP_CONCAT(title ORDER BY rating DESC SEPARATOR ' | ') as top_movies
                    FROM movies
                    WHERE release_date IS NOT NULL
                    GROUP BY YEAR(release_date)
                    HAVING movies_released >= 1
                    ORDER BY release_year DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'Temporal Analysis (Year)', 'data' => $result]);
                break;
            
            case 'insert_select_demo':
                // INSERT SELECT pattern demonstration (read-only version)
                $result = $db->fetchAll("SELECT 
                    m.title, 
                    m.rating, 
                    m.genre,
                    CASE 
                        WHEN m.rating >= 8.5 THEN 'Hall of Fame'
                        WHEN m.rating >= 7.5 THEN 'Highly Recommended'
                        WHEN m.rating >= 6.5 THEN 'Recommended'
                        ELSE 'Standard'
                    END as recommendation_level
                    FROM movies m 
                    ORDER BY m.rating DESC");
                echo json_encode(['success' => true, 'operation' => 'INSERT SELECT Pattern (Demo)', 'data' => $result]);
                break;
            
            case 'null_handling':
                // NULL handling with COALESCE/IFNULL
                $result = $db->fetchAll("SELECT 
                    m.title,
                    m.rating,
                    COALESCE(c.name, 'Unknown Director') as director,
                    COALESCE(m.duration, 0) as duration,
                    IFNULL(m.plot_summary, 'No summary available') as summary_status
                    FROM movies m
                    LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
                    ORDER BY m.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'NULL Handling (COALESCE/IFNULL)', 'data' => $result]);
                break;
            
            case 'string_functions':
                // String manipulation functions
                $result = $db->fetchAll("SELECT 
                    title,
                    UPPER(title) as uppercase_title,
                    LOWER(genre) as lowercase_genre,
                    LENGTH(title) as title_length,
                    CONCAT(title, ' (', genre, ')') as full_display,
                    SUBSTRING(title, 1, 20) as short_title
                    FROM movies
                    ORDER BY title_length DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'String Functions', 'data' => $result]);
                break;
            
            case 'mathematical_operations':
                // Mathematical operations
                $result = $db->fetchAll("SELECT 
                    title,
                    rating,
                    ROUND(rating, 1) as rounded_rating,
                    CEIL(rating) as ceiling,
                    FLOOR(rating) as floor,
                    duration,
                    ROUND(duration / 60, 2) as duration_hours,
                    MOD(duration, 60) as remaining_minutes
                    FROM movies
                    WHERE duration IS NOT NULL
                    ORDER BY rating DESC
                    LIMIT 15");
                echo json_encode(['success' => true, 'operation' => 'Mathematical Operations', 'data' => $result]);
                break;
            
            case 'exists_subquery':
                // EXISTS - Find movies with cast members
                $result = $db->fetchAll("SELECT 
                    m.title,
                    m.rating,
                    m.genre
                    FROM movies m
                    WHERE EXISTS (
                        SELECT 1 FROM movie_cast mc 
                        WHERE mc.movie_id = m.movie_id
                    )
                    ORDER BY m.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'EXISTS Subquery', 'data' => $result]);
                break;
            
            case 'not_exists_subquery':
                // NOT EXISTS - Find movies without cast
                $result = $db->fetchAll("SELECT 
                    m.title,
                    m.rating,
                    m.genre
                    FROM movies m
                    WHERE NOT EXISTS (
                        SELECT 1 FROM movie_cast mc 
                        WHERE mc.movie_id = m.movie_id
                    )
                    ORDER BY m.rating DESC
                    LIMIT 20");
                echo json_encode(['success' => true, 'operation' => 'NOT EXISTS Subquery', 'data' => $result]);
                break;
            
            default:
                echo json_encode(['success' => false, 'error' => 'Unknown operation']);
        }
    } else {
        // List all available operations
        $operations = [
            // SELECT Operations
            'distinct' => 'Unique genres from movies',
            'distinct_all' => 'ALL vs DISTINCT comparison',
            'aliases' => 'Column aliases using AS',
            
            // WHERE Clause
            'between' => 'Movies in date range',
            'in' => 'Movies in specific genres',
            'like' => 'Pattern matching in titles',
            'not_null' => 'Movies with directors',
            
            // Aggregates
            'aggregates' => 'COUNT, SUM, AVG, MIN, MAX',
            'count' => 'Count by category',
            
            // Grouping
            'group_by' => 'Statistics by genre',
            'having' => 'Filter grouped results',
            
            // Sorting
            'order_by' => 'Multi-column sorting',
            'order_asc_desc' => 'Ascending vs Descending',
            
            // Joins
            'inner_join' => 'Movies with directors',
            'left_join' => 'All movies (with/without directors)',
            'right_join' => 'All celebrities',
            'cross_join' => 'Cartesian product',
            'self_join' => 'Movies in same genre',
            'natural_join' => 'USING clause demo',
            'multiple_joins' => 'Movies with director and cast',
            
            // Subqueries
            'subquery_where' => 'Above average ratings',
            'subquery_select' => 'Compare to average',
            'subquery_from' => 'Derived tables',
            'exists_subquery' => 'Movies with cast',
            'not_exists_subquery' => 'Movies without cast',
            
            // Set Operations
            'union' => 'Combine movies and series',
            'union_all' => 'Combine with duplicates',
            
            // CTE
            'cte' => 'Common Table Expression',
            'cte_multiple' => 'Multiple CTEs',
            
            // Complex Queries
            'complex_query' => 'Full SQL syntax demo',
            'top_rated_by_genre' => 'Correlated subquery',
            'celebrity_statistics' => 'Director statistics',
            'rating_categories' => 'CASE statement',
            'genre_comparison' => 'Cross-content analysis',
            'temporal_analysis' => 'Year-based analysis',
            'insert_select_demo' => 'INSERT SELECT pattern',
            'null_handling' => 'COALESCE/IFNULL',
            'string_functions' => 'String manipulation',
            'mathematical_operations' => 'Math functions',
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Advanced SQL Operations - All queries operational',
            'total_operations' => count($operations),
            'operations' => $operations,
            'usage' => 'Add ?operation=<operation_name> to execute'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
