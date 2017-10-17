---
layout: document
title: "Authentication Adapters"
date: 2017-03-15 17:28:56
categories: Authentication
---

## Authentication Adapters

Authentication in Staple relies on Authentication Adapters. These are small classes that
create a bridge between the authentication system and a mechanism for authenticating
requests to the application.

### Building Your Own Auth Adapter

All Auth Adapters extend from the base `AuthAdapter` interface.

```php?start_inline=1
class MyAuthAdapter implments AuthAdapter
{
    //Contents
}
```

This adds three functions that must be implemented by the Auth Adapter code:



#### `getAuth($credentials): bool` Method

This method accepts credentials for the parameter and then returns a boolean true or
false to determine whether those credentials will perform a successful authentication.

```php?start_inline=1
public function getAuth($credentials): bool;
```

#### `getLevel()` Method

The `getLevel()` method returns the authenticated user's level. Which is useful
for routes that have varied access levels, such as a client vs an administrator.

```php?start_inline=1
public function getLevel();
```

#### `getUserId()` Method

The `getUserId()` method returns the authenticated user's identifier. This can be
an integer, string, array or even an object depending on your use case.

```php?start_inline=1
public function getUserId();
```

For more implementation details refer to the framework's `DBAuthAdapter`.