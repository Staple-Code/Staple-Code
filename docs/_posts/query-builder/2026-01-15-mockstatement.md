---
layout: document
title: "MockStatement"
date: 2026-01-15 20:35:00
categories: Query
---

## MockStatement

A mock implementation of the IStatement interface, used for testing purposes.

### Methods

#### `setRows(array $rows)`
Sets the mock data rows that the statement should return when `fetch()` or `fetchAll()` is called.

**Example:**
```php
use Staple\Query\MockStatement;

$stmt = new MockStatement();
$stmt->setRows([
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob']
]);
```

#### `fetch($fetch_style, $cursor_orientation, $cursor_offset)`
Returns the current row from the mock data and advances the internal pointer.

#### `fetchAll($fetch_style, $fetch_argument, $ctor_args)`
Returns all rows set in the mock statement.

#### `rowCount()`
Returns the number of rows in the mock dataset.

#### `foundRows()`
Returns the number of rows in the mock dataset.

#### `execute($bound_input_params)`
Always returns `true` for the mock implementation.

#### `bindValue($parameter, $value, $data_type)`
Always returns `true` for the mock implementation.
