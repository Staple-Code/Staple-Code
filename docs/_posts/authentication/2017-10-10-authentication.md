---
layout: document
title: "Integrated Authentication"
date: 2017-10-10 17:28:56
categories: Authentication
---

## Integrated Authentication

Staple includes a built in authentication mechanism for your applications. To use the
built in authentication you can choose from one of the prebuilt `AuthAdapter` classes
or you can create your own.

## Authenticating

To authenticate with the application you first need to grab the application instance
of the `Auth` object.

```php?start_inline=1
$auth = Auth::get();
```

### Performing Authentication

Once you have an instance of the `Auth` object you can then perform authentication
with your selected `AuthAdapter` by calling the `doAuth()` method on the `Auth` 
object and sending some credentials.

```php?start_inline=1
$auth->doAuth([
    'username'  =>  'test',
    'password'  =>  'test'
]);
```

These credentials could be an array, an object, or whatever else that the 
`AuthAdapter` might be expecting.

### Checking Authentication

You can check authentication status anywhere within your app by calling the 
`isAuthed()` method on the `Auth` object.

```php?start_inline=1
$authed = Auth::get()->isAuthed(); //Returns a booleon true or false.
```

### Clearing Authentication

To clear your current authentication call the `clearAuth()` method on the `Auth` 
object.

```php?start_inline=1
Auth::get->clearAuth();
```

## Protected Actions

For authentication to really be effective you need to protect the action in your
application. If you have not protected any actions in your app then everything is
available to anyone that can access you application.

### Protect Entire Controllers

To protect every route in a single `Controller` you can add the `@protected` notation
in the Controller's opening comment section.

```php?start_inline=1
/**
 * This controller has secure data inside.
 * @protected
 */
class SecureController extends Controller
{
    public function account()
    {
        return View::create();
    }
}
```

All current routes and any future routes will now require authentication before they
can be called.

### Protect Single Actions

If you have a `Controller` which will have mixed content, both secure and insecure,
you can add the `@protected` notation to the comments above the action itself.

```php?start_inline=1
class DataController extends Controller
{
    /**
     * This data is public
     */
    public function publicData()
    {
        return View::create();
    }
    
    /**
     * You must authenticate to access this data.
     * @protected
     */
    public function secureData()
    {
        return View::create();
    }
}
```

### Exclude Actions from Authentication

You can also exclude actions from a global controller protection by adding the
`@open` notation to a specific action.

```php?start_inline=1
/**
 * This controller has secure data inside.
 * @protected
 */
class DataController extends Controller
{
    /**
     * This data is public
     * @open
     */
    public function publicData()
    {
        return View::create();
    }
    
    /**
     * You must authenticate to access this data.
     */
    public function secureData()
    {
        return View::create();
    }
}
```