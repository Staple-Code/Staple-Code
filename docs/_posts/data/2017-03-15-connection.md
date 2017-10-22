---
layout: document
title: "Connection"
date: 2017-03-15 17:32:04
categories: Data
---

## The Connection Class

The Connection class extends the PHP PDO objects to allow
for various database connections.

### Default Connection

To initialize the default connection as specified by the `db`
section of the `application.ini` or `application.php` file call:

```php?start_inline=1
$conn = Connection::get();
```

Most query objects will call this method if a different connection
object is not previously specified.

#### Named Connections

A named connection can be specified in the config file under
any name that you would like. You can retrieve and establish
the named connection by referring to the top-level name of the
config attributes.

```php?start_inline=1
$conn = Connection::getNamedConnection('myconn');
```

#### Queries

Execute a query against a database connection object.

```php?start_inline=1
$result = $conn->query('SELECT * FROM customers;');
```

You can also send other query objects to the query method to have
them executed against the database.

```php?start_inline=1
$select = Query::select('customers');
$result = $conn->query($select);
```

The results of a query can be a boolean true or false for queries
that do not have a recordset returned, ex. `UPDATE`,
`INSERT`, `DELETE`. For `SELECT` and `UNION` queries, upon success
a `Statement` object is returned.

### Stored Procedures

You can call stored procedures using the `exec($statement)` method
on the `Connection` class.

```php?start_inline=1
$result = $conn->exec('CALL MySproc()');
```

This will return either a boolean or a `PDOStatement` object depending
the results of the stored procedure.