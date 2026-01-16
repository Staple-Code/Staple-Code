---
layout: document
title: "Statement"
date: 2026-01-15 20:35:00
categories: Query
---

## Statement

The Statement class is a specialized implementation of `PDOStatement` that provides additional functionality for the STAPLE framework, such as improved `rowCount()` and `foundRows()` support across different database drivers.

### Methods

#### `execute(array $params)`
Executes the prepared statement. It also logs the query to the connection log.

#### `rowCount()`
Returns the number of rows affected by the last SQL statement. For SQL Server, it provides a more reliable row count.

**Example:**
```php
$stmt = $db->query("DELETE FROM logs WHERE id < 100");
echo $stmt->rowCount() . " rows deleted.";
```

#### `foundRows()`
Returns the total number of rows that matched the query, ignoring the `LIMIT` clause. Uses `FOUND_ROWS()` for MySQL and `@@Rowcount` for SQL Server.

**Example:**
```php
$stmt = $db->query("SELECT SQL_CALC_FOUND_ROWS * FROM users LIMIT 10");
$total = $stmt->foundRows();
```

#### `fetch($mode)`
Fetches the next row from the result set.

#### `fetchAll($mode)`
Returns an array containing all the result set rows.

#### `bindValue($param, $value, $type)`
Binds a value to a parameter and stores it in the internal parameters array for logging.
