# Advanced SQL Operations - Quick Reference

## 🚀 Quick Access Guide

All 39 SQL operations are accessible via the **Advanced Queries** tab in the web interface.

---

## 📋 Operation List

### SELECT Operations (3)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| DISTINCT | `distinct` | Unique genres |
| ALL vs DISTINCT | `distinct_all` | Comparison |
| Aliases | `aliases` | Column renaming |

### WHERE Clause (4)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| BETWEEN | `between` | Date range filter |
| IN Clause | `in` | Multiple value filter |
| LIKE Pattern | `like` | Text search |
| NOT NULL | `not_null` | Exclude null values |

### Aggregate Functions (2)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| All Aggregates | `aggregates` | COUNT, AVG, MIN, MAX, SUM |
| COUNT | `count` | Count by category |

### Grouping & Filtering (2)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| GROUP BY | `group_by` | Statistics by genre |
| HAVING | `having` | Filter grouped results |

### Sorting (2)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| ORDER BY | `order_by` | Multi-column sorting |
| ASC vs DESC | `order_asc_desc` | Sort direction comparison |

### JOIN Operations (7)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| INNER JOIN | `inner_join` | Matching rows only |
| LEFT JOIN | `left_join` | All from left table |
| RIGHT JOIN | `right_join` | All from right table |
| CROSS JOIN | `cross_join` | Cartesian product |
| SELF JOIN | `self_join` | Table joins itself |
| USING Clause | `natural_join` | Simplified join |
| Multiple JOINs | `multiple_joins` | Join 3+ tables |

### Subqueries (5)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| Subquery WHERE | `subquery_where` | Filter using subquery |
| Subquery SELECT | `subquery_select` | Column from subquery |
| Subquery FROM | `subquery_from` | Derived table |
| EXISTS | `exists_subquery` | Check existence |
| NOT EXISTS | `not_exists_subquery` | Check non-existence |

### Set Operations (2)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| UNION | `union` | Combine, remove duplicates |
| UNION ALL | `union_all` | Combine, keep duplicates |

### Common Table Expression (2)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| CTE (WITH) | `cte` | Named temp result |
| Multiple CTEs | `cte_multiple` | Multiple WITH clauses |

### Complex & Advanced (6)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| Full SQL Syntax | `complex_query` | Complete SQL demo |
| Correlated Subquery | `top_rated_by_genre` | Top by category |
| Celebrity Stats | `celebrity_statistics` | Director metrics |
| CASE Statement | `rating_categories` | Conditional logic |
| Genre Analysis | `genre_comparison` | Cross-content stats |
| Temporal Analysis | `temporal_analysis` | Year-based analysis |

### Utility Functions (4)
| Operation | Button Click | Description |
|-----------|--------------|-------------|
| NULL Handling | `null_handling` | COALESCE, IFNULL |
| String Functions | `string_functions` | UPPER, LOWER, CONCAT |
| Math Operations | `mathematical_operations` | ROUND, CEIL, FLOOR |
| INSERT SELECT | `insert_select_demo` | INSERT from SELECT |

---

## 🔗 API Access

### Get All Operations
```
GET /backend/api/advanced-queries.php
```

Returns list of all available operations.

### Execute Specific Operation
```
GET /backend/api/advanced-queries.php?operation={operation_name}
```

**Examples:**
```
/backend/api/advanced-queries.php?operation=distinct
/backend/api/advanced-queries.php?operation=inner_join
/backend/api/advanced-queries.php?operation=cte
```

---

## 💻 Response Format

### Success Response
```json
{
  "success": true,
  "operation": "Operation Name",
  "data": [
    {/* result rows */}
  ]
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message"
}
```

---

## 🎯 Common Use Cases

### Find Movies Above Average Rating
```
Operation: subquery_where
Query: Movies rated higher than average
```

### Get Statistics by Genre
```
Operation: group_by
Query: Count and average rating per genre
```

### Movies with Directors
```
Operation: inner_join
Query: Movies joined with celebrities table
```

### Combine Movies and Series
```
Operation: union
Query: Unified content list
```

### Top Movie per Genre
```
Operation: top_rated_by_genre
Query: Highest rated in each category
```

---

## 📚 Learning Path

### Beginner Level
1. Start with: `distinct`, `between`, `in`, `like`
2. Practice: `order_by`, `count`, `group_by`
3. Learn: `inner_join`, `left_join`

### Intermediate Level
1. Explore: `subquery_where`, `subquery_select`
2. Master: `aggregates`, `having`
3. Practice: `union`, `self_join`

### Advanced Level
1. Study: `cte`, `cte_multiple`
2. Master: `complex_query`, `top_rated_by_genre`
3. Apply: `celebrity_statistics`, `genre_comparison`

---

## 🛠️ Tips

1. **Start Simple**: Try basic operations first
2. **Read Results**: Understand what each query returns
3. **Compare Operations**: See differences between similar queries
4. **Use Terminal**: Experiment with custom variations
5. **Check Documentation**: `ADVANCED_SQL_OPERATIONS.md` for details

---

## 🎨 UI Elements

### Success Indicators
- ✅ Green checkmark for successful queries
- 📊 Result count badge
- 📋 Formatted data tables

### Operation Sections
- Each category has distinct visual section
- Color-coded headers
- Organized button layout

### Result Display
- Professional table formatting
- Scrollable content area
- Loading states during execution

---

## 📞 Quick Help

**Can't find an operation?**
- Check the category sections
- Use Ctrl+F to search page
- Refer to this quick reference

**Query not working?**
- Check if data exists in database
- Review error message
- Consult full documentation

**Want to modify a query?**
- Use SQL Terminal for custom queries
- Check `backend/api/advanced-queries.php` for syntax
- See `ADVANCED_SQL_OPERATIONS.md` for examples

---

## 📊 Statistics

- **Total Operations**: 39
- **Categories**: 11
- **Tables Used**: movies, tv_series, celebrities, movie_cast
- **SQL Concepts**: All major SQL operations covered
- **Status**: ✅ All operational

---

## 🔍 Search Cheat Sheet

Looking for specific SQL concept?

- **Filtering**: WHERE clause operations
- **Joining**: JOIN operations section
- **Aggregating**: Aggregate functions
- **Combining**: Set operations (UNION)
- **Nested queries**: Subqueries section
- **Temporary tables**: CTE section
- **Conditional logic**: CASE statement in Complex
- **Text search**: LIKE pattern
- **Date filtering**: BETWEEN
- **NULL values**: NULL handling

---

**Last Updated**: October 25, 2025
**Version**: 2.0
**All operations tested and operational** ✅
