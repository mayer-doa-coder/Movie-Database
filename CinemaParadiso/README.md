# 🎬 Cinema Paradiso - Movie Database Management System

A comprehensive movie database management system with real-time SQL query monitoring, built with PHP, MySQL, and vanilla JavaScript.

## ✨ Features

### Core Features
- **Complete CRUD Operations** for Movies, TV Series, and Celebrities
- **Real-time SQL Terminal** - Live monitoring of all database queries
- **SQL Query Executor** - Execute custom SQL queries with instant results
- **Advanced SQL Operations** - Demonstrations of JOINs, Subqueries, Aggregations, CTEs, etc.
- **Responsive Dashboard** - Statistics and recent activity monitoring
- **Search & Filters** - Advanced filtering for all data types

### Real-time SQL Terminal
- **Live Query Monitoring** - See every SQL query as it executes
- **Query Classification** - Color-coded by type (SELECT, INSERT, UPDATE, DELETE, etc.)
- **Error Tracking** - Immediate visibility of failed queries
- **Performance Metrics** - Execution time for each query
- **Floating Panel** - Non-intrusive, draggable terminal window

### SQL Operations Included
All operations from your `all_sqls_list.txt`:
- SELECT (DISTINCT, ALL, BETWEEN, IN, LIKE)
- INSERT, UPDATE, DELETE
- JOINs (INNER, LEFT, RIGHT, CROSS, SELF)
- Aggregates (COUNT, SUM, AVG, MIN, MAX)
- GROUP BY, HAVING, ORDER BY
- Subqueries (SELECT, FROM, WHERE)
- UNION, UNION ALL
- Complex nested queries

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (or any PHP + MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Database Setup

1. Start XAMPP (Apache and MySQL)

2. Open phpMyAdmin or MySQL command line

3. Execute the schema file:
```bash
# Navigate to database folder
cd c:\xampp\htdocs\CinemaParadiso\database

# Import the schema
mysql -u root -p < schema.sql
```

Or manually:
- Open phpMyAdmin
- Create database `cinema_paradiso`
- Import `database/schema.sql`

### Step 2: Configure Backend

The backend is already configured for default XAMPP settings:
- Host: `localhost`
- User: `root`
- Password: `` (empty)
- Database: `cinema_paradiso`

If your MySQL settings are different, edit `backend/config.php`:
```php
define('DB_HOST', 'your_host');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'cinema_paradiso');
```

### Step 3: Set File Permissions

Ensure the logs directory is writable:
```bash
# Windows (PowerShell)
icacls "c:\xampp\htdocs\CinemaParadiso\backend\logs" /grant Everyone:F

# Or create an empty log file manually
echo [] > c:\xampp\htdocs\CinemaParadiso\backend\logs\sql_queries.json
```

### Step 4: Access the Application

1. Start XAMPP Apache and MySQL

2. Open your browser and navigate to:
```
http://localhost/CinemaParadiso/frontend/
```

## 📁 Project Structure

```
CinemaParadiso/
├── backend/
│   ├── api/
│   │   ├── movies.php              # Movies CRUD operations
│   │   ├── tv-series.php           # TV Series CRUD operations
│   │   ├── celebrities.php         # Celebrities CRUD operations
│   │   ├── execute-query.php       # Custom SQL query executor
│   │   ├── sql-logs.php            # SQL query logging API
│   │   └── advanced-queries.php    # Advanced SQL demonstrations
│   ├── classes/
│   │   ├── Database.php            # Database connection & query handler
│   │   └── SQLLogger.php           # Real-time SQL logging system
│   ├── logs/
│   │   └── sql_queries.json        # Query log storage
│   └── config.php                  # Database configuration
├── frontend/
│   ├── css/
│   │   └── style.css               # Complete styling
│   ├── js/
│   │   └── app.js                  # Application logic
│   └── index.html                  # Main interface
├── database/
│   └── schema.sql                  # Database schema with sample data
└── README.md
```

## 🎯 Usage Guide

### Dashboard
- View overall statistics
- Monitor recent SQL activity
- Quick access to all sections

### Movies Management
1. Click "Movies" tab
2. Use search and genre filters
3. Click "+ Add Movie" to create new entries
4. View details or delete movies

### TV Series Management
Similar to movies, with status filters (Ongoing, Ended, Cancelled)

### Celebrities Management
Add and manage actors, directors, writers, and producers

### SQL Query Executor
1. Click "SQL Executor" tab
2. Write your custom SQL query
3. Click "Execute Query"
4. View results in formatted table
5. All queries appear in real-time terminal

### Advanced SQL Operations
1. Click "Advanced Queries" tab
2. Click any operation button to see demonstrations:
   - DISTINCT - Get unique values
   - BETWEEN - Range queries
   - IN - Multiple value matching
   - LIKE - Pattern matching
   - Aggregates - COUNT, SUM, AVG, MIN, MAX
   - GROUP BY - Grouped data
   - HAVING - Filter groups
   - JOINs - Multiple table combinations
   - Subqueries - Nested queries
   - UNION - Combine results
   - Complex Queries - Multi-operation examples

### Real-time SQL Terminal
1. Click "Toggle SQL Terminal" button (top right)
2. Terminal appears as floating panel
3. Watch live SQL queries execute
4. Color-coded by query type
5. Shows success/error status
6. Click Clear to reset
7. Click × to hide

## 🔧 API Endpoints

### Movies
```
GET    /api/movies.php              # List all movies
GET    /api/movies.php?id=1         # Get specific movie
POST   /api/movies.php              # Create movie
PUT    /api/movies.php              # Update movie
DELETE /api/movies.php              # Delete movie
```

### TV Series
```
GET    /api/tv-series.php           # List all series
POST   /api/tv-series.php           # Create series
PUT    /api/tv-series.php           # Update series
DELETE /api/tv-series.php           # Delete series
```

### Celebrities
```
GET    /api/celebrities.php         # List all celebrities
POST   /api/celebrities.php         # Create celebrity
PUT    /api/celebrities.php         # Update celebrity
DELETE /api/celebrities.php         # Delete celebrity
```

### SQL Operations
```
POST   /api/execute-query.php       # Execute custom SQL
GET    /api/sql-logs.php            # Get query logs
POST   /api/sql-logs.php            # Get statistics
DELETE /api/sql-logs.php            # Clear logs
GET    /api/advanced-queries.php?operation=<name>  # Run advanced query
```

## 🎨 Features Breakdown

### SQL Logger System
- **Automatic logging** of all database queries
- **Timestamp tracking** with microsecond precision
- **Query type detection** (SELECT, INSERT, UPDATE, etc.)
- **Error capturing** with detailed messages
- **Performance monitoring** with execution times
- **JSON file storage** for persistence

### Query Executor
- **Safe execution** with dangerous operation blocking
- **Result formatting** in clean tables
- **Error handling** with user-friendly messages
- **Support for all SQL types** (DDL, DML, DQL)

### Security Features
- Parameterized queries to prevent SQL injection
- Restricted operations (DROP DATABASE, GRANT, etc.)
- Input validation
- CORS headers configured

## 🐛 Troubleshooting

### Database Connection Error
- Verify XAMPP MySQL is running
- Check credentials in `backend/config.php`
- Ensure `cinema_paradiso` database exists

### SQL Terminal Not Updating
- Check browser console for errors
- Verify `backend/logs/` directory is writable
- Clear browser cache

### Queries Not Appearing
- Ensure file permissions on logs directory
- Check if `sql_queries.json` exists
- Restart Apache

### 404 Errors on API Calls
- Verify XAMPP Apache is running
- Check file paths in `frontend/js/app.js`
- Ensure all PHP files exist

## 📊 Database Schema

The database includes these main tables:
- **users** - User accounts and profiles
- **celebrities** - Actors, directors, writers, producers
- **movies** - Movie information
- **tv_series** - TV show information
- **movie_cast** - Movie-celebrity relationships
- **series_cast** - Series-celebrity relationships
- **watchlist** - User watchlists
- **favorites** - User favorites
- **reviews** - User reviews and ratings
- **review_likes** - Review engagement
- **user_lists** - Custom user lists
- **list_items** - Items in custom lists
- **user_follows** - User follow relationships

## 🚀 Advanced Features

### Real-time Polling
The system polls for new queries every 2 seconds, ensuring the terminal stays updated without page refresh.

### Query Formatting
All queries are formatted with parameter substitution for readability in the terminal.

### Statistics Tracking
- Total queries executed
- Success rate
- Error tracking
- Query type distribution

## 📝 Sample Queries

Try these in the SQL Executor:

```sql
-- Get top-rated movies
SELECT title, rating FROM movies WHERE rating > 8.5 ORDER BY rating DESC;

-- Movies by director
SELECT m.title, c.name as director 
FROM movies m 
JOIN celebrities c ON m.director_id = c.celebrity_id;

-- Average rating by genre
SELECT genre, AVG(rating) as avg_rating, COUNT(*) as count 
FROM movies 
GROUP BY genre 
HAVING count > 1;

-- Complex query with subquery
SELECT title, rating, 
  (SELECT AVG(rating) FROM movies) as avg_rating 
FROM movies 
WHERE rating > (SELECT AVG(rating) FROM movies);
```

## 🎓 Learning Resource

This project demonstrates:
- RESTful API design with PHP
- Real-time data monitoring
- SQL query execution and logging
- Vanilla JavaScript SPA patterns
- CSS Grid and Flexbox layouts
- CRUD operations
- Database relationships
- Advanced SQL techniques

## 📄 License

This project is created for educational purposes.

## 🤝 Contributing

Feel free to enhance this project with:
- User authentication system
- More advanced visualizations
- Export functionality
- Query history search
- Favorites and bookmarks
- Dark mode toggle

---

**Enjoy managing your Cinema Paradiso database! 🎬✨**
