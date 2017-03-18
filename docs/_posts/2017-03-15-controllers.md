---
layout: document
title: "Controllers"
date: 2017-03-15 17:28:56
categories: Components
---

Controllers in STAPLE live in the /application/controllers folder. Controller
classes should extend the `Controller` base object and the name should end
in `Controller` for the autoloading to find the classes.

## Controller Startup `_start()`

The special method `_start()` is called every time that an action is executed
on a controller. This method allows you to add actions that need to occur
before the action is executed. An example would be to switch to a secure
connection (HTTPS).

```php?start_inline=1
class MyController extends Controller
{
    public function _start()
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
will be attemtped to be converted to JSON and returned, otherwise they
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
