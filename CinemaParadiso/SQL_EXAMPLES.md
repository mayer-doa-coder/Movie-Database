# 📖 Cinema Paradiso - Complete SQL Operations Examples

This document contains examples of ALL SQL operations from `all_sqls_list.txt` implemented in the Cinema Paradiso system.

## Table of Contents
1. [DDL - Data Definition Language](#ddl)
2. [DML - Data Manipulation Language](#dml)
3. [DQL - Data Query Language](#dql)
4. [Advanced Queries](#advanced)
5. [Joins](#joins)
6. [Set Operations](#set-operations)
7. [Subqueries](#subqueries)

---

## DDL - Data Definition Language

### DROP TABLE
```sql
-- Drop a table (use with caution!)
DROP TABLE IF EXISTS temp_table;
```

### CREATE TABLE
```sql
-- Create a new table
CREATE TABLE user_preferences (
    pref_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    theme VARCHAR(20) DEFAULT 'light',
    language VARCHAR(10) DEFAULT 'en',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
```

### ALTER TABLE - ADD COLUMN
```sql
-- Add a new column to existing table
ALTER TABLE movies ADD budget DECIMAL(12,2);
ALTER TABLE movies ADD box_office DECIMAL(12,2);
```

### ALTER TABLE - MODIFY COLUMN
```sql
-- Modify column datatype
ALTER TABLE movies MODIFY plot_summary LONGTEXT;
ALTER TABLE celebrities MODIFY biography LONGTEXT;
```

### ALTER TABLE - RENAME COLUMN
```sql
-- Rename a column (MySQL 8.0+)
ALTER TABLE movies RENAME COLUMN duration TO runtime;
```

### ALTER TABLE - DROP COLUMN
```sql
-- Remove a column
ALTER TABLE movies DROP COLUMN budget;
```

### Constraints

#### PRIMARY KEY
```sql
-- Drop and add primary key
ALTER TABLE temp_table DROP PRIMARY KEY;
ALTER TABLE temp_table ADD CONSTRAINT pk_temp PRIMARY KEY (id);
```

#### FOREIGN KEY
```sql
-- Add foreign key constraint
ALTER TABLE movie_cast 
ADD CONSTRAINT fk_movie_cast_movie 
FOREIGN KEY (movie_id) REFERENCES movies(movie_id) 
ON DELETE CASCADE;
```

#### ON DELETE CASCADE
```sql
-- Cascade deletes to related records
CREATE TABLE watchlist (
    watchlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE
);
```

#### ON DELETE SET NULL
```sql
-- Set NULL when parent is deleted
ALTER TABLE movies 
ADD CONSTRAINT fk_director 
FOREIGN KEY (director_id) REFERENCES celebrities(celebrity_id) 
ON DELETE SET NULL;
```

#### UNIQUE
```sql
-- Ensure unique values
ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);
ALTER TABLE users ADD CONSTRAINT unique_username UNIQUE (username);
```

#### NOT NULL
```sql
-- Require a value
ALTER TABLE movies MODIFY title VARCHAR(255) NOT NULL;
```

#### CHECK
```sql
-- Validate data
ALTER TABLE movies ADD CONSTRAINT chk_rating CHECK (rating >= 0 AND rating <= 10);
ALTER TABLE reviews ADD CONSTRAINT chk_review_rating CHECK (rating >= 0 AND rating <= 10);
```

#### DEFAULT
```sql
-- Set default value
ALTER TABLE users ALTER COLUMN is_active SET DEFAULT TRUE;
ALTER TABLE movies ALTER COLUMN total_ratings SET DEFAULT 0;
```

---

## DML - Data Manipulation Language

### INSERT INTO
```sql
-- Insert single record
INSERT INTO movies (title, release_date, genre, rating) 
VALUES ('The Matrix', '1999-03-31', 'Sci-Fi', 8.7);

-- Insert multiple records
INSERT INTO celebrities (name, profession, nationality) VALUES
('Keanu Reeves', 'Actor', 'Canadian'),
('Lana Wachowski', 'Director', 'American'),
('Lilly Wachowski', 'Director', 'American');
```

### SELECT
```sql
-- Basic select
SELECT * FROM movies;
SELECT title, rating FROM movies;
SELECT movie_id, title, release_date FROM movies WHERE rating > 8.0;
```

### UPDATE
```sql
-- Update records
UPDATE movies SET rating = 9.0 WHERE movie_id = 1;
UPDATE users SET last_login = NOW() WHERE user_id = 1;
UPDATE celebrities SET profession = 'Actor/Director' WHERE celebrity_id = 2;
```

### DELETE
```sql
-- Delete records
DELETE FROM reviews WHERE rating < 3.0;
DELETE FROM watchlist WHERE user_id = 5;
DELETE FROM movies WHERE rating IS NULL AND created_at < '2020-01-01';
```

---

## DQL - Data Query Language

### SELECT DISTINCT
```sql
-- Get unique values
SELECT DISTINCT genre FROM movies;
SELECT DISTINCT country FROM movies;
SELECT DISTINCT profession FROM celebrities;
SELECT DISTINCT content_type FROM reviews;
```

### SELECT ALL
```sql
-- Explicitly select all (default behavior)
SELECT ALL title FROM movies;
SELECT ALL * FROM tv_series;
```

### Column Aliases (AS)
```sql
-- Rename columns in output
SELECT title AS movie_name, rating AS score FROM movies;
SELECT name AS celebrity_name, profession AS role FROM celebrities;
SELECT COUNT(*) AS total_movies FROM movies;
```

### BETWEEN
```sql
-- Range queries
SELECT * FROM movies WHERE release_date BETWEEN '2000-01-01' AND '2020-12-31';
SELECT * FROM movies WHERE rating BETWEEN 7.0 AND 9.0;
SELECT * FROM celebrities WHERE YEAR(birth_date) BETWEEN 1970 AND 1990;
```

### IN
```sql
-- Match any value in list
SELECT * FROM movies WHERE genre IN ('Action', 'Sci-Fi', 'Thriller');
SELECT * FROM celebrities WHERE profession IN ('Actor', 'Director');
SELECT * FROM tv_series WHERE status IN ('Ongoing', 'Ended');
```

### ORDER BY
```sql
-- Sort results
SELECT * FROM movies ORDER BY rating DESC;
SELECT * FROM movies ORDER BY release_date ASC;
SELECT * FROM movies ORDER BY rating DESC, title ASC;
SELECT * FROM celebrities ORDER BY name;
```

### Complete Query Order
```sql
-- SELECT ... FROM ... WHERE ... GROUP BY ... HAVING ... ORDER BY
SELECT 
    genre,
    COUNT(*) as movie_count,
    AVG(rating) as avg_rating
FROM movies
WHERE release_date > '2000-01-01'
GROUP BY genre
HAVING movie_count > 2
ORDER BY avg_rating DESC;
```

### LIKE Pattern Matching
```sql
-- Wildcard searches
SELECT * FROM movies WHERE title LIKE '%the%';
SELECT * FROM movies WHERE title LIKE 'The %';
SELECT * FROM celebrities WHERE name LIKE '%son';
SELECT * FROM movies WHERE genre LIKE '%Action%';
```

### REGEXP (Regular Expression)
```sql
-- Advanced pattern matching
SELECT * FROM movies WHERE title REGEXP '^The';
SELECT * FROM movies WHERE genre REGEXP 'Action|Drama';
SELECT REGEXP_SUBSTR(title, '[0-9]+') as numbers FROM movies;
```

### MOD (Modulo)
```sql
-- Get remainder
SELECT movie_id, title FROM movies WHERE MOD(movie_id, 2) = 0; -- Even IDs
SELECT * FROM users WHERE MOD(user_id, 3) = 0; -- Every 3rd user
```

---

## Aggregate Functions

### COUNT
```sql
-- Count records
SELECT COUNT(*) as total_movies FROM movies;
SELECT COUNT(DISTINCT genre) as unique_genres FROM movies;
SELECT content_type, COUNT(*) as review_count FROM reviews GROUP BY content_type;
```

### SUM
```sql
-- Sum values
SELECT SUM(duration) as total_runtime FROM movies;
SELECT SUM(total_ratings) as all_ratings FROM movies;
SELECT genre, SUM(total_ratings) as total FROM movies GROUP BY genre;
```

### AVG
```sql
-- Average values
SELECT AVG(rating) as average_rating FROM movies;
SELECT genre, AVG(rating) as avg_rating FROM movies GROUP BY genre;
SELECT AVG(duration) as avg_duration FROM movies WHERE genre = 'Action';
```

### MIN
```sql
-- Minimum value
SELECT MIN(rating) as lowest_rating FROM movies;
SELECT MIN(release_date) as oldest_movie FROM movies;
SELECT genre, MIN(duration) as shortest FROM movies GROUP BY genre;
```

### MAX
```sql
-- Maximum value
SELECT MAX(rating) as highest_rating FROM movies;
SELECT MAX(release_date) as newest_movie FROM movies;
SELECT genre, MAX(duration) as longest FROM movies GROUP BY genre;
```

### NVL / COALESCE
```sql
-- Handle NULL values (MySQL uses COALESCE or IFNULL)
SELECT title, COALESCE(rating, 0) as rating FROM movies;
SELECT name, IFNULL(biography, 'No bio available') as bio FROM celebrities;
```

### GROUP BY
```sql
-- Group records
SELECT genre, COUNT(*) as count FROM movies GROUP BY genre;
SELECT country, AVG(rating) as avg_rating FROM movies GROUP BY country;
SELECT profession, COUNT(*) as count FROM celebrities GROUP BY profession;
```

### HAVING
```sql
-- Filter grouped results
SELECT genre, COUNT(*) as count 
FROM movies 
GROUP BY genre 
HAVING count > 2;

SELECT genre, AVG(rating) as avg_rating 
FROM movies 
GROUP BY genre 
HAVING avg_rating > 8.0;
```

---

## Joins

### Implicit Join (Comma)
```sql
-- Old style join
SELECT m.title, c.name 
FROM movies m, celebrities c 
WHERE m.director_id = c.celebrity_id;
```

### Explicit JOIN ON
```sql
-- Modern join syntax
SELECT m.title, c.name as director
FROM movies m
JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### USING (when column names match)
```sql
-- Simplified join on same column name
SELECT m.title, mc.role
FROM movies m
JOIN movie_cast mc USING (movie_id);
```

### NATURAL JOIN
```sql
-- Auto-join on all matching columns (use cautiously)
SELECT * FROM movies NATURAL JOIN movie_cast;
```

### CROSS JOIN
```sql
-- Cartesian product
SELECT m.title, c.name
FROM movies m
CROSS JOIN celebrities c
LIMIT 10;
```

### INNER JOIN
```sql
-- Only matching records
SELECT m.title, c.name as director
FROM movies m
INNER JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### LEFT OUTER JOIN
```sql
-- All from left table, matching from right
SELECT m.title, c.name as director
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### RIGHT OUTER JOIN
```sql
-- All from right table, matching from left
SELECT c.name, m.title
FROM movies m
RIGHT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### FULL OUTER JOIN
```sql
-- All records from both tables (MySQL workaround with UNION)
SELECT m.title, c.name
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
UNION
SELECT m.title, c.name
FROM movies m
RIGHT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### SELF JOIN
```sql
-- Join table to itself
SELECT u1.username as follower, u2.username as following
FROM user_follows uf
JOIN users u1 ON uf.follower_id = u1.user_id
JOIN users u2 ON uf.following_id = u2.user_id;
```

---

## Set Operations

### UNION
```sql
-- Combine results, remove duplicates
SELECT title, 'Movie' as type FROM movies
UNION
SELECT title, 'Series' as type FROM tv_series;
```

### UNION ALL
```sql
-- Combine results, keep duplicates
SELECT title FROM movies
UNION ALL
SELECT title FROM tv_series;
```

### INTERSECT (MySQL workaround)
```sql
-- Records in both sets
SELECT title FROM movies WHERE genre LIKE '%Drama%'
AND title IN (
    SELECT title FROM movies WHERE genre LIKE '%Crime%'
);
```

### MINUS (MySQL: use NOT IN or NOT EXISTS)
```sql
-- Records in first set but not second
SELECT title FROM movies
WHERE movie_id NOT IN (SELECT content_id FROM watchlist WHERE content_type = 'movie');
```

---

## Subqueries

### Subquery in SELECT
```sql
-- Calculate value for each row
SELECT 
    title, 
    rating,
    (SELECT AVG(rating) FROM movies) as avg_rating,
    rating - (SELECT AVG(rating) FROM movies) as diff_from_avg
FROM movies;
```

### Subquery in FROM
```sql
-- Use subquery as table
SELECT genre, avg_rating
FROM (
    SELECT genre, AVG(rating) as avg_rating
    FROM movies
    GROUP BY genre
) as genre_stats
WHERE avg_rating > 8.0;
```

### Subquery in WHERE
```sql
-- Filter using subquery
SELECT * FROM movies
WHERE rating > (SELECT AVG(rating) FROM movies);

SELECT * FROM celebrities
WHERE celebrity_id IN (
    SELECT DISTINCT celebrity_id FROM movie_cast
);
```

### Correlated Subquery
```sql
-- Subquery references outer query
SELECT m1.title, m1.genre, m1.rating
FROM movies m1
WHERE m1.rating = (
    SELECT MAX(m2.rating)
    FROM movies m2
    WHERE m2.genre = m1.genre
);
```

---

## Advanced Queries

### INSERT INTO SELECT
```sql
-- Insert from query results
INSERT INTO watchlist (user_id, content_type, content_id)
SELECT 1, 'movie', movie_id
FROM movies
WHERE rating > 9.0;
```

### Common Table Expression (CTE) - WITH
```sql
-- Named subqueries
WITH high_rated AS (
    SELECT * FROM movies WHERE rating > 8.5
),
recent AS (
    SELECT * FROM movies WHERE YEAR(release_date) > 2015
)
SELECT * FROM high_rated
INTERSECT
SELECT * FROM recent;
```

### Complex Nested Query
```sql
-- Multiple levels of nesting
SELECT 
    m.title,
    m.rating,
    c.name as director,
    (SELECT COUNT(*) FROM reviews r WHERE r.content_type = 'movie' AND r.content_id = m.movie_id) as review_count,
    (SELECT AVG(rating) FROM reviews r WHERE r.content_type = 'movie' AND r.content_id = m.movie_id) as user_rating
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
WHERE m.rating > (
    SELECT AVG(rating) FROM movies WHERE genre = m.genre
)
ORDER BY m.rating DESC;
```

### Window Functions (MySQL 8.0+)
```sql
-- Ranking and partitioning
SELECT 
    title,
    genre,
    rating,
    ROW_NUMBER() OVER (PARTITION BY genre ORDER BY rating DESC) as genre_rank,
    DENSE_RANK() OVER (ORDER BY rating DESC) as overall_rank
FROM movies;
```

---

## Views

### CREATE VIEW
```sql
-- Create a virtual table
CREATE OR REPLACE VIEW high_rated_movies AS
SELECT m.*, c.name as director_name
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
WHERE m.rating >= 8.0;
```

### SELECT FROM VIEW
```sql
-- Use view like a table
SELECT * FROM high_rated_movies;
SELECT title, rating FROM high_rated_movies WHERE genre LIKE '%Action%';
```

### UPDATE VIEW
```sql
-- Update underlying table through view
UPDATE high_rated_movies 
SET rating = 9.5 
WHERE title = 'Inception';
```

---

## Practical Examples

### Top Rated Movies by Genre
```sql
SELECT genre, title, rating
FROM movies m1
WHERE rating = (
    SELECT MAX(rating) FROM movies m2 WHERE m1.genre = m2.genre
)
ORDER BY genre;
```

### Users with Most Reviews
```sql
SELECT u.username, COUNT(*) as review_count
FROM users u
JOIN reviews r ON u.user_id = r.user_id
GROUP BY u.user_id, u.username
ORDER BY review_count DESC
LIMIT 10;
```

### Movies with Cast Details
```sql
SELECT 
    m.title,
    m.rating,
    GROUP_CONCAT(c.name SEPARATOR ', ') as cast_members
FROM movies m
JOIN movie_cast mc ON m.movie_id = mc.movie_id
JOIN celebrities c ON mc.celebrity_id = c.celebrity_id
WHERE mc.cast_type = 'Actor'
GROUP BY m.movie_id, m.title, m.rating
ORDER BY m.rating DESC;
```

### Genre Statistics
```sql
SELECT 
    genre,
    COUNT(*) as movie_count,
    AVG(rating) as avg_rating,
    MIN(rating) as min_rating,
    MAX(rating) as max_rating,
    SUM(total_ratings) as total_user_ratings
FROM movies
GROUP BY genre
HAVING movie_count >= 2
ORDER BY avg_rating DESC;
```

---

**All these queries can be executed in the Cinema Paradiso SQL Query Executor!**
Watch them appear in the real-time SQL terminal! 🎬
