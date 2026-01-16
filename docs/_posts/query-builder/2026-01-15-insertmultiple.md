---
layout: document
title: "InsertMultiple"
date: 2026-01-15 20:35:00
categories: Query
---

## InsertMultiple

The InsertMultiple class is used to build SQL INSERT statements for multiple rows in a single query.

### Methods

#### `addRow(DataSet $row)`
Adds a single `DataSet` object representing one row to the multiple insert query. The columns in the `DataSet` must match the columns defined in the `InsertMultiple` object.

**Example:**
```php
use Staple\Query\InsertMultiple;
use Staple\Query\DataSet;

$insert = new InsertMultiple('users', ['username', 'email']);

$row1 = new DataSet(['username' => 'jdoe', 'email' => 'jdoe@example.com']);
$insert->addRow($row1);

$row2 = new DataSet(['username' => 'asmith', 'email' => 'asmith@example.com']);
$insert->addRow($row2);
```

#### `setColumns(array $columns)`
Sets the list of columns that will be included in the `INSERT` statement.

#### `setData(array $data)`
Sets the entire dataset for the query. The input must be an array of `DataSet` objects.

#### `build()`
Builds and returns the SQL string for the multi-row `INSERT` statement.

**Example:**
```php
echo $insert->build();
/*
INSERT INTO users (username,email) VALUES ('jdoe', 'jdoe@example.com'), ('asmith', 'asmith@example.com')
*/
```

#### `execute()`
Executes the multi-row `INSERT` query on the database. Inherited from `Insert`.
