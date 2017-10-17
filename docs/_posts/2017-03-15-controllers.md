---
layout: document
title: "Controllers"
date: 2017-03-15 17:28:56
categories: Components
---

Controllers in STAPLE live in the /application/controllers folder. Controller
classes should extend the `Controller` base object and the name should end
in `Controller` for the autoloader to find the classes.

## Controller Startup `_start()`

The special method `_start()` is called every time that an action is executed
on a controller. This method allows you to add actions that need to occur
before the action is executed. An example would be to switch to a secure
connection (HTTPS). This method needs to be protected to ensure that it is not
accessible directly from the browser.

```php?start_inline=1
class MyController extends Controller
{
    protected function _start()
    {
        // Pre-action code here
    }
}
```

## Stateful Controllers

It is important to know that controller objects are stored in the session
once they are routed to the first time. This means that any properties
attached to a controller class will retain their state between action calls.
This allows the storage of data that will be used across different actions
in the same controller.

## Actions

Any methods in a controller class that are both public and do not have
underscores in the name become routable actions. If during routing the
front controller finds no action name, it will assume the default action
of `index` is to be used.

Method-actions with `camelCased` or `PascalCased` names can be called by
using the proper case or by replacing the uppercase letters with lowercase
versions prefixed with a dash `-`. For example `MyContoller::FirstPage()`
could be routed by either `MyController/FirstPage` or by `my-controller/first-page`.

### Controller Actions

```php?start_inline=1
class MyController extends Controller
{
    // The default action: my-controller/index
    public function index()
    {
        // Do Something
    }

    // Routable by my-controller/page-two
    public function PageTwo()
    {
        // Do Something Else
    }

    private function internalOnly()
    {
        // This function is for internal use of the controller.
    }
}
```

The above controller has two routable actions (`index` and `PageTwo`) and
an internal use function `internalOnly()` that is not routable.

## Action Results

The return of a controller action will determine what the front controller
returns in its response. If the return of a controller-action is a `View`
object, then the view will be rendered and returned to the user. If the
result is a string, the string will be output and returned. Returned objects
will be attempted to be converted to JSON and returned, otherwise they
will be output as a text `var_dump` of the object.

### Return a View

To return the default view object from the controller, create a new `View`
object with no parameters.

```php?start_inline=1
class ClientController extends Controller
{
    public function view($id)
    {
        return View::create();
    }
}
```

### Return a Different View

To return a view other than the default, you can specify the view name when
creating the view object.

```php?start_inline=1
class ReportController extends Controller
{
    public function sales()
    {
        return View::create('print');
    }
}
```

## Communicating Data to Views

To send data to the view, you can return the data with the object itself using
the `data()` method on the `View` object. The data needs to be an associative 
array. 

```php?start_inline=1
class ReportController extends Controller
{
    public function sales()
    {
        return View::create()->data([
            'make'  =>  'Subaru',
            'model' =>  'Outback',
            'sales' =>  1450
        ]);
    }
}
```

In the View itself data is accessible by calling for the key on the `View` object.

```php
<div>
    <?php 
    echo $this->make;
    ?> 
</div>
```

## Authenticating Controller Actions

Staple has an authentication system built directly into the controllers. To use the
authentication mechanism on the each controller you have a few quick options.

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