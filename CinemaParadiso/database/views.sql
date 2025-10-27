-- Cinema Paradiso Database Views
-- Common views for simplified queries
USE cinema_paradiso;

-- View 1: Movies with Director Information
CREATE OR REPLACE VIEW v_movies_with_directors AS
SELECT 
    m.movie_id,
    m.title,
    m.release_date,
    m.duration,
    m.genre,
    m.rating,
    m.language,
    m.country,
    c.name as director_name,
    c.nationality as director_nationality
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id;

-- View 2: TV Series with Creator Information
CREATE OR REPLACE VIEW v_series_with_creators AS
SELECT 
    ts.series_id,
    ts.title,
    ts.first_air_date,
    ts.number_of_seasons,
    ts.number_of_episodes,
    ts.genre,
    ts.rating,
    ts.status,
    c.name as creator_name,
    c.nationality as creator_nationality
FROM tv_series ts
LEFT JOIN celebrities c ON ts.creator_id = c.celebrity_id;

-- View 3: Top Rated Content (Movies and Series)
CREATE OR REPLACE VIEW v_top_rated_content AS
SELECT 
    title,
    'Movie' as content_type,
    rating,
    genre,
    release_date as date
FROM movies
WHERE rating >= 8.0
UNION
SELECT 
    title,
    'TV Series' as content_type,
    rating,
    genre,
    first_air_date as date
FROM tv_series
WHERE rating >= 8.0;

-- View 4: User Statistics
CREATE OR REPLACE VIEW v_user_statistics AS
SELECT 
    u.user_id,
    u.username,
    u.full_name,
    u.country,
    (SELECT COUNT(*) FROM reviews WHERE user_id = u.user_id) as total_reviews,
    (SELECT COUNT(*) FROM watchlist WHERE user_id = u.user_id) as watchlist_count,
    (SELECT COUNT(*) FROM favorites WHERE user_id = u.user_id) as favorites_count
FROM users u;

-- View 5: Celebrity Filmography Summary
CREATE OR REPLACE VIEW v_celebrity_filmography AS
SELECT 
    c.celebrity_id,
    c.name,
    c.profession,
    COUNT(DISTINCT m.movie_id) as total_movies_directed,
    AVG(m.rating) as avg_movie_rating
FROM celebrities c
LEFT JOIN movies m ON c.celebrity_id = m.director_id
GROUP BY c.celebrity_id, c.name, c.profession;

-- View 6: Recent Reviews with User and Content Info
CREATE OR REPLACE VIEW v_recent_reviews AS
SELECT 
    r.review_id,
    r.rating,
    r.review_title,
    r.created_at,
    u.username,
    r.content_type,
    CASE 
        WHEN r.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = r.content_id)
        WHEN r.content_type = 'tv_series' THEN (SELECT title FROM tv_series WHERE series_id = r.content_id)
    END as content_title
FROM reviews r
JOIN users u ON r.user_id = u.user_id;

-- Display created views
SHOW FULL TABLES WHERE Table_type = 'VIEW';
