# Advanced SQL Operations - Cinema Paradiso

This document provides a comprehensive overview of all SQL operations implemented in the Cinema Paradiso database system. All operations are **fully operational** and work with real database data.

## 📋 Table of Contents

1. [SELECT Operations](#select-operations)
2. [WHERE Clause Operations](#where-clause-operations)
3. [Aggregate Functions](#aggregate-functions)
4. [Grouping & Filtering](#grouping--filtering)
5. [Sorting Operations](#sorting-operations)
6. [JOIN Operations](#join-operations)
7. [Subqueries](#subqueries)
8. [Set Operations](#set-operations)
9. [Common Table Expressions (CTE)](#common-table-expressions-cte)
10. [Complex & Advanced Queries](#complex--advanced-queries)
11. [Utility Functions](#utility-functions)

---

## SELECT Operations

### 1. DISTINCT
**Operation:** `distinct`
**Description:** Returns unique/distinct values from a column
**Use Case:** Get all unique genres from movies

```sql
SELECT DISTINCT genre FROM movies ORDER BY genre;
```

### 2. ALL vs DISTINCT
**Operation:** `distinct_all`
**Description:** Compares ALL (default) vs DISTINCT results
**Use Case:** Compare total genre entries vs unique genres

```sql
-- ALL (includes duplicates)
SELECT genre FROM movies;

-- DISTINCT (removes duplicates)
SELECT DISTINCT genre FROM movies;
```

### 3. Column Aliases (AS)
**Operation:** `aliases`
**Description:** Rename columns in result set using aliases
**Use Case:** Create more readable column names

```sql
SELECT 
    title AS movie_name, 
    rating AS score, 
    release_date AS premiere_date,
    duration AS runtime_minutes
FROM movies;
```

---

## WHERE Clause Operations

### 4. BETWEEN
**Operation:** `between`
**Description:** Filter results within a range
**Use Case:** Find movies released between 2010-2020

```sql
SELECT title, release_date, rating 
FROM movies 
WHERE release_date BETWEEN '2010-01-01' AND '2020-12-31';
```

### 5. IN Clause
**Operation:** `in`
**Description:** Filter using a list of values
**Use Case:** Find movies in specific genres

```sql
SELECT title, genre, rating 
FROM movies 
WHERE genre IN ('Action', 'Sci-Fi', 'Drama', 'Thriller');
```

### 6. LIKE Pattern
**Operation:** `like`
**Description:** Pattern matching with wildcards
**Use Case:** Find movies with "the" in the title

```sql
SELECT title, genre, rating 
FROM movies 
WHERE title LIKE '%the%';
```

### 7. NOT NULL
**Operation:** `not_null`
**Description:** Filter out NULL values
**Use Case:** Find movies with assigned directors

```sql
SELECT m.title, c.name as director, m.rating 
FROM movies m 
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id 
WHERE m.director_id IS NOT NULL;
```

---

## Aggregate Functions

### 8. All Aggregates
**Operation:** `aggregates`
**Description:** COUNT, SUM, AVG, MIN, MAX functions
**Use Case:** Get comprehensive statistics

```sql
SELECT 
    COUNT(*) as total_movies,
    AVG(rating) as avg_rating,
    MAX(rating) as max_rating,
    MIN(rating) as min_rating,
    AVG(duration) as avg_duration
FROM movies;
```

### 9. COUNT
**Operation:** `count`
**Description:** Count rows and distinct values
**Use Case:** Count movies per genre

```sql
SELECT 
    genre, 
    COUNT(*) as movie_count,
    COUNT(DISTINCT director_id) as unique_directors
FROM movies 
GROUP BY genre;
```

---

## Grouping & Filtering

### 10. GROUP BY
**Operation:** `group_by`
**Description:** Group rows by column values
**Use Case:** Calculate statistics per genre

```sql
SELECT 
    genre, 
    COUNT(*) as count, 
    AVG(rating) as avg_rating,
    MAX(rating) as best_rated
FROM movies 
GROUP BY genre;
```

### 11. HAVING
**Operation:** `having`
**Description:** Filter grouped results
**Use Case:** Find genres with multiple movies

```sql
SELECT 
    genre, 
    COUNT(*) as count
FROM movies 
GROUP BY genre 
HAVING count >= 2;
```

---

## Sorting Operations

### 12. ORDER BY
**Operation:** `order_by`
**Description:** Sort results by multiple columns
**Use Case:** Sort by rating and date

```sql
SELECT title, genre, rating, release_date 
FROM movies 
ORDER BY rating DESC, release_date DESC;
```

### 13. ASC vs DESC
**Operation:** `order_asc_desc`
**Description:** Ascending vs Descending sort
**Use Case:** Compare highest and lowest rated

```sql
-- Highest rated
SELECT title, rating FROM movies ORDER BY rating DESC LIMIT 5;

-- Lowest rated
SELECT title, rating FROM movies ORDER BY rating ASC LIMIT 5;
```

---

## JOIN Operations

### 14. INNER JOIN
**Operation:** `inner_join`
**Description:** Return matching rows from both tables
**Use Case:** Movies with their directors

```sql
SELECT 
    m.title, 
    m.rating,
    c.name as director,
    c.nationality
FROM movies m 
INNER JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### 15. LEFT JOIN (LEFT OUTER JOIN)
**Operation:** `left_join`
**Description:** All rows from left table, matching from right
**Use Case:** All movies, with or without directors

```sql
SELECT 
    m.title, 
    m.rating,
    c.name as director
FROM movies m 
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### 16. RIGHT JOIN (RIGHT OUTER JOIN)
**Operation:** `right_join`
**Description:** All rows from right table, matching from left
**Use Case:** All celebrities, with or without movies

```sql
SELECT 
    c.name as celebrity,
    c.profession,
    m.title
FROM movies m 
RIGHT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### 17. CROSS JOIN
**Operation:** `cross_join`
**Description:** Cartesian product of two tables
**Use Case:** All possible combinations (limited for safety)

```sql
SELECT 
    m.title as movie, 
    c.name as celebrity
FROM (SELECT * FROM movies LIMIT 3) m 
CROSS JOIN (SELECT * FROM celebrities LIMIT 3) c;
```

### 18. SELF JOIN
**Operation:** `self_join`
**Description:** Join table with itself
**Use Case:** Find movies in the same genre

```sql
SELECT DISTINCT
    m1.title as movie1,
    m2.title as movie2,
    m1.genre as shared_genre
FROM movies m1
JOIN movies m2 ON m1.genre = m2.genre AND m1.movie_id < m2.movie_id;
```

### 19. USING Clause (NATURAL JOIN simulation)
**Operation:** `natural_join`
**Description:** Simplified join syntax with common column
**Use Case:** Movies with cast count

```sql
SELECT 
    m.title,
    COUNT(mc.celebrity_id) as cast_count
FROM movies m
LEFT JOIN movie_cast mc USING(movie_id)
GROUP BY m.movie_id, m.title;
```

### 20. Multiple JOINs
**Operation:** `multiple_joins`
**Description:** Join multiple tables
**Use Case:** Movies with director and cast information

```sql
SELECT 
    m.title,
    m.rating,
    d.name as director,
    COUNT(DISTINCT mc.celebrity_id) as cast_count
FROM movies m
LEFT JOIN celebrities d ON m.director_id = d.celebrity_id
LEFT JOIN movie_cast mc ON m.movie_id = mc.movie_id
GROUP BY m.movie_id;
```

---

## Subqueries

### 21. Subquery in WHERE
**Operation:** `subquery_where`
**Description:** Use subquery in WHERE clause
**Use Case:** Movies rated above average

```sql
SELECT title, rating, genre 
FROM movies 
WHERE rating > (SELECT AVG(rating) FROM movies);
```

### 22. Subquery in SELECT
**Operation:** `subquery_select`
**Description:** Use subquery in SELECT clause
**Use Case:** Compare each movie to average

```sql
SELECT 
    title, 
    rating,
    (SELECT AVG(rating) FROM movies) as avg_rating,
    rating - (SELECT AVG(rating) FROM movies) as rating_diff
FROM movies;
```

### 23. Subquery in FROM (Derived Table)
**Operation:** `subquery_from`
**Description:** Use subquery as a table
**Use Case:** Query results of another query

```sql
SELECT 
    genre_stats.genre,
    genre_stats.avg_rating
FROM (
    SELECT genre, AVG(rating) as avg_rating
    FROM movies
    GROUP BY genre
) as genre_stats;
```

### 24. EXISTS Subquery
**Operation:** `exists_subquery`
**Description:** Check if subquery returns any rows
**Use Case:** Find movies with cast members

```sql
SELECT m.title, m.rating
FROM movies m
WHERE EXISTS (
    SELECT 1 FROM movie_cast mc 
    WHERE mc.movie_id = m.movie_id
);
```

### 25. NOT EXISTS Subquery
**Operation:** `not_exists_subquery`
**Description:** Check if subquery returns no rows
**Use Case:** Find movies without cast

```sql
SELECT m.title, m.rating
FROM movies m
WHERE NOT EXISTS (
    SELECT 1 FROM movie_cast mc 
    WHERE mc.movie_id = m.movie_id
);
```

---

## Set Operations

### 26. UNION
**Operation:** `union`
**Description:** Combine results, remove duplicates
**Use Case:** Combine movies and TV series

```sql
SELECT title, 'Movie' as type, rating FROM movies 
UNION 
SELECT title, 'TV Series' as type, rating FROM tv_series;
```

### 27. UNION ALL
**Operation:** `union_all`
**Description:** Combine results, keep duplicates
**Use Case:** All content with duplicates

```sql
SELECT title, genre FROM movies 
UNION ALL 
SELECT title, genre FROM tv_series;
```

---

## Common Table Expressions (CTE)

### 28. WITH Clause (CTE)
**Operation:** `cte`
**Description:** Named temporary result set
**Use Case:** Complex queries with readability

```sql
WITH HighRated AS (
    SELECT title, rating, genre 
    FROM movies 
    WHERE rating >= 8.0
)
SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating
FROM HighRated
GROUP BY genre;
```

### 29. Multiple CTEs
**Operation:** `cte_multiple`
**Description:** Multiple WITH clauses
**Use Case:** Complex analysis with multiple temp tables

```sql
WITH 
    MovieStats AS (
        SELECT genre, AVG(rating) as avg_rating
        FROM movies GROUP BY genre
    ),
    SeriesStats AS (
        SELECT genre, AVG(rating) as avg_rating
        FROM tv_series GROUP BY genre
    )
SELECT * FROM MovieStats
UNION ALL
SELECT * FROM SeriesStats;
```

---

## Complex & Advanced Queries

### 30. Full SQL Syntax
**Operation:** `complex_query`
**Description:** Demonstrates SELECT, FROM, WHERE, GROUP BY, HAVING, ORDER BY
**Use Case:** Complete SQL syntax in one query

```sql
SELECT 
    m.genre,
    COUNT(DISTINCT m.movie_id) as movie_count,
    AVG(m.rating) as avg_rating
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
WHERE m.rating >= 7.0
GROUP BY m.genre
HAVING movie_count >= 2
ORDER BY avg_rating DESC;
```

### 31. Correlated Subquery
**Operation:** `top_rated_by_genre`
**Description:** Subquery references outer query
**Use Case:** Top rated movie per genre

```sql
SELECT m1.genre, m1.title, m1.rating
FROM movies m1 
WHERE m1.rating = (
    SELECT MAX(m2.rating) 
    FROM movies m2 
    WHERE m1.genre = m2.genre
);
```

### 32. Celebrity Statistics
**Operation:** `celebrity_statistics`
**Description:** Complex aggregation with joins
**Use Case:** Director performance metrics

```sql
SELECT 
    c.name,
    COUNT(DISTINCT m.movie_id) as movies_directed,
    AVG(m.rating) as avg_movie_rating,
    MAX(m.rating) as best_movie_rating
FROM celebrities c
LEFT JOIN movies m ON c.celebrity_id = m.director_id
WHERE c.profession LIKE '%Director%'
GROUP BY c.celebrity_id, c.name
HAVING movies_directed > 0;
```

### 33. CASE Statement
**Operation:** `rating_categories`
**Description:** Conditional logic in SQL
**Use Case:** Categorize movies by rating

```sql
SELECT 
    title,
    rating,
    CASE 
        WHEN rating >= 9.0 THEN 'Masterpiece'
        WHEN rating >= 8.0 THEN 'Excellent'
        WHEN rating >= 7.0 THEN 'Good'
        ELSE 'Average'
    END as rating_category
FROM movies;
```

### 34. Genre Comparison
**Operation:** `genre_comparison`
**Description:** Cross-content analysis
**Use Case:** Compare genres across movies and series

```sql
SELECT 
    genre,
    COUNT(*) as total_items,
    AVG(rating) as avg_rating,
    MIN(rating) as min_rating,
    MAX(rating) as max_rating
FROM (
    SELECT genre, rating FROM movies
    UNION ALL
    SELECT genre, rating FROM tv_series
) as all_content
GROUP BY genre;
```

### 35. Temporal Analysis
**Operation:** `temporal_analysis`
**Description:** Date-based analysis
**Use Case:** Movies by release year

```sql
SELECT 
    YEAR(release_date) as release_year,
    COUNT(*) as movies_released,
    AVG(rating) as avg_rating
FROM movies
WHERE release_date IS NOT NULL
GROUP BY YEAR(release_date)
ORDER BY release_year DESC;
```

---

## Utility Functions

### 36. NULL Handling
**Operation:** `null_handling`
**Description:** COALESCE, IFNULL, NVL
**Use Case:** Replace NULL values

```sql
SELECT 
    m.title,
    COALESCE(c.name, 'Unknown Director') as director,
    IFNULL(m.duration, 0) as duration
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### 37. String Functions
**Operation:** `string_functions`
**Description:** UPPER, LOWER, LENGTH, CONCAT, SUBSTRING
**Use Case:** String manipulation

```sql
SELECT 
    title,
    UPPER(title) as uppercase_title,
    LENGTH(title) as title_length,
    CONCAT(title, ' (', genre, ')') as full_display
FROM movies;
```

### 38. Mathematical Operations
**Operation:** `mathematical_operations`
**Description:** ROUND, CEIL, FLOOR, MOD
**Use Case:** Numeric calculations

```sql
SELECT 
    title,
    rating,
    ROUND(rating, 1) as rounded_rating,
    duration,
    ROUND(duration / 60, 2) as duration_hours,
    MOD(duration, 60) as remaining_minutes
FROM movies;
```

### 39. INSERT SELECT Pattern
**Operation:** `insert_select_demo`
**Description:** INSERT data from SELECT query
**Use Case:** Populate tables from query results

```sql
-- Demo version (read-only)
SELECT 
    m.title, 
    m.rating, 
    CASE 
        WHEN m.rating >= 8.5 THEN 'Hall of Fame'
        WHEN m.rating >= 7.5 THEN 'Highly Recommended'
        ELSE 'Standard'
    END as recommendation_level
FROM movies m;

-- Actual INSERT SELECT syntax:
-- INSERT INTO archive_movies
-- SELECT * FROM movies WHERE release_date < '2000-01-01';
```

---

## How to Use

### Via Web Interface
1. Navigate to the "Advanced Queries" tab
2. Click on any operation button to execute it
3. View results in real-time with formatted tables

### Via API
```bash
# Get all operations
GET /backend/api/advanced-queries.php

# Execute specific operation
GET /backend/api/advanced-queries.php?operation=inner_join
```

### Via SQL Terminal
1. Click "Toggle SQL Terminal" button
2. Write custom queries combining these operations
3. Execute and view results in real-time

---

## Coverage Summary

✅ **39 SQL Operations** fully implemented and operational
- 3 SELECT operations
- 4 WHERE clause operations  
- 2 Aggregate functions
- 2 Grouping operations
- 2 Sorting operations
- 7 JOIN operations
- 5 Subquery operations
- 2 Set operations
- 2 CTE operations
- 6 Complex queries
- 4 Utility functions

All queries work with **real database data** from:
- `movies` table
- `tv_series` table
- `celebrities` table
- `movie_cast` table
- `users` table

---

## Additional SQL Concepts Covered

From the `all_sqls_list.txt`, the following DDL operations are also available via SQL Terminal:

### Table Operations
- `DROP TABLE` - Remove table
- `CREATE TABLE` - Create new table
- `ALTER TABLE ADD COLUMN` - Add column
- `ALTER TABLE MODIFY COLUMN` - Change column type
- `ALTER TABLE RENAME COLUMN` - Rename column
- `ALTER TABLE DROP COLUMN` - Remove column

### Constraints
- `PRIMARY KEY` - Unique identifier
- `FOREIGN KEY` - Relationship enforcement
- `ON DELETE CASCADE` - Cascading deletes
- `ON DELETE SET NULL` - Set null on delete
- `UNIQUE` - Unique constraint
- `NOT NULL` - Mandatory field
- `CHECK` - Value validation
- `DEFAULT` - Default value

### DML Operations
- `INSERT INTO VALUES` - Add new records
- `UPDATE SET WHERE` - Modify records
- `DELETE FROM WHERE` - Remove records

All DML operations are accessible via the SQL Terminal for testing and learning.

---

## Best Practices

1. **Use indexes** on frequently queried columns
2. **Limit results** when testing large datasets
3. **Use EXPLAIN** to analyze query performance
4. **Avoid SELECT *** - specify needed columns
5. **Use JOINs** instead of multiple subqueries when possible
6. **Use CTEs** for complex queries readability
7. **Test queries** in development before production

---

## Support

For questions or issues:
- Check `SQL_QUERY_LOCATIONS.md` for query locations in codebase
- Review `SQL_EXAMPLES.md` for additional examples
- Use SQL Terminal to experiment with custom queries

**Last Updated:** October 25, 2025
**Version:** 2.0
**Status:** All operations fully operational ✅
