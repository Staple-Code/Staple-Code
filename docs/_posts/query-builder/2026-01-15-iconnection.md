---
layout: document
title: "IConnection"
date: 2026-01-15 20:35:00
categories: Query
---

## IConnection

Interface for database connection classes. It defines the mandatory methods that any connection driver must implement.

### Methods

#### `get()`
Static method to retrieve the default connection instance.

**Example:**
```php
use Staple\Query\IConnection;
use Staple\Query\Connection;

$db = Connection::get(); // Implementation of IConnection
```

#### `getDriver()`
Returns the name of the PDO driver being used (e.g., `mysql`, `sqlsrv`).

#### `getLastQuery()`
Returns the last executed SQL query string.

#### `exec(string $statement)`
Executes an SQL statement and returns the number of affected rows.

#### `query(string $query, $fetchMode, ...$fetchModeArgs)`
Executes an SQL query and returns a result set.

#### `prepare(string $query, array $options)`
Prepares an SQL statement for execution.

#### `getSchema()`
Returns the default database schema name.

#### `setSchema($schema)`
Sets the default database schema name.
