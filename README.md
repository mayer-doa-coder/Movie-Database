# Cinema Paradiso - Movie Database Management System

![SQL Coverage](https://img.shields.io/badge/SQL%20Coverage-94.9%25-brightgreen) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange) ![License](https://img.shields.io/badge/License-Educational-lightgrey)

A movie database management system with real-time SQL query monitoring. Built with PHP, MySQL, and JavaScript. Implements 94.9% SQL operation coverage including advanced concepts, views, set operations, and complex queries.

## Table of Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Technology Stack](#technology-stack)
- [Installation & Setup](#installation--setup)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Documentation](#api-documentation)
- [SQL Operations Coverage](#sql-operations-coverage)
- [Usage Guide](#usage-guide)
- [Advanced Features](#advanced-features)
- [Troubleshooting](#troubleshooting)
- [Learning Resources](#learning-resources)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### Core Features
- CRUD operations for Movies, TV Series, Celebrities, Users, and Reviews
- Real-time SQL terminal with live monitoring and color-coded output
- SQL query executor with instant results and error handling
- 56 SQL operations including JOINs, Subqueries, Aggregations, Views, and Set Operations
- 6 pre-built database views for common data access patterns
- INTERSECT and MINUS simulations
- Analytics dashboard with real-time statistics
- Responsive, mobile-friendly interface
- Advanced search and filtering

### Real-time SQL Terminal
- Live query monitoring displays all SQL queries as they execute
- Color-coded query classification (SELECT, INSERT, UPDATE, DELETE, DDL)
- Error tracking with detailed error messages
- Performance metrics showing execution time
- Draggable floating panel interface
- Auto-refresh polling every 2 seconds
- Statistics tracking total queries, success rate, and errors

### SQL Operations Coverage (94.9%)

**Data Definition Language (DDL):**
- CREATE DATABASE, CREATE TABLE, DROP DATABASE
- Foreign Keys, Indexes, Constraints

**Data Manipulation Language (DML):**
- SELECT (DISTINCT, ALL, aliases)
- INSERT (VALUES, SELECT)
- UPDATE, DELETE
- WHERE, BETWEEN, IN, LIKE, IS NULL

**Advanced Queries:**
- JOINs (INNER, LEFT, RIGHT, CROSS, SELF)
- Aggregates (COUNT, SUM, AVG, MIN, MAX)
- GROUP BY, HAVING, ORDER BY
- Subqueries (SELECT, FROM, WHERE clauses)
- UNION, UNION ALL
- Database Views (6 views)
- Set Operations (INTERSECT, MINUS simulations)

**View Operations:**
- `v_movies_with_directors` - Movies with director information
- `v_series_with_creators` - TV series with creator details
- `v_top_rated_content` - Highly rated content (8.0+)
- `v_user_statistics` - User activity summaries
- `v_celebrity_filmography` - Celebrity work statistics
- `v_recent_reviews` - Latest user reviews

**Set Operations:**
- INTERSECT simulations (genre AND rating, common watchlists, genre AND year)
- MINUS simulations (unwatched movies, watchlist NOT favorites, unreviewed movies)

---

## Screenshots

### Dashboard
Main dashboard showing system statistics, recent activity, and module access.

### SQL Terminal
Real-time query monitoring with color-coded output and performance metrics.

### Movies Management
Browse, search, and manage movies with filtering options.

### SQL Query Executor
Execute custom SQL queries with formatted results and error handling.

---

## Technology Stack

**Backend:**
- PHP 7.4+
- MySQL 5.7+
- RESTful API architecture
- PDO for database connections
- Custom SQL Logger system

**Frontend:**
- Vanilla JavaScript (ES6+)
- HTML5 & CSS3
- CSS Grid & Flexbox
- Fetch API for AJAX
- No framework dependencies

**Database:**
- MySQL with InnoDB engine
- 13 normalized tables
- 6 database views
- Foreign key constraints
- Indexes for performance

---

## Installation & Setup

### Prerequisites
- XAMPP (or any PHP + MySQL environment)
  - Apache Web Server
  - MySQL Database Server
  - PHP 7.4 or higher
- Modern web browser
- Text editor (optional)

### Step 1: Download & Extract

1. Clone or download this repository:
```bash
git clone https://github.com/yourusername/cinema-paradiso.git
# OR download and extract the ZIP file
```

2. Move the `CinemaParadiso` folder to your web server directory:
```bash
# For XAMPP on Windows
Move to: C:\xampp\htdocs\CinemaParadiso

# For XAMPP on macOS/Linux
Move to: /Applications/XAMPP/htdocs/CinemaParadiso
```

### Step 2: Database Setup

1. **Start XAMPP Services:**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** modules
   - Wait for both to show green "Running" status

2. **Create Database:**

   **Option A: Using phpMyAdmin (Recommended)**
   - Open browser and go to `http://localhost/phpmyadmin`
   - Click "New" in the left sidebar
   - Database name: `cinema_paradiso`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"
   - Select the `cinema_paradiso` database
   - Click "Import" tab
   - Choose file: Browse to `CinemaParadiso/database/schema.sql`
   - Click "Go" at the bottom
   - Wait for success message

   **Option B: Using MySQL Command Line**
   ```bash
   # Open Command Prompt or Terminal
   cd C:\xampp\mysql\bin
   
   # Login to MySQL
   mysql -u root -p
   # (Press Enter if no password, or enter your MySQL password)
   
   # Create and populate database
   source C:/xampp/htdocs/CinemaParadiso/database/schema.sql
   ```

3. **Create Views:**
   ```bash
   # In phpMyAdmin, select cinema_paradiso database
   # Go to SQL tab
   # Copy and paste content from database/views.sql
   # Click "Go"
   
   # OR via command line:
   mysql -u root -p cinema_paradiso < C:/xampp/htdocs/CinemaParadiso/database/views.sql
   ```

### Step 3: Configure Backend

1. **Check Database Configuration:**

   Open `backend/config.php` and verify settings match your MySQL setup:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Empty for default XAMPP
   define('DB_NAME', 'cinema_paradiso');
   ```

2. **Customize if needed:**
   - If you set a MySQL root password, update `DB_PASS`
   - If using different MySQL user, update `DB_USER`
   - If using different host/port, update `DB_HOST`

### Step 4: Set File Permissions

**Windows Users:**
```powershell
# Open PowerShell as Administrator
cd C:\xampp\htdocs\CinemaParadiso

# Create logs directory if it doesn't exist
New-Item -ItemType Directory -Force -Path backend\logs

# Create empty log file
echo "[]" | Out-File -FilePath backend\logs\sql_queries.json -Encoding UTF8

# Set write permissions
icacls "backend\logs" /grant Everyone:F
```

**macOS/Linux Users:**
```bash
# Open Terminal
cd /Applications/XAMPP/htdocs/CinemaParadiso

# Create logs directory
mkdir -p backend/logs

# Create empty log file
echo "[]" > backend/logs/sql_queries.json

# Set permissions
chmod -R 777 backend/logs
```

### Step 5: Launch Application

1. **Verify Services:**
   - Ensure Apache and MySQL are running in XAMPP Control Panel

2. **Access Application:**
   
   Open your web browser and navigate to:
   ```
   http://localhost/CinemaParadiso/
   ```
   
   OR directly to frontend:
   ```
   http://localhost/CinemaParadiso/frontend/index.html
   ```

3. **First-time Setup Page (Optional):**
   ```
   http://localhost/CinemaParadiso/frontend/setup.html
   ```

### Step 6: Verify Installation

1. **Check Dashboard:**
   - You should see statistics for Movies, Series, Celebrities, and Users
   - Sample data should be loaded from schema.sql

2. **Test SQL Terminal:**
   - Click "Toggle SQL Terminal" button in top-right corner
   - Terminal panel should appear
   - Navigate to different tabs to see queries being logged

3. **Test Database Connection:**
   - Click "Movies" tab
   - You should see a list of movies from the sample data
   - Click "SQL Executor" tab
   - Run a test query: `SELECT * FROM movies LIMIT 5`
   - Results should appear in a formatted table

### Troubleshooting Installation

**Database connection error:**
- Verify MySQL service is running in XAMPP
- Check credentials in `backend/config.php`
- Ensure `cinema_paradiso` database exists

**Blank page or errors:**
- Check Apache error logs: `C:\xampp\apache\logs\error.log`
- Enable PHP error display in `php.ini`: `display_errors = On`
- Verify PHP version: Open `http://localhost/dashboard/phpinfo.php`

**SQL Terminal not updating:**
- Check browser console (F12) for JavaScript errors
- Verify `backend/logs/sql_queries.json` exists and is writable
- Clear browser cache (Ctrl+Shift+Delete)

**404 errors on API calls:**
- Check that all files are in correct locations
- Verify Apache is serving from correct htdocs directory
- Check browser Network tab (F12) for actual error details

---

## Project Structure

```
CinemaParadiso/
│
├── index.php                          # Entry point
├── LICENSE                            # License file
├── README.md                          # Documentation
├── IMPLEMENTATION_SUMMARY.md          # Views & Set Operations summary
├── SQL_ANALYSIS_REPORT.md            # SQL coverage report
├── SQL_COMPARISON_SUMMARY.md         # SQL operations comparison
├── SQL_DETAILED_MAPPING.md           # SQL to code mapping
│
├── backend/                          # PHP Backend
│   ├── config.php                    # Database configuration
│   │
│   ├── api/                          # RESTful API endpoints
│   │   ├── movies.php                # Movies CRUD
│   │   ├── tv-series.php             # TV Series CRUD
│   │   ├── celebrities.php           # Celebrities CRUD
│   │   ├── users.php                 # Users CRUD
│   │   ├── reviews.php               # Reviews CRUD
│   │   ├── execute-query.php         # SQL query executor
│   │   ├── sql-logs.php              # SQL query logging
│   │   ├── advanced-queries.php      # Advanced SQL demos
│   │   ├── analytics.php             # Analytics
│   │   ├── views.php                 # Database views API
│   │   └── set-operations.php        # INTERSECT/MINUS operations
│   │
│   ├── classes/                      # PHP Classes
│   │   ├── Database.php              # Database connection
│   │   └── SQLLogger.php             # SQL logging system
│   │
│   └── logs/                         # Application logs
│       └── sql_queries.json          # SQL query log storage
│
├── database/                         # Database Files
│   ├── schema.sql                    # Database schema + sample data
│   ├── views.sql                     # Database views
│   └── intersect_minus.sql           # Set operations examples
│
└── frontend/                         # Frontend Application
    ├── index.html                    # Main interface
    ├── setup.html                    # Setup guide
    │
    ├── css/
    │   └── style.css                 # Application styling
    │
    └── js/
        └── app.js                    # Application logic
```

### Key Files Description

**Backend Files:**
- `config.php` - Database connection settings
- `Database.php` - PDO wrapper with prepared statements and SQL logging
- `SQLLogger.php` - Logs all SQL queries to JSON file with timestamps
- `movies.php`, `tv-series.php`, etc. - RESTful CRUD endpoints
- `execute-query.php` - Allows custom SQL execution with safety checks
- `advanced-queries.php` - Demonstrates 40+ SQL operations
- `views.php` - Provides access to 6 database views
- `set-operations.php` - INTERSECT and MINUS simulations

**Frontend Files:**
- `index.html` - Single-page application interface with tabs
- `app.js` - Handles all frontend logic, API calls, and UI updates
- `style.css` - Modern, responsive styling with CSS Grid/Flexbox

**Database Files:**
- `schema.sql` - Creates 13 tables with relationships, indexes, and sample data
- `views.sql` - Defines 6 database views for common queries
- `intersect_minus.sql` - Examples of set operations

---

## Database Schema

### Entity Relationship Overview

```
Users ─┬─ Reviews ──→ Movies/TV Series
       ├─ Watchlist ──→ Movies/TV Series
       ├─ Favorites ──→ Movies/TV Series
       ├─ User Lists ──→ List Items ──→ Movies/TV Series
       └─ User Follows

Celebrities ─┬─ Movies (as Directors)
             ├─ TV Series (as Creators)
             ├─ Movie Cast
             └─ Series Cast

Movies ──→ Movie Cast ──→ Celebrities
TV Series ──→ Series Cast ──→ Celebrities

Reviews ──→ Review Likes ──→ Users
```

### Tables (13 Total)

#### Core Content Tables
1. **movies** - Movie information (title, release date, duration, plot, rating, genre, etc.)
2. **tv_series** - TV show information (seasons, episodes, status, air dates, etc.)
3. **celebrities** - Actors, directors, writers, producers (name, biography, nationality, etc.)

#### User & Social Tables
4. **users** - User accounts (username, email, profile, bio, country, etc.)
5. **reviews** - User reviews and ratings for movies/series
6. **review_likes** - Users can like reviews
7. **watchlist** - User watchlists (to-watch list)
8. **favorites** - User favorites (marked as favorite)
9. **user_lists** - Custom user-created lists
10. **user_follows** - User follow relationships

#### Relationship Tables
11. **movie_cast** - Links movies with celebrities (actors, directors, etc.)
12. **series_cast** - Links TV series with celebrities
13. **list_items** - Items within custom user lists

### Database Views (6 Total)

1. **v_movies_with_directors** - Movies joined with director information
2. **v_series_with_creators** - TV series joined with creator information
3. **v_top_rated_content** - All content (movies + series) rated 8.0 or higher
4. **v_user_statistics** - User activity summary (reviews, watchlist, favorites count)
5. **v_celebrity_filmography** - Celebrity work statistics (movies directed, avg rating)
6. **v_recent_reviews** - Recent reviews with user and content details

### Key Features
- Foreign key constraints maintain referential integrity
- Indexes optimize common queries (title, rating, dates)
- ON DELETE CASCADE for automatic cleanup
- Timestamps for audit trails (created_at, updated_at)
- ENUM types for content validation
- DECIMAL storage for precise ratings (0.0 to 10.0)

---

## Usage Guide

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

## � API Documentation

### Base URL
```
http://localhost/CinemaParadiso/backend/api
```

### Authentication
Currently, no authentication is required (educational version). In production, implement JWT or session-based authentication.

---

### Movies API

**Endpoint:** `/api/movies.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | List all movies with optional filters | `GET /api/movies.php` |
| GET | `id=<movie_id>` | Get specific movie details | `GET /api/movies.php?id=1` |
| GET | `search=<term>` | Search movies by title | `GET /api/movies.php?search=matrix` |
| GET | `genre=<genre>` | Filter by genre | `GET /api/movies.php?genre=Action` |
| POST | JSON body | Create new movie | See below |
| PUT | JSON body | Update existing movie | See below |
| DELETE | `id=<movie_id>` | Delete movie | `DELETE /api/movies.php?id=1` |

**Create Movie (POST):**
```json
{
  "title": "The Matrix",
  "release_date": "1999-03-31",
  "duration": 136,
  "plot_summary": "A computer hacker learns from mysterious rebels...",
  "genre": "Sci-Fi",
  "language": "English",
  "country": "USA",
  "rating": 8.7,
  "director_id": 5
}
```

**Update Movie (PUT):**
```json
{
  "movie_id": 1,
  "title": "The Matrix Reloaded",
  "rating": 7.2
}
```

---

### TV Series API

**Endpoint:** `/api/tv-series.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | List all TV series | `GET /api/tv-series.php` |
| GET | `id=<series_id>` | Get specific series | `GET /api/tv-series.php?id=1` |
| GET | `search=<term>` | Search series | `GET /api/tv-series.php?search=breaking` |
| GET | `status=<status>` | Filter by status | `GET /api/tv-series.php?status=Ended` |
| POST | JSON body | Create new series | Similar to movies |
| PUT | JSON body | Update series | Similar to movies |
| DELETE | `id=<series_id>` | Delete series | `DELETE /api/tv-series.php?id=1` |

**Status values:** `Ongoing`, `Ended`, `Cancelled`

---

### Celebrities API

**Endpoint:** `/api/celebrities.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | List all celebrities | `GET /api/celebrities.php` |
| GET | `id=<celebrity_id>` | Get specific celebrity | `GET /api/celebrities.php?id=1` |
| GET | `search=<term>` | Search celebrities | `GET /api/celebrities.php?search=tarantino` |
| GET | `profession=<prof>` | Filter by profession | `GET /api/celebrities.php?profession=Director` |
| POST | JSON body | Create celebrity | See below |
| PUT | JSON body | Update celebrity | See below |
| DELETE | `id=<celebrity_id>` | Delete celebrity | `DELETE /api/celebrities.php?id=1` |

**Profession values:** `Actor`, `Director`, `Writer`, `Producer`

---

### Users API

**Endpoint:** `/api/users.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | List all users | `GET /api/users.php` |
| GET | `id=<user_id>` | Get specific user | `GET /api/users.php?id=1` |
| GET | `search=<term>` | Search users | `GET /api/users.php?search=john` |
| POST | JSON body | Create user | See below |
| PUT | JSON body | Update user | See below |
| DELETE | `id=<user_id>` | Delete user | `DELETE /api/users.php?id=1` |

---

### Reviews API

**Endpoint:** `/api/reviews.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | List all reviews | `GET /api/reviews.php` |
| GET | `user_id=<id>` | Get user's reviews | `GET /api/reviews.php?user_id=1` |
| GET | `content_id=<id>&content_type=movie` | Get reviews for content | `GET /api/reviews.php?content_id=1&content_type=movie` |
| POST | JSON body | Create review | See below |
| PUT | JSON body | Update review | See below |
| DELETE | `id=<review_id>` | Delete review | `DELETE /api/reviews.php?id=1` |

**Create Review:**
```json
{
  "user_id": 1,
  "content_type": "movie",
  "content_id": 5,
  "rating": 9.5,
  "review_title": "Masterpiece!",
  "review_text": "One of the best movies I've ever seen...",
  "is_spoiler": false
}
```

---

### SQL Query Executor API

**Endpoint:** `/api/execute-query.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| POST | `query=<SQL>` | Execute custom SQL query | See below |

**Execute Query:**
```javascript
fetch('/api/execute-query.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: 'query=' + encodeURIComponent('SELECT * FROM movies LIMIT 10')
})
```

**Safety Features:**
- Blocks dangerous operations (DROP DATABASE, GRANT, REVOKE, etc.)
- Returns formatted results with column names
- Error handling with detailed messages
- All queries are logged to SQL terminal

---

### SQL Logs API

**Endpoint:** `/api/sql-logs.php`

| Method | Parameters | Description | Example |
|--------|-----------|-------------|---------|
| GET | none | Get all logged queries | `GET /api/sql-logs.php` |
| GET | `limit=<n>` | Get last N queries | `GET /api/sql-logs.php?limit=50` |
| POST | none | Get query statistics | `POST /api/sql-logs.php` |
| DELETE | none | Clear all logs | `DELETE /api/sql-logs.php` |

**Response Format:**
```json
[
  {
    "query": "SELECT * FROM movies WHERE rating > 8.0",
    "type": "SELECT",
    "timestamp": "2025-12-30 10:30:45.123456",
    "execution_time": 0.0234,
    "success": true,
    "error": null
  }
]
```

---

### Advanced Queries API

**Endpoint:** `/api/advanced-queries.php?operation=<operation>`

Demonstrates 40+ SQL operations. See [SQL Operations Coverage](#sql-operations-coverage) for full list.

**Examples:**
```bash
GET /api/advanced-queries.php?operation=distinct
GET /api/advanced-queries.php?operation=inner_join
GET /api/advanced-queries.php?operation=group_by
GET /api/advanced-queries.php?operation=subquery_where
GET /api/advanced-queries.php?operation=union
```

---

### Database Views API

**Endpoint:** `/api/views.php?action=<view_name>`

| Action | Parameters | Description |
|--------|-----------|-------------|
| `list` | none | List all available views |
| `movies_with_directors` | `limit` | Movies with director info |
| `series_with_creators` | `limit` | Series with creator info |
| `top_rated_content` | `limit` | Content rated 8.0+ |
| `user_statistics` | `user_id` | User activity summary |
| `celebrity_filmography` | `celebrity_id` | Celebrity work stats |
| `recent_reviews` | `limit` | Recent reviews |

**Examples:**
```bash
GET /api/views.php?action=list
GET /api/views.php?action=top_rated_content&limit=20
GET /api/views.php?action=user_statistics&user_id=1
```

---

### Set Operations API

**Endpoint:** `/api/set-operations.php?operation=<operation>`

**INTERSECT Operations:**
| Operation | Parameters | Description |
|-----------|-----------|-------------|
| `intersect_high_rated` | `genre`, `min_rating` | Movies in genre AND high rating |
| `intersect_user_common` | `user1_id`, `user2_id` | Content in both watchlists |
| `intersect_genre_year` | `genre`, `start_year`, `end_year` | Movies in genre AND year range |

**MINUS Operations:**
| Operation | Parameters | Description |
|-----------|-----------|-------------|
| `minus_all_not_watched` | `user_id` | All movies NOT in watchlist |
| `minus_watchlist_not_favorites` | `user_id` | Watchlist items NOT favorited |
| `minus_movies_no_reviews` | none | All movies NOT reviewed |

**Examples:**
```bash
GET /api/set-operations.php?operation=list
GET /api/set-operations.php?operation=intersect_high_rated&genre=Sci-Fi&min_rating=8.0
GET /api/set-operations.php?operation=minus_all_not_watched&user_id=1
```

---

### Analytics API

**Endpoint:** `/api/analytics.php?action=<action>`

| Action | Description |
|--------|-------------|
| `dashboard` | Overall system statistics |
| `genre_distribution` | Movie count by genre |
| `rating_distribution` | Rating histogram |
| `top_reviewers` | Most active reviewers |
| `popular_movies` | Most watched/favorited |

**Example:**
```bash
GET /api/analytics.php?action=dashboard
```

---

## SQL Operations Coverage

This project implements 56 out of 59 SQL operations (94.9% coverage).

### Data Definition Language (DDL)

| Operation | Status | Location | Usage |
|-----------|--------|----------|-------|
| CREATE DATABASE | Implemented | schema.sql | `CREATE DATABASE cinema_paradiso` |
| DROP DATABASE | Implemented | schema.sql | `DROP DATABASE IF EXISTS cinema_paradiso` |
| CREATE TABLE | Implemented | schema.sql | 13 tables created |
| CREATE VIEW | Implemented | views.sql | 6 views created |
| PRIMARY KEY | Implemented | schema.sql | All tables |
| FOREIGN KEY | Implemented | schema.sql | Relationships defined |
| UNIQUE KEY | Implemented | schema.sql | Unique constraints |
| INDEX | Implemented | schema.sql | Performance indexes |

### Data Manipulation Language (DML)

#### SELECT Operations
- SELECT, SELECT DISTINCT, SELECT ALL ✅
- WHERE, BETWEEN, IN, NOT IN ✅
- LIKE, IS NULL, IS NOT NULL ✅
- ORDER BY, LIMIT, AS (Aliases) ✅

#### INSERT, UPDATE, DELETE
- INSERT INTO VALUES ✅
- INSERT INTO SELECT ✅
- UPDATE SET WHERE ✅
- DELETE FROM WHERE ✅

### ✅ JOINs (All Types)
- INNER JOIN ✅
- LEFT JOIN ✅
- RIGHT JOIN ✅
- CROSS JOIN ✅
- SELF JOIN ✅
- Multiple JOINs ✅

### ✅ Aggregate Functions
- COUNT, SUM, AVG, MIN, MAX ✅
- COUNT DISTINCT ✅

### ✅ Grouping & Filtering
- GROUP BY ✅
- HAVING ✅
- GROUP BY Multiple Columns ✅

### ✅ Subqueries
- Subquery in WHERE ✅
- Subquery in SELECT ✅
- Subquery in FROM ✅
- Correlated Subquery ✅
- EXISTS, NOT EXISTS ✅

### ✅ Set Operations
- UNION ✅
- UNION ALL ✅
- INTERSECT (simulated) ✅
- MINUS (simulated) ✅

### ✅ Database Views (6)
- v_movies_with_directors ✅
- v_series_with_creators ✅
- v_top_rated_content ✅
- v_user_statistics ✅
- v_celebrity_filmography ✅
- v_recent_reviews ✅

### ❌ Not Implemented (5.1%)
- EXCEPT - MySQL limitation
- Common Table Expressions (CTEs)
- Window Functions (ROW_NUMBER, RANK)

**Full detailed coverage:** See [SQL_ANALYSIS_REPORT.md](SQL_ANALYSIS_REPORT.md)

---

## Usage Guide

### 1. Dashboard Overview

When you first open the application, you'll see:
- **Statistics Cards**: Total movies, series, celebrities, users
- **Recent Activity**: Latest queries executed
- **Quick Actions**: Navigate to different sections

### 2. Movies Management

**Browse Movies:**
1. Click "Movies" tab
2. Use search box to find movies by title
3. Use genre dropdown to filter
4. Click on any movie to view details

**Add New Movie:**
1. Click "+ Add Movie" button
2. Fill in the form (title, release date, duration, plot, etc.)
3. Select director from celebrity dropdown
4. Click "Save"
5. Watch SQL INSERT query appear in terminal

**Edit Movie:**
1. Click "Edit" button on movie card
2. Modify fields in the form
3. Click "Update"
4. See UPDATE query in terminal

**Delete Movie:**
1. Click "Delete" button on movie card
2. Confirm deletion
3. Watch DELETE query execute

### 3. TV Series Management

Similar to movies, with additional fields:
- Number of seasons/episodes
- Status (Ongoing, Ended, Cancelled)
- First air date and last air date
- Series creator selection

### 4. Celebrities Management

**Add Celebrity:**
1. Click "Celebrities" tab
2. Click "+ Add Celebrity"
3. Enter name, birth date, nationality
4. Select profession (Actor, Director, Writer, Producer)
5. Add biography and profile image URL
6. Save

**Filter Celebrities:**
- Search by name
- Filter by profession
- View their associated movies/series

### 5. Users Management

**View Users:**
- See all registered users
- Search by username or email
- View user statistics (reviews, watchlist, favorites)

**User Details:**
- Full name, country, bio
- Account created date
- Last login timestamp
- Activity summary

### 6. Reviews Management

**View Reviews:**
- Browse all user reviews
- Filter by user or content
- See ratings and review text
- Check for spoiler warnings

**Add Review:**
1. Select content (movie or series)
2. Choose rating (0-10)
3. Add review title and text
4. Mark as spoiler if needed
5. Submit

### 7. SQL Query Executor

**Execute Custom Queries:**
1. Click "SQL Executor" tab
2. Type your SQL query in the text area
3. Click "Execute Query"
4. View formatted results below
5. See query logged in real-time terminal

**Safety Features:**
- Dangerous operations blocked (DROP DATABASE, GRANT, etc.)
- Syntax errors displayed with details
- All queries logged automatically

**Example Queries to Try:**
```sql
-- Top rated movies
SELECT title, rating, genre FROM movies WHERE rating >= 8.0 ORDER BY rating DESC LIMIT 10;

-- Movies with directors
SELECT m.title, c.name as director, m.rating
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
WHERE m.rating > 7.0;

-- Genre statistics
SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating
FROM movies
GROUP BY genre
HAVING count >= 3
ORDER BY avg_rating DESC;

-- Subquery example
SELECT title, rating
FROM movies
WHERE rating > (SELECT AVG(rating) FROM movies);
```

### 8. Advanced SQL Operations

**Access Advanced Queries:**
1. Click "Advanced Queries" tab
2. Browse operation categories
3. Click any operation button to see demonstration

**Categories Include:**
- SELECT Variants (DISTINCT, ALL, Aliases)
- Filter Operations (WHERE, BETWEEN, IN, LIKE)
- JOIN Operations (INNER, LEFT, RIGHT, CROSS, SELF)
- Aggregate Functions (COUNT, SUM, AVG, MIN, MAX)
- Grouping (GROUP BY, HAVING)
- Subqueries (WHERE, SELECT, FROM clauses)
- Set Operations (UNION, UNION ALL)

### 9. Database Views

**Query Pre-Built Views:**
1. Click "Views" tab
2. Select a view:
   - Movies with Directors
   - Series with Creators
   - Top Rated Content (8.0+)
   - User Statistics
   - Celebrity Filmography
   - Recent Reviews
3. Click "View Data"
4. Optionally add parameters (limit, user_id, etc.)

**Use Cases:**
- Quick access to commonly needed data
- Simplified complex queries
- Consistent data presentation

### 10. Set Operations

**INTERSECT Operations:**
1. Click "Set Operations" tab
2. Select INTERSECT type:
   - High Rated Movies in Genre
   - Common Watchlist Items
   - Genre in Year Range
3. Enter parameters
4. Click "Execute"
5. View results showing intersection

**MINUS Operations:**
1. Select MINUS type:
   - Movies Not Watched by User
   - Watchlist Not Favorited
   - Movies Without Reviews
2. Enter parameters if needed
3. Execute to see difference

### 11. Real-time SQL Terminal

**Activate Terminal:**
1. Click "Toggle SQL Terminal" button (top-right)
2. Floating panel appears showing live queries

**Terminal Features:**
- Color-coded query types (green=SELECT, blue=INSERT, orange=UPDATE, red=DELETE)
- Timestamp for each query
- Execution time in milliseconds
- Success/error status
- Auto-scrolls to latest queries
- Draggable window position
- Clear button to reset

**Understanding Query Types:**
- SELECT - Data retrieval
- INSERT - New records
- UPDATE - Modify existing
- DELETE - Remove records
- DDL - Schema changes
- OTHER - Utility queries

### 12. Analytics & Statistics

**View Analytics:**
1. Dashboard shows real-time statistics
2. SQL logs show query distribution
3. User statistics show engagement

**Metrics Tracked:**
- Total movies, series, celebrities
- User count and activity
- Query success rate
- Most active users
- Popular content

### 13. Search & Filtering

**Global Search:**
- Each tab has search functionality
- Real-time filtering as you type
- Searches relevant fields (title, name, etc.)

**Advanced Filters:**
- Genre filter for movies
- Status filter for series
- Profession filter for celebrities
- Content type for reviews

### 14. Mobile Usage

**Responsive Design:**
- Works on tablets and phones
- Touch-friendly interface
- Collapsible terminal on small screens
- Stacked layout for narrow viewports

---

## Features Breakdown

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

---

## Advanced Features

### Real-time Query Monitoring
- **Auto-polling**: Terminal refreshes every 2 seconds
- **Live updates**: See queries as they execute
- **No page refresh**: Updates happen asynchronously
- **Query history**: All queries stored in JSON file

### Performance Metrics
- **Execution time tracking**: Microsecond precision
- **Query statistics**: Success/error counts
- **Performance analysis**: Identify slow queries
- **Benchmarking**: Compare query speeds

### Data Visualization
- **Statistics cards**: Real-time counts
- **Color-coded terminal**: Visual query type identification
- **Formatted tables**: Clean data presentation
- **Responsive charts**: Analytics dashboard

### Query Classification
Automatically detects and classifies:
- SELECT queries (green)
- INSERT queries (blue)
- UPDATE queries (orange)
- DELETE queries (red)
- DDL queries (purple)
- Other operations (gray)

### Error Handling
- **Detailed error messages**: SQL error codes and descriptions
- **Query highlighting**: Shows problematic query
- **Graceful degradation**: App continues working on errors
- **User-friendly feedback**: Clear error explanations

### Developer Tools
- **SQL Query Executor**: Test queries directly
- **Live terminal**: Debug queries in real-time
- **Query logs**: Review execution history
- **API testing**: Direct endpoint access

---

## Troubleshooting

### Common Issues & Solutions

#### 1. Database Connection Error
**Symptoms:**
- "Could not connect to database" error
- Blank pages or 500 errors
- API calls failing

**Solutions:**
```bash
# Check MySQL is running
# In XAMPP Control Panel, MySQL should show "Running"

# Verify database exists
mysql -u root -p
SHOW DATABASES;
# Should see 'cinema_paradiso' in list

# Check credentials in backend/config.php
define('DB_HOST', 'localhost');  # Correct host?
define('DB_USER', 'root');        # Correct username?
define('DB_PASS', '');            # Correct password?
define('DB_NAME', 'cinema_paradiso');  # Correct database name?
```

#### 2. SQL Terminal Not Updating
**Symptoms:**
- Terminal shows old queries
- New queries don't appear
- Terminal is blank

**Solutions:**
```bash
# 1. Check browser console (F12)
# Look for JavaScript errors

# 2. Verify log file exists and is writable
# Windows PowerShell:
Test-Path C:\xampp\htdocs\CinemaParadiso\backend\logs\sql_queries.json
# Should return True

# 3. Check file permissions
icacls "C:\xampp\htdocs\CinemaParadiso\backend\logs" /grant Everyone:F

# 4. Clear browser cache
# Ctrl + Shift + Delete, clear cached files

# 5. Restart Apache
# In XAMPP, stop and start Apache
```

#### 3. Queries Not Being Logged
**Symptoms:**
- SQL terminal empty
- sql_queries.json file not updating

**Solutions:**
```bash
# 1. Check if file exists
# Should be at: backend/logs/sql_queries.json

# 2. Ensure directory is writable
# Windows:
icacls "backend\logs" /grant Everyone:F

# 3. Check SQLLogger.php is loaded
# Verify require_once in Database.php

# 4. Manually create log file
echo "[]" > backend/logs/sql_queries.json

# 5. Check Apache error logs
# C:\xampp\apache\logs\error.log
```

#### 4. 404 Errors on API Calls
**Symptoms:**
- "404 Not Found" in browser console
- API endpoints not responding
- Broken functionality

**Solutions:**
```bash
# 1. Verify file paths
# Check frontend/js/app.js for correct API_BASE
const API_BASE = '../backend/api';  # Should be relative path

# 2. Ensure all PHP files exist
# Check backend/api/ directory contains all files

# 3. Verify Apache is serving from correct directory
# In XAMPP, DocumentRoot should be C:\xampp\htdocs

# 4. Check .htaccess (if using)
# Ensure no incorrect rewrite rules

# 5. Test API directly in browser
http://localhost/CinemaParadiso/backend/api/movies.php
```

#### 5. Blank Page or White Screen
**Symptoms:**
- Application shows blank page
- No errors visible
- Nothing loads

**Solutions:**
```bash
# 1. Enable PHP error display
# Edit C:\xampp\php\php.ini
display_errors = On
error_reporting = E_ALL

# 2. Check browser console (F12)
# Look for JavaScript errors

# 3. Verify all files uploaded correctly
# Compare with project structure

# 4. Check Apache error logs
# C:\xampp\apache\logs\error.log

# 5. Test PHP is working
# Create test.php with: <?php echo "Working"; ?>
# Access: http://localhost/CinemaParadiso/test.php
```

#### 6. Views Not Working
**Symptoms:**
- "Table doesn't exist" errors
- Views tab shows errors
- View queries failing

**Solutions:**
```bash
# 1. Verify views were created
mysql -u root -p cinema_paradiso
SHOW FULL TABLES WHERE Table_type = 'VIEW';
# Should show 6 views

# 2. Recreate views if missing
# In phpMyAdmin, go to SQL tab
# Copy content from database/views.sql
# Click Go

# 3. Or via command line:
mysql -u root -p cinema_paradiso < database/views.sql

# 4. Test individual view
SELECT * FROM v_movies_with_directors LIMIT 5;
```

#### 7. Foreign Key Constraint Errors
**Symptoms:**
- Cannot add/update records
- "Cannot add or update a child row" error
- "Cannot delete or update a parent row" error

**Solutions:**
```sql
-- 1. Check if referenced record exists
-- Example: Adding movie with director_id=99
SELECT * FROM celebrities WHERE celebrity_id = 99;
-- Should return a row

-- 2. Use NULL for optional foreign keys
UPDATE movies SET director_id = NULL WHERE movie_id = 1;

-- 3. Delete in correct order (children first)
DELETE FROM reviews WHERE content_id = 1 AND content_type = 'movie';
DELETE FROM movies WHERE movie_id = 1;

-- 4. Check cascade settings
SHOW CREATE TABLE movie_cast;
-- Should show ON DELETE CASCADE
```

#### 8. Slow Query Performance
**Symptoms:**
- Queries take long time
- Terminal shows high execution times
- Application feels sluggish

**Solutions:**
```sql
-- 1. Check if indexes exist
SHOW INDEX FROM movies;
SHOW INDEX FROM tv_series;

-- 2. Add missing indexes
CREATE INDEX idx_genre ON movies(genre);
CREATE INDEX idx_rating ON movies(rating);

-- 3. Use EXPLAIN to analyze queries
EXPLAIN SELECT * FROM movies WHERE genre = 'Action';

-- 4. Optimize tables
OPTIMIZE TABLE movies;
OPTIMIZE TABLE tv_series;

-- 5. Limit result sets
SELECT * FROM movies LIMIT 100;  -- Instead of SELECT *
```

#### 9. Character Encoding Issues
**Symptoms:**
- Special characters display as ���
- Foreign language text corrupted
- Emojis not displaying

**Solutions:**
```sql
-- 1. Check database charset
SHOW CREATE DATABASE cinema_paradiso;
-- Should show utf8mb4

-- 2. Check table charset
SHOW CREATE TABLE movies;
-- Should show CHARSET=utf8mb4

-- 3. Set connection charset in config.php
$pdo->exec("SET NAMES 'utf8mb4'");

-- 4. Use proper headers in PHP
header('Content-Type: application/json; charset=utf-8');
```

#### 10. XAMPP Specific Issues
**Symptoms:**
- Apache won't start
- Port conflicts
- MySQL won't start

**Solutions:**
```bash
# 1. Check port conflicts
# Apache usually uses port 80 and 443
# MySQL uses port 3306

# 2. Change Apache port
# Edit C:\xampp\apache\conf\httpd.conf
# Change: Listen 80 to Listen 8080

# 3. Stop conflicting services
# Windows: Skype, IIS, other web servers
# Check Task Manager for processes using ports

# 4. Run as Administrator
# Right-click XAMPP Control Panel
# "Run as Administrator"

# 5. Check firewall settings
# Allow Apache and MySQL through Windows Firewall
```

---

## Sample Queries & Examples

Here are practical SQL queries you can try in the SQL Executor:

### Basic Queries

```sql
-- Get all movies
SELECT * FROM movies;

-- Search movies by title
SELECT * FROM movies WHERE title LIKE '%matrix%';

-- Movies released after 2010
SELECT title, release_date, rating FROM movies WHERE release_date >= '2010-01-01';

-- Top 10 highest rated movies
SELECT title, rating, genre FROM movies ORDER BY rating DESC LIMIT 10;
```

### Filtering & Conditions

```sql
-- Movies in specific genres
SELECT title, genre, rating FROM movies WHERE genre IN ('Action', 'Sci-Fi', 'Thriller');

-- Movies with rating between 7 and 9
SELECT title, rating FROM movies WHERE rating BETWEEN 7.0 AND 9.0;

-- Movies without a director assigned
SELECT title FROM movies WHERE director_id IS NULL;

-- High-rated long movies
SELECT title, duration, rating 
FROM movies 
WHERE rating >= 8.0 AND duration >= 120;
```

### JOIN Queries

```sql
-- Movies with their directors
SELECT m.title, c.name as director, m.rating
FROM movies m
INNER JOIN celebrities c ON m.director_id = c.celebrity_id;

-- All movies with optional director info
SELECT m.title, m.rating, c.name as director
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
ORDER BY m.title;

-- Movies with cast members
SELECT m.title, c.name as actor, mc.role
FROM movies m
INNER JOIN movie_cast mc ON m.movie_id = mc.movie_id
INNER JOIN celebrities c ON mc.celebrity_id = c.celebrity_id
WHERE mc.cast_type = 'Actor';
```

### Aggregate Functions

```sql
-- Total number of movies
SELECT COUNT(*) as total_movies FROM movies;

-- Average rating of all movies
SELECT AVG(rating) as avg_rating FROM movies;

-- Longest and shortest movies
SELECT MAX(duration) as longest, MIN(duration) as shortest FROM movies;

-- Total duration of all movies
SELECT SUM(duration) as total_minutes FROM movies;
```

### GROUP BY & HAVING

```sql
-- Count movies by genre
SELECT genre, COUNT(*) as count
FROM movies
GROUP BY genre
ORDER BY count DESC;

-- Average rating by genre (genres with 3+ movies)
SELECT genre, COUNT(*) as count, AVG(rating) as avg_rating
FROM movies
GROUP BY genre
HAVING count >= 3
ORDER BY avg_rating DESC;

-- Directors with multiple movies
SELECT c.name, COUNT(*) as movie_count, AVG(m.rating) as avg_rating
FROM movies m
INNER JOIN celebrities c ON m.director_id = c.celebrity_id
GROUP BY c.celebrity_id, c.name
HAVING movie_count >= 2;
```

### Subqueries

```sql
-- Movies rated above average
SELECT title, rating
FROM movies
WHERE rating > (SELECT AVG(rating) FROM movies)
ORDER BY rating DESC;

-- Movies with comparison to average
SELECT title, rating,
  (SELECT AVG(rating) FROM movies) as avg_rating,
  rating - (SELECT AVG(rating) FROM movies) as diff_from_avg
FROM movies
ORDER BY rating DESC;

-- Directors with at least one highly rated movie
SELECT name FROM celebrities
WHERE celebrity_id IN (
  SELECT director_id FROM movies WHERE rating >= 9.0
);

-- Movies that have reviews
SELECT * FROM movies m
WHERE EXISTS (
  SELECT 1 FROM reviews r
  WHERE r.content_id = m.movie_id AND r.content_type = 'movie'
);
```

### Set Operations

```sql
-- All content titles (movies and series)
SELECT title, 'Movie' as type FROM movies
UNION
SELECT title, 'Series' as type FROM tv_series;

-- All genres (with duplicates from both tables)
SELECT genre FROM movies
UNION ALL
SELECT genre FROM tv_series;

-- Movies in Action genre AND rated above 8.0 (INTERSECT simulation)
SELECT * FROM movies
WHERE genre = 'Action'
  AND movie_id IN (SELECT movie_id FROM movies WHERE rating >= 8.0);

-- Movies NOT in user's watchlist (MINUS simulation)
SELECT * FROM movies
WHERE movie_id NOT IN (
  SELECT content_id FROM watchlist WHERE user_id = 1 AND content_type = 'movie'
);
```

### Complex Queries

```sql
-- Top 5 directors by average movie rating
SELECT c.name, COUNT(m.movie_id) as movies, AVG(m.rating) as avg_rating
FROM celebrities c
INNER JOIN movies m ON c.celebrity_id = m.director_id
GROUP BY c.celebrity_id, c.name
HAVING movies >= 2
ORDER BY avg_rating DESC
LIMIT 5;

-- Users with most reviews
SELECT u.username, COUNT(r.review_id) as review_count
FROM users u
INNER JOIN reviews r ON u.user_id = r.user_id
GROUP BY u.user_id, u.username
ORDER BY review_count DESC
LIMIT 10;

-- Movies with their review count and average user rating
SELECT m.title, m.rating as imdb_rating,
  COUNT(r.review_id) as review_count,
  AVG(r.rating) as user_avg_rating
FROM movies m
LEFT JOIN reviews r ON m.movie_id = r.content_id AND r.content_type = 'movie'
GROUP BY m.movie_id, m.title, m.rating
HAVING review_count > 0
ORDER BY user_avg_rating DESC;
```

### View Queries

```sql
-- Query pre-built views
SELECT * FROM v_movies_with_directors WHERE rating >= 8.0;
SELECT * FROM v_top_rated_content LIMIT 20;
SELECT * FROM v_user_statistics WHERE total_reviews > 5;
SELECT * FROM v_recent_reviews LIMIT 10;
```

---

## Learning Resources

### What You'll Learn

**Database Design:**
- Normalized table structures (3NF)
- Primary and foreign key relationships
- Indexes for performance optimization
- Constraints and data integrity

**SQL Fundamentals:**
- DDL (CREATE, DROP, ALTER)
- DML (INSERT, UPDATE, DELETE, SELECT)
- All JOIN types and use cases
- Aggregate functions and grouping
- Subqueries and nested queries
- Set operations (UNION, INTERSECT, MINUS)
- Views for data abstraction

**Backend Development:**
- PHP 7+ with modern syntax
- PDO for database connectivity
- Prepared statements (SQL injection prevention)
- RESTful API design patterns
- Error handling and logging
- JSON API responses
- Object-oriented PHP (classes)

**Frontend Development:**
- Vanilla JavaScript (ES6+)
- Fetch API for AJAX requests
- DOM manipulation
- Event handling
- Asynchronous programming
- Real-time data polling
- Single Page Application (SPA) patterns

**Security Best Practices:**
- Parameterized queries
- Input validation
- SQL injection prevention
- XSS prevention
- CORS configuration
- Safe query execution

**Performance Optimization:**
- Database indexing strategies
- Query optimization
- Execution time tracking
- Connection pooling
- Efficient JOIN operations

### Project Use Cases

**For Students:**
- Database management system course project
- SQL practice and learning
- Web development portfolio piece
- Understanding CRUD operations

**For Developers:**
- PHP + MySQL project template
- RESTful API reference
- Real-time logging implementation
- Database design patterns

**For Educators:**
- Teaching SQL concepts with live examples
- Demonstrating database relationships
- Showing best practices in web development
- Interactive SQL learning tool

### Extending the Project

**Authentication System:**
```php
// Add user login/logout
// JWT tokens for API authentication
// Session management
// Password hashing (bcrypt)
```

**Advanced Features:**
```php
// File upload for posters
// Image optimization
// Email notifications
// Scheduled tasks (cron jobs)
// Full-text search
// Pagination
// Caching layer (Redis)
```

**Frontend Enhancements:**
```javascript
// Dark mode toggle
// Drag-and-drop file upload
// Infinite scroll
// Advanced charts (Chart.js)
// Real-time notifications (WebSockets)
// Progressive Web App (PWA)
```

### Related Documentation

- [SQL_ANALYSIS_REPORT.md](SQL_ANALYSIS_REPORT.md) - Detailed SQL coverage analysis
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Views & set operations guide
- [SQL_COMPARISON_SUMMARY.md](SQL_COMPARISON_SUMMARY.md) - SQL operations comparison
- [SQL_DETAILED_MAPPING.md](SQL_DETAILED_MAPPING.md) - Code to SQL operation mapping

---

## Contributing

We welcome contributions! Here's how you can help:

### Reporting Issues

1. Check if issue already exists
2. Use issue templates
3. Provide detailed description
4. Include error messages
5. Steps to reproduce

### Submitting Pull Requests

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Code Style Guidelines

**PHP:**
- PSR-12 coding standard
- Type hints for parameters and returns
- DocBlocks for functions
- Descriptive variable names

**JavaScript:**
- ES6+ syntax
- Consistent indentation (2 spaces)
- Meaningful function names
- Comments for complex logic

**SQL:**
- Uppercase keywords
- Lowercase table/column names
- Proper indentation
- Comments for complex queries

### Suggested Improvements

- [ ] User authentication and authorization
- [ ] Advanced search with filters
- [ ] Export data to CSV/PDF
- [ ] Import data from files
- [ ] Batch operations
- [ ] API rate limiting
- [ ] Query result caching
- [ ] Dark mode theme
- [ ] Mobile app version
- [ ] Multi-language support
- [ ] Activity logs
- [ ] Backup/restore functionality

---

## License

This project is created for educational purposes.

**Educational Use:**
- Free to use for learning
- Modify and experiment
- Use in coursework
- Include in portfolio

**Commercial Use:**
- Requires attribution
- Not licensed for direct resale
- Contact for commercial licensing

**Credits:**
- Created as a comprehensive database management learning project
- Demonstrates modern web development practices
- Covers 94.9% of SQL operations

---

## Support & Contact

**Found a bug?**
- Create an issue on GitHub
- Include error details and steps to reproduce

**Need help?**
- Check [Troubleshooting](#-troubleshooting) section
- Review documentation files
- Search existing issues

**Want to contribute?**
- See [Contributing](#-contributing) section
- Fork and submit pull requests

---

## Project Stats

- **Lines of Code**: 3,000+
- **PHP Files**: 13
- **Database Tables**: 13
- **Database Views**: 6
- **API Endpoints**: 50+
- **SQL Operations**: 56 implemented (94.9% coverage)
- **Features**: Real-time monitoring, CRUD operations, Advanced queries
- **Technologies**: PHP, MySQL, JavaScript, HTML5, CSS3

---

## Acknowledgments

- XAMPP for providing easy PHP + MySQL environment
- Modern web standards (ES6, CSS Grid, Flexbox)
- Educational resources that inspired this project
- Open source community for best practices

---

<div align="center">

**Cinema Paradiso - Movie Database Management System**

Making database learning interactive and fun

**[Back to Top](#cinema-paradiso---movie-database-management-system)**

---

Made for database learners everywhere

**Last Updated:** December 30, 2025

</div>
