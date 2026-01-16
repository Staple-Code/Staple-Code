---
layout: document
title: "IStatement"
date: 2026-01-15 20:35:00
categories: Query
---

## IStatement

Interface for database statement classes (typically wrapping PDOStatement).

### Methods

#### `execute(array $params)`
Executes the prepared statement.

**Example:**
```php
$stmt->execute([':id' => 123]);
```

#### `fetch(int $mode)`
Fetches the next row from a result set.

**Example:**
```php
while ($row = $stmt->fetch()) {
    echo $row['name'];
}
```

#### `fetchAll(int $mode)`
Returns an array containing all of the result set rows.

**Example:**
```php
$allUsers = $stmt->fetchAll();
```

#### `rowCount()`
Returns the number of rows affected by the last SQL statement.

#### `foundRows()`
Returns the total number of rows that matched the query, ignoring the `LIMIT` clause (if `SQL_CALC_FOUND_ROWS` was used in the query).

#### `bindValue(string $param, mixed $value, int $type)`
Binds a value to a parameter.

**Example:**
```php
$stmt->bindValue(':status', 'active', PDO::PARAM_STR);
```

#### `bindParam(int|string $param, mixed &$var, int $type)`
Binds a parameter to the specified variable name.
