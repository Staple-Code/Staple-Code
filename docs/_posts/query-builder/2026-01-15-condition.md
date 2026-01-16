---
layout: document
title: "Condition"
date: 2026-01-15 20:35:00
categories: Query
---

## Condition

A class that represents a single SQL condition. It is used to build WHERE and HAVING clauses.

### Constants

- `SQL_AND`: Represents the AND conjunction.
- `SQL_OR`: Represents the OR conjunction.

### Methods

#### `get($column, $operator, $value, $columnJoin, $paramName, $conjunction, $parameterized)`
Factory method to create a new `Condition` object with specified column, operator, and value.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::get('status', '=', 'active');
```

#### `equal($column, $value, $columnJoin, $paramName, $conjunction, $parameterized)`
Creates an equality (`=`) condition. If the value is `NULL`, it automatically uses the `IS` operator.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::equal('id', 123);
// Result: id = :id (with parameter id = 123)
```

#### `notEqual($column, $value, $columnJoin, $paramName, $conjunction, $parameterized)`
Creates a non-equality (`<>`) condition. If the value is `NULL`, it automatically uses the `IS NOT` operator.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::notEqual('status', 'deleted');
// Result: status <> :status
```

#### `like($column, $value, $columnJoin, $paramName, $conjunction, $parameterized)`
Creates a `LIKE` condition for pattern matching.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::like('name', 'John%');
// Result: name LIKE :name
```

#### `notLike($column, $value, $columnJoin, $paramName, $conjunction, $parameterized)`
Creates a `NOT LIKE` condition.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::notLike('email', '%@example.com');
// Result: email NOT LIKE :email
```

#### `null($column, $conjunction)`
Creates an `IS NULL` condition for the specified column.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::null('deleted_at');
// Result: deleted_at IS NULL
```

#### `in($column, $values, $paramName, $conjunction, $parameterized)`
Creates an `IN` condition, allowing the column to match any value in the provided array or `Select` query.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::in('category_id', [1, 2, 3]);
// Result: category_id IN (:category_id_in_1, :category_id_in_2, :category_id_in_3)
```

#### `between($column, $start, $end, $startParamName, $endParamName, $conjunction, $parameterized)`
Creates a `BETWEEN` condition to check if a column value falls within a range.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::between('price', 10, 50);
// Result: price BETWEEN :price_start AND :price_end
```

#### `statement($statement)`
Creates a condition from a raw SQL statement. Use with caution to avoid SQL injection.

**Example:**
```php
use Staple\Query\Condition;

$condition = Condition::statement('DATE(created_at) = CURDATE()');
// Result: (DATE(created_at) = CURDATE())
```
