---
layout: document
title: "Connection"
date: 2026-01-15 20:35:00
categories: Query
---

## Connection

The Connection class handles the database connection using PDO. It provides methods for executing queries, preparing statements, and managing transactions.

### Drivers

- `DRIVER_MYSQL`: MySQL driver.
- `DRIVER_SQLSRV`: SQL Server driver.
- `DRIVER_SQLITE`: SQLite driver.
- `DRIVER_PGSQL`: PostgreSQL driver.
- `DRIVER_ORACLE`: Oracle driver.

### Methods

#### `get()`
Static alias for `getInstance()`. Returns the default database connection instance configured in the application.

**Example:**
```php
use Staple\Query\Connection;

$db = Connection::get();
```

#### `getInstance()`
Returns the default database connection instance. It lazily creates the connection based on the `db` configuration.

**Example:**
```php
use Staple\Query\Connection;

$db = Connection::getInstance();
```

#### `getNamedConnection($namedInstance)`
Returns a connection instance for a specific named configuration defined in the application.

**Example:**
```php
use Staple\Query\Connection;

$reportingDb = Connection::getNamedConnection('reporting');
```

#### `query($query, $fetchMode, ...$fetchModeArgs)`
Executes a SQL query and returns a `Statement` object. It automatically logs the query and handles errors via observers.

**Example:**
```php
use Staple\Query\Connection;

$db = Connection::get();
$result = $db->query("SELECT * FROM users WHERE status = 'active'");
foreach ($result as $row) {
    echo $row['username'];
}
```

#### `exec($statement)`
Executes an SQL statement and returns the number of affected rows. Suitable for `INSERT`, `UPDATE`, and `DELETE` statements that don't return a result set.

**Example:**
```php
use Staple\Query\Connection;

$db = Connection::get();
$affectedRows = $db->exec("UPDATE users SET status = 'inactive' WHERE last_login < '2025-01-01'");
echo "Updated $affectedRows users.";
```

#### `prepare($query, $options = [])`
Prepares a SQL statement for execution and returns a `PDOStatement` (specifically a `Staple\Query\Statement`) object.

**Example:**
```php
use Staple\Query\Connection;

$db = Connection::get();
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', 123);
$stmt->execute();
$user = $stmt->fetch();
```

#### `quote($string, $parameterType)`
Quotes a string for use in a query according to the underlying driver.

**Example:**
```php
$db = Connection::get();
$quoted = $db->quote("O'Reilly");
// Result: 'O\'Reilly' (for MySQL)
```

#### `lastInsertId($name)`
Returns the ID of the last inserted row or sequence value.

**Example:**
```php
$db = Connection::get();
$db->exec("INSERT INTO users (username) VALUES ('jdoe')");
$id = $db->lastInsertId();
```

#### `beginTransaction()`
Initiates a transaction.

**Example:**
```php
$db = Connection::get();
$db->beginTransaction();
try {
    $db->exec("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    $db->exec("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
}
```

#### `commit()`
Commits a transaction.

#### `rollBack()`
Rolls back a transaction.
