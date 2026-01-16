---
layout: document
title: "ISelectQuery"
date: 2026-01-15 20:35:00
categories: Query
---

## ISelectQuery

Interface for SELECT query builders. It defines methods for common SELECT operations like selecting columns, joining tables, and ordering results.

### Methods

#### `where($column, $operator, $value, bool $columnJoin, string $paramName, bool $parameterized)`
Adds a `WHERE` condition to the `SELECT` query.

**Example:**
```php
$select->where('status', '=', 'active');
```

#### `columns(array $columns)`
Sets the columns to be selected, replacing any previously set columns.

**Example:**
```php
$select->columns(['id', 'username', 'email']);
```

#### `orderBy(array|string $order)`
Sets the `ORDER BY` clause for the query.

**Example:**
```php
$select->orderBy('created_at DESC');
```

#### `limit(int|Pager $limit, int $offset)`
Sets the `LIMIT` and optionally the `OFFSET` for the query.

**Example:**
```php
$select->limit(10, 20); // LIMIT 10 OFFSET 20
```

#### `leftJoin($table, $condition, $alias, $schema)`
Adds a `LEFT JOIN` to the query.

**Example:**
```php
$select->leftJoin('profiles', 'users.id = profiles.user_id', 'p');
```

#### `innerJoin($table, $condition, $alias, $schema)`
Adds an `INNER JOIN` to the query.

#### `groupBy($group)`
Sets the `GROUP BY` clause for the query.

#### `havingEqual($column, $value, bool $columnJoin, string $paramName, bool $parameterized)`
Adds a `HAVING` clause with an equality condition.
