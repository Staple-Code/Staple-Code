---
layout: document
title: "MockConnection"
date: 2026-01-15 20:35:00
categories: Query
---

## MockConnection

A mock implementation of the IConnection interface, used for testing purposes. It allows simulating database interactions without requiring a real database.

### Methods

#### `setResults($results)`
Sets the results that should be returned by the next `query()` or `exec()` call.

**Example:**
```php
use Staple\Query\MockConnection;

$mock = new MockConnection('mysql:host=localhost;dbname=test');
$mock->setResults([['id' => 1, 'name' => 'Test User']]);
```

#### `query($statement)`
Returns the results previously set via `setResults()`. Logs the query and notifies observers.

#### `exec($statement)`
Returns the results previously set via `setResults()`. Logs the query and notifies observers.

#### `prepare($statement, $options)`
Returns a `MockStatement` object for testing prepared statement interactions.

#### `quote($string, $parameter_type)`
Returns a simple single-quoted string for testing, without actual database escaping.
