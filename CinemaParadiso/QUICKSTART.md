# 🚀 Quick Start Guide - Cinema Paradiso

## Step-by-Step Setup (5 Minutes)

### 1️⃣ Start XAMPP
```
1. Open XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to show green "Running" status
```

### 2️⃣ Create Database
Open your browser and go to: `http://localhost/phpmyadmin`

**Option A - Automatic (Recommended):**
```sql
1. Click "Import" tab
2. Click "Choose File"
3. Select: C:\xampp\htdocs\CinemaParadiso\database\schema.sql
4. Click "Go" at the bottom
5. Wait for "Import has been successfully finished"
```

**Option B - Manual:**
```sql
1. Click "New" in left sidebar
2. Database name: cinema_paradiso
3. Click "Create"
4. Click the database name
5. Click "SQL" tab
6. Copy entire contents from database/schema.sql
7. Paste and click "Go"
```

### 3️⃣ Verify Setup
Open: `http://localhost/CinemaParadiso/frontend/setup.html`

Click "Run System Checks" button.

✅ All checks should be GREEN (success)

If you see RED errors:
- Check XAMPP is running
- Re-import the database
- Verify database name is `cinema_paradiso`

### 4️⃣ Launch Application
Open: `http://localhost/CinemaParadiso/frontend/`

OR click "Go to Application" button on setup page.

---

## 🎯 First Steps in the Application

### View the Dashboard
- See total movies, series, celebrities
- View recent SQL activity
- Monitor query statistics

### Toggle SQL Terminal
1. Click "📊 Toggle SQL Terminal" (top right)
2. Watch queries appear in real-time
3. All database operations show here LIVE!

### Try SQL Executor
1. Click "SQL Executor" tab
2. Enter a query:
```sql
SELECT * FROM movies ORDER BY rating DESC LIMIT 5;
```
3. Click "▶ Execute Query"
4. See results + watch it appear in SQL Terminal!

### Explore Advanced Queries
1. Click "Advanced Queries" tab
2. Click any operation button (e.g., "INNER JOIN", "GROUP BY")
3. See SQL in action
4. Check the terminal - it logged the query!

### Add Data
1. Go to "Movies" tab
2. Click "+ Add Movie"
3. Fill in the form
4. Submit
5. Watch the INSERT query in the terminal!

---

## 🔧 Troubleshooting

### "Can't connect to database"
✅ Fix:
- Make sure MySQL is running in XAMPP
- Check `backend/config.php` has correct password
- Default password is empty for XAMPP

### "SQL Terminal not updating"
✅ Fix:
```bash
# Make logs directory writable (PowerShell as Admin)
cd C:\xampp\htdocs\CinemaParadiso\backend\logs
echo [] > sql_queries.json
```

### "404 Not Found" on API calls
✅ Fix:
- Ensure Apache is running
- Access via `http://localhost/CinemaParadiso/frontend/` (not file://)
- Check all files are in correct folders

### Database already exists error
✅ Fix:
```sql
-- In phpMyAdmin SQL tab:
DROP DATABASE IF EXISTS cinema_paradiso;
-- Then re-import schema.sql
```

---

## 🎓 Learning Path

### Week 1: Basics
- ✅ Run all CRUD operations (Create, Read, Update, Delete)
- ✅ Watch queries in SQL terminal
- ✅ Try basic SELECT queries

### Week 2: Intermediate
- ✅ Use filters and search
- ✅ Execute custom JOIN queries
- ✅ Try GROUP BY and aggregations

### Week 3: Advanced
- ✅ Write complex nested queries
- ✅ Use subqueries in SELECT and WHERE
- ✅ Practice UNION and INTERSECT

### Week 4: Mastery
- ✅ Create custom views
- ✅ Optimize queries
- ✅ Analyze query performance in terminal

---

## 📚 Sample Queries to Try

Copy these into the SQL Executor:

### 1. Basic SELECT
```sql
SELECT title, rating FROM movies WHERE rating > 8.0;
```

### 2. JOIN Query
```sql
SELECT m.title, c.name as director 
FROM movies m 
INNER JOIN celebrities c ON m.director_id = c.celebrity_id;
```

### 3. Aggregation
```sql
SELECT genre, COUNT(*) as movie_count, AVG(rating) as avg_rating 
FROM movies 
GROUP BY genre 
ORDER BY avg_rating DESC;
```

### 4. Subquery
```sql
SELECT title, rating 
FROM movies 
WHERE rating > (SELECT AVG(rating) FROM movies);
```

### 5. Complex Query
```sql
SELECT 
    m.title,
    m.rating,
    c.name as director,
    (SELECT COUNT(*) FROM movie_cast mc WHERE mc.movie_id = m.movie_id) as cast_count
FROM movies m
LEFT JOIN celebrities c ON m.director_id = c.celebrity_id
WHERE m.rating >= 8.0
ORDER BY m.rating DESC;
```

### 6. UNION Example
```sql
SELECT title, 'Movie' as type FROM movies
UNION
SELECT title, 'Series' as type FROM tv_series;
```

---

## 🎨 Features Checklist

Try each feature:

- [ ] View Dashboard statistics
- [ ] Toggle SQL Terminal on/off
- [ ] Add a new movie
- [ ] Search movies by title
- [ ] Filter movies by genre
- [ ] View movie details
- [ ] Delete a movie
- [ ] Add TV series
- [ ] Add celebrity
- [ ] Execute custom SQL query
- [ ] Run each Advanced Query operation
- [ ] Watch queries appear in terminal
- [ ] Clear SQL terminal
- [ ] See error handling (try invalid SQL)

---

## 💡 Pro Tips

1. **Keep Terminal Open**: Always have SQL terminal visible to learn how operations translate to SQL

2. **Start Simple**: Begin with basic SELECT queries, then gradually add complexity

3. **Watch the Logs**: Every button click = SQL query. Learn the patterns!

4. **Experiment Safely**: The sample data can always be recreated by re-importing schema.sql

5. **Use Filters**: Combine search + filters to see WHERE clauses in action

6. **Performance**: Check execution times in terminal for different query types

---

## 🆘 Need Help?

### Check These First:
1. Is XAMPP running? (Both Apache & MySQL green)
2. Did you import the database schema?
3. Are you accessing via localhost (not file://)?
4. Did you run the setup verification?

### Common URLs:
- Application: `http://localhost/CinemaParadiso/frontend/`
- Setup Check: `http://localhost/CinemaParadiso/frontend/setup.html`
- phpMyAdmin: `http://localhost/phpmyadmin`

---

## 🎬 You're Ready!

Everything is set up. Open the application and start exploring!

**Remember**: Every action you take generates SQL queries - watch them flow through the terminal!

Happy coding! 🚀
