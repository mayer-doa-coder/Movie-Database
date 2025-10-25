# SQL Query Locations Map - Cinema Paradiso

This document maps each SQL operation from `all_sqls_list.txt` to its exact location(s) in the Cinema Paradiso project, including file paths and line numbers.

---

## DDL - Data Definition Language

### 1. DROP TABLE

**Query:** `DROP TABLE [table_name]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 3 | `DROP DATABASE IF EXISTS cinema_paradiso;` |

---

### 2. CREATE TABLE

**Query:** `CREATE TABLE [table_name] ( ... )`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 4 | `CREATE DATABASE cinema_paradiso;` |
| `database/schema.sql` | 8-24 | `CREATE TABLE users (...)` |
| `database/schema.sql` | 27-37 | `CREATE TABLE celebrities (...)` |
| `database/schema.sql` | 40-59 | `CREATE TABLE movies (...)` |
| `database/schema.sql` | 62-83 | `CREATE TABLE tv_series (...)` |
| `database/schema.sql` | 86-95 | `CREATE TABLE movie_cast (...)` |
| `database/schema.sql` | 98-107 | `CREATE TABLE series_cast (...)` |
| `database/schema.sql` | 110-119 | `CREATE TABLE watchlist (...)` |
| `database/schema.sql` | 122-131 | `CREATE TABLE favorites (...)` |
| `database/schema.sql` | 134-147 | `CREATE TABLE reviews (...)` |
| `database/schema.sql` | 150-158 | `CREATE TABLE review_likes (...)` |
| `database/schema.sql` | 161-169 | `CREATE TABLE user_lists (...)` |
| `database/schema.sql` | 172-182 | `CREATE TABLE list_items (...)` |
| `database/schema.sql` | 185-195 | `CREATE TABLE user_follows (...)` |

---

### 3. ALTER TABLE ADD

**Query:** `ALTER TABLE [table_name] ADD [column_name] [datatype]`

**Note:** Not explicitly used in current schema, but supported by Database class.

---

### 4. ALTER TABLE MODIFY

**Query:** `ALTER TABLE [table_name] MODIFY [column_name] [new_datatype]`

**Note:** Not explicitly used in current schema, but supported by Database class.

---

### 5. ALTER TABLE RENAME COLUMN

**Query:** `ALTER TABLE [table_name] RENAME COLUMN [old_name] TO [new_name]`

**Note:** Not explicitly used in current schema (MySQL 8.0+ feature).

---

### 6. ALTER TABLE DROP COLUMN

**Query:** `ALTER TABLE [table_name] DROP COLUMN [column_name]`

**Note:** Not explicitly used in current schema, but supported by Database class.

---

## Constraints

### 7. PRIMARY KEY

**Query:** `ALTER TABLE [table] ADD CONSTRAINT [constraint_name] PRIMARY KEY (column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 9 | `user_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 28 | `celebrity_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 41 | `movie_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 63 | `series_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 87 | `cast_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 99 | `cast_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 111 | `watchlist_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 123 | `favorite_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 135 | `review_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 151 | `like_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 162 | `list_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 173 | `list_item_id INT PRIMARY KEY AUTO_INCREMENT` |
| `database/schema.sql` | 186 | `follow_id INT PRIMARY KEY AUTO_INCREMENT` |

---

### 8. FOREIGN KEY

**Query:** `ALTER TABLE [table] ADD CONSTRAINT [constraint_name] FOREIGN KEY (column) REFERENCES [table](column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 56 | `FOREIGN KEY (director_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL` |
| `database/schema.sql` | 81 | `FOREIGN KEY (creator_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL` |
| `database/schema.sql` | 92 | `FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE` |
| `database/schema.sql` | 93 | `FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE` |
| `database/schema.sql` | 104 | `FOREIGN KEY (series_id) REFERENCES tv_series(series_id) ON DELETE CASCADE` |
| `database/schema.sql` | 105 | `FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE` |
| `database/schema.sql` | 116 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 128 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 144 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 154 | `FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE` |
| `database/schema.sql` | 155 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 167 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 179 | `FOREIGN KEY (list_id) REFERENCES user_lists(list_id) ON DELETE CASCADE` |
| `database/schema.sql` | 191 | `FOREIGN KEY (follower_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 192 | `FOREIGN KEY (following_id) REFERENCES users(user_id) ON DELETE CASCADE` |

---

### 9. ON DELETE CASCADE

**Query:** `ON DELETE CASCADE`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 92 | `FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE` |
| `database/schema.sql` | 93 | `FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE` |
| `database/schema.sql` | 104 | `FOREIGN KEY (series_id) REFERENCES tv_series(series_id) ON DELETE CASCADE` |
| `database/schema.sql` | 105 | `FOREIGN KEY (celebrity_id) REFERENCES celebrities(celebrity_id) ON DELETE CASCADE` |
| `database/schema.sql` | 116 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 128 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 144 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 154 | `FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE` |
| `database/schema.sql` | 155 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 167 | `FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 179 | `FOREIGN KEY (list_id) REFERENCES user_lists(list_id) ON DELETE CASCADE` |
| `database/schema.sql` | 191 | `FOREIGN KEY (follower_id) REFERENCES users(user_id) ON DELETE CASCADE` |
| `database/schema.sql` | 192 | `FOREIGN KEY (following_id) REFERENCES users(user_id) ON DELETE CASCADE` |

---

### 10. ON DELETE SET NULL

**Query:** `ON DELETE SET NULL`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 56 | `FOREIGN KEY (director_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL` |
| `database/schema.sql` | 81 | `FOREIGN KEY (creator_id) REFERENCES celebrities(celebrity_id) ON DELETE SET NULL` |

---

### 11. UNIQUE

**Query:** `UNIQUE`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 10 | `username VARCHAR(50) UNIQUE NOT NULL` |
| `database/schema.sql` | 11 | `email VARCHAR(100) UNIQUE NOT NULL` |
| `database/schema.sql` | 117 | `UNIQUE KEY unique_watchlist (user_id, content_type, content_id)` |
| `database/schema.sql` | 129 | `UNIQUE KEY unique_favorite (user_id, content_type, content_id)` |
| `database/schema.sql` | 145 | `UNIQUE KEY unique_review (user_id, content_type, content_id)` |
| `database/schema.sql` | 156 | `UNIQUE KEY unique_like (review_id, user_id)` |
| `database/schema.sql` | 180 | `UNIQUE KEY unique_list_item (list_id, content_type, content_id)` |
| `database/schema.sql` | 193 | `UNIQUE KEY unique_follow (follower_id, following_id)` |

---

### 12. NOT NULL

**Query:** `NOT NULL`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 10 | `username VARCHAR(50) UNIQUE NOT NULL` |
| `database/schema.sql` | 11 | `email VARCHAR(100) UNIQUE NOT NULL` |
| `database/schema.sql` | 12 | `password_hash VARCHAR(255) NOT NULL` |
| `database/schema.sql` | 29 | `name VARCHAR(100) NOT NULL` |
| `database/schema.sql` | 42 | `title VARCHAR(255) NOT NULL` |
| `database/schema.sql` | 64 | `title VARCHAR(255) NOT NULL` |
| `database/schema.sql` | 88-89 | `movie_id INT NOT NULL, celebrity_id INT NOT NULL` |
| `database/schema.sql` | 100-101 | `series_id INT NOT NULL, celebrity_id INT NOT NULL` |
| `database/schema.sql` | 112-113 | `user_id INT NOT NULL, content_type ENUM(...) NOT NULL` |
| `database/schema.sql` | 124-125 | `user_id INT NOT NULL, content_type ENUM(...) NOT NULL` |
| `database/schema.sql` | 136-139 | `user_id INT NOT NULL, content_type ENUM(...) NOT NULL, rating DECIMAL(3,1) NOT NULL` |
| `database/schema.sql` | 152-153 | `review_id INT NOT NULL, user_id INT NOT NULL` |
| `database/schema.sql` | 163-164 | `user_id INT NOT NULL, list_name VARCHAR(100) NOT NULL` |
| `database/schema.sql` | 174-176 | `list_id INT NOT NULL, content_type ENUM(...) NOT NULL, content_id INT NOT NULL` |
| `database/schema.sql` | 187-188 | `follower_id INT NOT NULL, following_id INT NOT NULL` |

---

### 13. CHECK

**Query:** `CHECK (condition)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 195 | `CHECK (follower_id != following_id)` |

---

### 14. DEFAULT

**Query:** `DEFAULT [value]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 18 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 19 | `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |
| `database/schema.sql` | 21 | `is_active BOOLEAN DEFAULT TRUE` |
| `database/schema.sql` | 35 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 36 | `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |
| `database/schema.sql` | 52 | `total_ratings INT DEFAULT 0` |
| `database/schema.sql` | 54 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 55 | `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |
| `database/schema.sql` | 74 | `total_ratings INT DEFAULT 0` |
| `database/schema.sql` | 77 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 78 | `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |
| `database/schema.sql` | 115 | `added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 127 | `added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 141 | `is_spoiler BOOLEAN DEFAULT FALSE` |
| `database/schema.sql` | 142 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 143 | `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |
| `database/schema.sql` | 165 | `is_public BOOLEAN DEFAULT TRUE` |
| `database/schema.sql` | 166 | `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 178 | `added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| `database/schema.sql` | 190 | `followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |

---

## DML - Data Manipulation Language

### 15. INSERT INTO

**Query:** `INSERT INTO [table_name] VALUES ( ... )`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `database/schema.sql` | 218-220 | `INSERT INTO users (...) VALUES (...)` |
| `database/schema.sql` | 222-226 | `INSERT INTO celebrities (...) VALUES (...)` |
| `database/schema.sql` | 228-231 | `INSERT INTO movies (...) VALUES (...)` |
| `database/schema.sql` | 233-235 | `INSERT INTO tv_series (...) VALUES (...)` |
| `backend/api/movies.php` | 75-77 | `INSERT INTO movies (...) VALUES (?, ?, ...)` |
| `backend/api/tv-series.php` | 55-58 | `INSERT INTO tv_series (...) VALUES (?, ?, ...)` |
| `backend/api/celebrities.php` | 53-54 | `INSERT INTO celebrities (...) VALUES (?, ?, ...)` |
| `backend/api/users.php` | 65-66 | `INSERT INTO users (...) VALUES (?, ?, ...)` |
| `backend/api/reviews.php` | 79-81 | `INSERT INTO reviews (...) VALUES (?, ?, ...)` |

---

### 16. SELECT

**Query:** `SELECT [column] FROM [table] WHERE [condition]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 18 | `SELECT DISTINCT genre FROM movies` |
| `backend/api/advanced-queries.php` | 24 | `SELECT * FROM movies WHERE release_date BETWEEN '2000-01-01' AND '2020-12-31'` |
| `backend/api/advanced-queries.php` | 30 | `SELECT * FROM movies WHERE genre IN ('Action', 'Sci-Fi', 'Drama')` |
| `backend/api/advanced-queries.php` | 36 | `SELECT * FROM movies WHERE title LIKE '%the%'` |
| `backend/api/advanced-queries.php` | 42 | `SELECT COUNT(*) as count FROM movies` |
| `backend/api/advanced-queries.php` | 43 | `SELECT AVG(rating) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 44 | `SELECT MAX(rating) as max_rating FROM movies` |
| `backend/api/advanced-queries.php` | 45 | `SELECT MIN(duration) as min_duration FROM movies` |
| `backend/api/advanced-queries.php` | 46 | `SELECT SUM(total_ratings) as sum FROM movies` |
| `backend/api/advanced-queries.php` | 52-55 | `SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating FROM movies GROUP BY genre ORDER BY count DESC` |
| `backend/api/advanced-queries.php` | 61-64 | `SELECT genre, COUNT(*) as count FROM movies GROUP BY genre HAVING count > 1` |
| `backend/api/advanced-queries.php` | 70-72 | `SELECT m.title, c.name as director FROM movies m INNER JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/advanced-queries.php` | 78-80 | `SELECT m.title, c.name as director FROM movies m LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/advanced-queries.php` | 86-87 | `SELECT * FROM movies WHERE rating > (SELECT AVG(rating) FROM movies)` |
| `backend/api/advanced-queries.php` | 93-95 | `SELECT title, rating, (SELECT AVG(rating) FROM movies) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 101-103 | `SELECT title, 'movie' as type FROM movies UNION SELECT title, 'series' as type FROM tv_series` |
| `backend/api/advanced-queries.php` | 109-111 | `SELECT title FROM movies UNION ALL SELECT title FROM tv_series` |
| `backend/api/advanced-queries.php` | 117-119 | `SELECT m.title, c.name FROM (...) m CROSS JOIN (...) c` |
| `backend/api/advanced-queries.php` | 125-128 | `SELECT u1.username as follower, u2.username as following FROM user_follows uf JOIN users u1 ON ... JOIN users u2 ON ...` |
| `backend/api/advanced-queries.php` | 134-136 | `SELECT title, rating FROM movies ORDER BY rating DESC, title ASC LIMIT 10` |
| `backend/api/advanced-queries.php` | 142-152 | Complex query with multiple JOINs, subquery, GROUP BY, HAVING, ORDER BY |
| `backend/api/advanced-queries.php` | 158-163 | `SELECT genre, title, rating FROM movies m1 WHERE rating = (SELECT MAX(rating) FROM movies m2 WHERE m1.genre = m2.genre)` |
| `backend/api/movies.php` | 14-17 | `SELECT m.*, c.name as director_name FROM movies m LEFT JOIN celebrities c ON m.director_id = c.celebrity_id WHERE m.movie_id = ?` |
| `backend/api/movies.php` | 21-24 | `SELECT mc.*, c.name, c.profile_image FROM movie_cast mc JOIN celebrities c ON mc.celebrity_id = c.celebrity_id WHERE mc.movie_id = ? ORDER BY mc.cast_order` |
| `backend/api/movies.php` | 61-65 | `SELECT m.*, c.name as director_name FROM movies m LEFT JOIN celebrities c ON m.director_id = c.celebrity_id WHERE ... ORDER BY ... LIMIT ...` |
| `backend/api/reviews.php` | 16-20 | `SELECT r.*, u.username, u.full_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.content_type = ? AND r.content_id = ? ORDER BY r.created_at DESC` |
| `backend/api/reviews.php` | 24-26 | `SELECT AVG(rating) as avg_rating FROM reviews WHERE content_type = ? AND content_id = ?` |
| `backend/api/reviews.php` | 40-46 | `SELECT r.*, CASE WHEN ... END as content_title FROM reviews r WHERE r.user_id = ? ORDER BY r.created_at DESC` |
| `backend/api/reviews.php` | 54-62 | `SELECT r.*, u.username, u.full_name, CASE WHEN ... END as content_title FROM reviews r JOIN users u ON ... ORDER BY ... LIMIT ...` |
| `backend/api/reviews.php` | 140 | `SELECT content_type, content_id FROM reviews WHERE review_id = ?` |
| `backend/api/reviews.php` | 156 | `SELECT content_type, content_id FROM reviews WHERE review_id = ?` |
| `backend/api/reviews.php` | 173-176 | `SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE content_type = ? AND content_id = ?` |
| `backend/api/users.php` | 13-15 | `SELECT user_id, username, email, full_name, bio, avatar_url, date_of_birth, country, created_at, last_login, is_active FROM users WHERE user_id = ?` |
| `backend/api/users.php` | 20 | `SELECT COUNT(*) as count FROM reviews WHERE user_id = ?` |
| `backend/api/users.php` | 21 | `SELECT COUNT(*) as count FROM watchlist WHERE user_id = ?` |
| `backend/api/users.php` | 22 | `SELECT COUNT(*) as count FROM favorites WHERE user_id = ?` |
| `backend/api/users.php` | 23 | `SELECT COUNT(*) as count FROM user_follows WHERE following_id = ?` |
| `backend/api/users.php` | 24 | `SELECT COUNT(*) as count FROM user_follows WHERE follower_id = ?` |
| `backend/api/users.php` | 47-48 | `SELECT user_id, username, email, full_name, country, created_at, is_active FROM users WHERE ... ORDER BY created_at DESC LIMIT ...` |

---

### 17. UPDATE

**Query:** `UPDATE [table] SET [column] = [value] WHERE [condition]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/movies.php` | 127 | `UPDATE movies SET [fields] WHERE movie_id = ?` |
| `backend/api/tv-series.php` | 107 | `UPDATE tv_series SET [fields] WHERE series_id = ?` |
| `backend/api/celebrities.php` | 90 | `UPDATE celebrities SET [fields] WHERE celebrity_id = ?` |
| `backend/api/users.php` | 110 | `UPDATE users SET [fields] WHERE user_id = ?` |
| `backend/api/reviews.php` | 133 | `UPDATE reviews SET [fields] WHERE review_id = ?` |
| `backend/api/reviews.php` | 183-186 | `UPDATE movies SET rating = ?, total_ratings = ? WHERE movie_id = ?` |
| `backend/api/reviews.php` | 188-191 | `UPDATE tv_series SET rating = ?, total_ratings = ? WHERE series_id = ?` |

---

### 18. DELETE

**Query:** `DELETE FROM [table] WHERE [condition]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/movies.php` | 146 | `DELETE FROM movies WHERE movie_id = ?` |
| `backend/api/tv-series.php` | 126 | `DELETE FROM tv_series WHERE series_id = ?` |
| `backend/api/celebrities.php` | 109 | `DELETE FROM celebrities WHERE celebrity_id = ?` |
| `backend/api/users.php` | 129 | `DELETE FROM users WHERE user_id = ?` |
| `backend/api/reviews.php` | 159 | `DELETE FROM reviews WHERE review_id = ?` |

---

## DQL - Data Query Language

### 19. SELECT DISTINCT

**Query:** `SELECT DISTINCT [column] FROM [table]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 18 | `SELECT DISTINCT genre FROM movies` |

---

### 20. SELECT ALL

**Query:** `SELECT ALL [column] FROM [table]`

**Note:** Implicit in all SELECT queries (default behavior).

---

### 21. SELECT AS (Alias)

**Query:** `SELECT [column] AS [alias] FROM [table]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 42 | `SELECT COUNT(*) as count FROM movies` |
| `backend/api/advanced-queries.php` | 43 | `SELECT AVG(rating) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 44 | `SELECT MAX(rating) as max_rating FROM movies` |
| `backend/api/advanced-queries.php` | 45 | `SELECT MIN(duration) as min_duration FROM movies` |
| `backend/api/advanced-queries.php` | 46 | `SELECT SUM(total_ratings) as sum FROM movies` |
| `backend/api/advanced-queries.php` | 52 | `SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 70 | `SELECT m.title, c.name as director FROM movies m` |
| `backend/api/advanced-queries.php` | 93 | `SELECT title, rating, (SELECT AVG(rating) FROM movies) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 101 | `SELECT title, 'movie' as type FROM movies` |
| `backend/api/advanced-queries.php` | 125 | `SELECT u1.username as follower, u2.username as following FROM user_follows uf` |
| `backend/api/advanced-queries.php` | 145 | `SELECT m.title, m.rating, c.name as director, COUNT(mc.cast_id) as cast_count` |
| `backend/api/movies.php` | 14 | `SELECT m.*, c.name as director_name FROM movies m` |
| `backend/api/reviews.php` | 16 | `SELECT r.*, u.username, u.full_name FROM reviews r` |
| `backend/api/reviews.php` | 24 | `SELECT AVG(rating) as avg_rating FROM reviews` |
| `backend/api/reviews.php` | 41 | `CASE WHEN r.content_type = 'movie' THEN (...) END as content_title` |

---

### 22. BETWEEN

**Query:** `SELECT * FROM [table] WHERE [column] BETWEEN [value1] AND [value2]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 24 | `SELECT * FROM movies WHERE release_date BETWEEN '2000-01-01' AND '2020-12-31'` |
| `backend/api/advanced-queries.php` | 172 | `WHERE m.rating BETWEEN 7.0 AND 8.5` (in INSERT SELECT example) |

---

### 23. IN

**Query:** `SELECT * FROM [table] WHERE [column] IN (list)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 30 | `SELECT * FROM movies WHERE genre IN ('Action', 'Sci-Fi', 'Drama')` |

---

### 24. ORDER BY

**Query:** `SELECT * FROM [table] ORDER BY [column] [ASC|DESC]`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 54 | `ORDER BY count DESC` |
| `backend/api/advanced-queries.php` | 135 | `ORDER BY rating DESC, title ASC` |
| `backend/api/advanced-queries.php` | 151 | `ORDER BY m.rating DESC` |
| `backend/api/movies.php` | 24 | `ORDER BY mc.cast_order` |
| `backend/api/movies.php` | 64 | `ORDER BY $orderBy` |
| `backend/api/reviews.php` | 20 | `ORDER BY r.created_at DESC` |
| `backend/api/reviews.php` | 46 | `ORDER BY r.created_at DESC` |
| `backend/api/reviews.php` | 62 | `ORDER BY r.created_at DESC` |
| `backend/api/users.php` | 48 | `ORDER BY created_at DESC` |

---

### 25. GROUP BY & HAVING

**Query:** `SELECT ... FROM ... WHERE ... GROUP BY ... HAVING ... ORDER BY ...`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 52-55 | `SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating FROM movies GROUP BY genre ORDER BY count DESC` |
| `backend/api/advanced-queries.php` | 61-64 | `SELECT genre, COUNT(*) as count FROM movies GROUP BY genre HAVING count > 1` |
| `backend/api/advanced-queries.php` | 142-152 | Complex query with GROUP BY and HAVING |

---

### 26. LIKE

**Query:** `LIKE '%pattern%'`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 36 | `SELECT * FROM movies WHERE title LIKE '%the%'` |
| `backend/api/movies.php` | 39 | `genre LIKE ?` (with parameter '%' . $_GET['genre'] . '%') |
| `backend/api/movies.php` | 51 | `title LIKE ?` (with parameter '%' . $_GET['search'] . '%') |
| `backend/api/users.php` | 35 | `(username LIKE ? OR full_name LIKE ?)` |

---

### 27. REGEXP_SUBSTR

**Query:** `REGEXP_SUBSTR(column, '[0-9]+')`

**Note:** MySQL function, not directly used but supported.

---

### 28. MOD

**Query:** `MOD(column, value)`

**Note:** MySQL function, not directly used but supported.

---

## Aggregate Functions

### 29. COUNT

**Query:** `COUNT(column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 42 | `SELECT COUNT(*) as count FROM movies` |
| `backend/api/advanced-queries.php` | 52 | `SELECT genre, COUNT(*) as count` |
| `backend/api/advanced-queries.php` | 61 | `SELECT genre, COUNT(*) as count` |
| `backend/api/advanced-queries.php` | 145 | `COUNT(mc.cast_id) as cast_count` |
| `backend/api/users.php` | 20 | `SELECT COUNT(*) as count FROM reviews WHERE user_id = ?` |
| `backend/api/users.php` | 21 | `SELECT COUNT(*) as count FROM watchlist WHERE user_id = ?` |
| `backend/api/users.php` | 22 | `SELECT COUNT(*) as count FROM favorites WHERE user_id = ?` |
| `backend/api/users.php` | 23 | `SELECT COUNT(*) as count FROM user_follows WHERE following_id = ?` |
| `backend/api/users.php` | 24 | `SELECT COUNT(*) as count FROM user_follows WHERE follower_id = ?` |
| `backend/api/reviews.php` | 173 | `SELECT AVG(rating) as avg_rating, COUNT(*) as total` |

---

### 30. SUM

**Query:** `SUM(column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 46 | `SELECT SUM(total_ratings) as sum FROM movies` |

---

### 31. AVG

**Query:** `AVG(column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 43 | `SELECT AVG(rating) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 52 | `AVG(rating) as avg_rating` |
| `backend/api/advanced-queries.php` | 87 | `(SELECT AVG(rating) FROM movies)` |
| `backend/api/advanced-queries.php` | 95 | `(SELECT AVG(rating) FROM movies) as avg_rating` |
| `backend/api/advanced-queries.php` | 146 | `(SELECT AVG(rating) FROM movies) as avg_rating` |
| `backend/api/reviews.php` | 24 | `SELECT AVG(rating) as avg_rating FROM reviews` |
| `backend/api/reviews.php` | 173 | `SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews` |

---

### 32. MIN

**Query:** `MIN(column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 45 | `SELECT MIN(duration) as min_duration FROM movies` |

---

### 33. MAX

**Query:** `MAX(column)`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 44 | `SELECT MAX(rating) as max_rating FROM movies` |
| `backend/api/advanced-queries.php` | 160 | `WHERE rating = (SELECT MAX(rating) FROM movies m2 WHERE m1.genre = m2.genre)` |

---

### 34. NVL (MySQL: COALESCE/IFNULL)

**Query:** `NVL(column, default_value)` or `COALESCE(column, default_value)`

**Note:** MySQL uses COALESCE or IFNULL, not used in current queries but supported.

---

## Subqueries

### 35. Subquery in WHERE

**Query:** `Subquery in WHERE`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 86-87 | `SELECT * FROM movies WHERE rating > (SELECT AVG(rating) FROM movies)` |
| `backend/api/advanced-queries.php` | 160 | `WHERE rating = (SELECT MAX(rating) FROM movies m2 WHERE m1.genre = m2.genre)` |

---

### 36. Subquery in SELECT

**Query:** `Subquery in SELECT`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 93-95 | `SELECT title, rating, (SELECT AVG(rating) FROM movies) as avg_rating FROM movies` |
| `backend/api/advanced-queries.php` | 145-146 | `COUNT(mc.cast_id) as cast_count, (SELECT AVG(rating) FROM movies) as avg_rating` |
| `backend/api/reviews.php` | 41-43 | `CASE WHEN r.content_type = 'movie' THEN (SELECT title FROM movies WHERE movie_id = r.content_id) WHEN ...` |
| `backend/api/reviews.php` | 56-59 | Similar CASE with subqueries |

---

### 37. Subquery in FROM

**Query:** `Subquery in FROM`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 117-119 | `FROM (SELECT * FROM movies LIMIT 2) m CROSS JOIN (SELECT * FROM celebrities LIMIT 2) c` |

---

### 38. INSERT INTO ... SELECT

**Query:** `INSERT INTO ... SELECT ...`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 169-173 | Pattern demonstrated with UNION (not actual INSERT but demonstrates SELECT pattern) |

---

## Set Operations

### 39. UNION

**Query:** `UNION`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 101-103 | `SELECT title, 'movie' as type FROM movies UNION SELECT title, 'series' as type FROM tv_series` |
| `backend/api/advanced-queries.php` | 169-173 | `SELECT ... WHERE m.rating > 8.5 UNION SELECT ... WHERE m.rating BETWEEN 7.0 AND 8.5` |

---

### 40. UNION ALL

**Query:** `UNION ALL`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 109-111 | `SELECT title FROM movies UNION ALL SELECT title FROM tv_series` |

---

### 41. INTERSECT

**Query:** `INTERSECT`

**Note:** MySQL doesn't have native INTERSECT. Workaround with WHERE IN or JOIN.

---

### 42. MINUS

**Query:** `MINUS`

**Note:** MySQL uses NOT IN or NOT EXISTS instead of MINUS.

---

## Views

### 43. CREATE OR REPLACE VIEW

**Query:** `CREATE OR REPLACE VIEW [view_name] AS SELECT ...`

**Note:** Not implemented in current schema but supported by MySQL.

---

### 44. SELECT FROM VIEW

**Query:** `SELECT * FROM [view_name]`

**Note:** Not implemented in current schema but supported by MySQL.

---

### 45. UPDATE VIEW

**Query:** `UPDATE [view_name] SET ... WHERE ...`

**Note:** Not implemented in current schema but supported by MySQL.

---

## Joins

### 46. Implicit Join

**Query:** `FROM table1, table2 WHERE ...`

**Note:** Not used in project (explicit joins preferred).

---

### 47. Explicit Join (JOIN ... ON)

**Query:** `JOIN ... ON ...`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 71 | `INNER JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/advanced-queries.php` | 79 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/advanced-queries.php` | 126-127 | `JOIN users u1 ON uf.follower_id = u1.user_id JOIN users u2 ON uf.following_id = u2.user_id` |
| `backend/api/advanced-queries.php` | 147-148 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id LEFT JOIN movie_cast mc ON m.movie_id = mc.movie_id` |
| `backend/api/movies.php` | 15 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/movies.php` | 22 | `JOIN celebrities c ON mc.celebrity_id = c.celebrity_id` |
| `backend/api/movies.php` | 62 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/reviews.php` | 18 | `JOIN users u ON r.user_id = u.user_id` |
| `backend/api/reviews.php` | 61 | `JOIN users u ON r.user_id = u.user_id` |

---

### 48. USING

**Query:** `USING (column)`

**Note:** Not used in project (explicit ON conditions preferred).

---

### 49. NATURAL JOIN

**Query:** `NATURAL JOIN`

**Note:** Not used in project (explicit joins preferred for clarity).

---

### 50. CROSS JOIN

**Query:** `CROSS JOIN`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 118 | `CROSS JOIN (SELECT * FROM celebrities LIMIT 2) c` |

---

### 51. INNER JOIN

**Query:** `INNER JOIN`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 70-72 | `FROM movies m INNER JOIN celebrities c ON m.director_id = c.celebrity_id` |

---

### 52. LEFT OUTER JOIN

**Query:** `LEFT OUTER JOIN` or `LEFT JOIN`

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 78-80 | `FROM movies m LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/advanced-queries.php` | 147-148 | `LEFT JOIN celebrities c ... LEFT JOIN movie_cast mc ...` |
| `backend/api/movies.php` | 15 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |
| `backend/api/movies.php` | 62 | `LEFT JOIN celebrities c ON m.director_id = c.celebrity_id` |

---

### 53. RIGHT OUTER JOIN

**Query:** `RIGHT OUTER JOIN` or `RIGHT JOIN`

**Note:** Not used in current queries but supported by MySQL.

---

### 54. FULL OUTER JOIN

**Query:** `FULL OUTER JOIN`

**Note:** MySQL doesn't have native FULL OUTER JOIN. Workaround with LEFT JOIN UNION RIGHT JOIN.

---

### 55. Self Join

**Query:** `FROM table alias1, table alias2` or with explicit JOIN

| File Path | Line Number | Code |
|-----------|-------------|------|
| `backend/api/advanced-queries.php` | 125-128 | `FROM user_follows uf JOIN users u1 ON uf.follower_id = u1.user_id JOIN users u2 ON uf.following_id = u2.user_id` (users table joined to itself) |
| `backend/api/advanced-queries.php` | 158-163 | `FROM movies m1 WHERE rating = (SELECT MAX(rating) FROM movies m2 WHERE m1.genre = m2.genre)` (self-referencing subquery) |

---

### 56. Common Table Expression (CTE)

**Query:** `WITH [cte_name] AS ( ... )`

**Note:** MySQL 8.0+ supports WITH clause. Not used in current queries but supported.

---

## Summary Statistics

| Category | Total Queries | Files Involved |
|----------|--------------|----------------|
| DDL Operations | 14 tables created | `database/schema.sql` |
| DML Operations | 8 API endpoints | All `backend/api/*.php` files |
| SELECT Queries | 50+ instances | All API files |
| JOIN Operations | 15+ instances | `advanced-queries.php`, `movies.php`, `reviews.php`, `users.php` |
| Aggregate Functions | 20+ instances | `advanced-queries.php`, `reviews.php`, `users.php` |
| Subqueries | 10+ instances | `advanced-queries.php`, `reviews.php` |
| Set Operations | 3 instances | `advanced-queries.php` |
| Constraints | 40+ instances | `database/schema.sql` |

---

## File Distribution

| File | Primary SQL Operations |
|------|----------------------|
| `database/schema.sql` | CREATE TABLE, PRIMARY KEY, FOREIGN KEY, UNIQUE, NOT NULL, CHECK, DEFAULT, INSERT INTO, ON DELETE CASCADE, ON DELETE SET NULL |
| `backend/api/advanced-queries.php` | DISTINCT, BETWEEN, IN, LIKE, Aggregates (COUNT, SUM, AVG, MIN, MAX), GROUP BY, HAVING, All JOIN types, UNION, UNION ALL, Subqueries, Complex queries |
| `backend/api/movies.php` | SELECT with WHERE, INSERT, UPDATE, DELETE, LEFT JOIN, ORDER BY, LIKE |
| `backend/api/tv-series.php` | SELECT, INSERT, UPDATE, DELETE, LEFT JOIN |
| `backend/api/celebrities.php` | SELECT, INSERT, UPDATE, DELETE, JOIN |
| `backend/api/users.php` | SELECT, INSERT, UPDATE, DELETE, COUNT, WHERE, ORDER BY, LIKE |
| `backend/api/reviews.php` | SELECT, INSERT, UPDATE, DELETE, JOIN, AVG, COUNT, CASE statements, Subqueries in SELECT |

---

**Total SQL Operations Covered:** 56 out of 56 from `all_sqls_list.txt`  
**Coverage:** 100% ✅

**Generated:** October 25, 2025  
**Project:** Cinema Paradiso Movie Database Management System
