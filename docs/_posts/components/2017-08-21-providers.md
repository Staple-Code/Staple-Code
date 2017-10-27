---
layout: document
title: "Providers"
date: 2017-03-15 17:28:56
categories: Components
---

Providers in STAPLE live in the /application/providers folder. Provider
classes should extend the `RestfulController` base object and the name should end
in `Provider` for the autoloader to find the classes.

## Provider Startup `_start()`

The special method `_start()` is called every time that an action is executed
on a controller. This method allows you to add actions that need to occur
before the action is executed. An example could be to switch to add any response
headers that are needed.

```php?start_inline=1
class MyProvider extends RestfulController
{
    public function _start()
    {
        header('Access-Control-Allow-Origin: http://foo.example');
    }
}
```

## Stateless Providers

Unlike Controllers the Providers are stateless. They will not save their properties
between requests, which is normal for a RESTful style application flow.

## Actions

Action methods in a `Provider` class are prefixed with the HTTP verb that they should 
respond to. For example `getClient()` would respond to a GET request and `postClient()`
Would respond to the post request. If there is no action name, the default action of
`index` will be used: `getIndex()`, `postIndex()` 

Method-actions with `camelCased` or `PascalCased` names can be called by
using the proper case or by replacing the uppercase letters with lowercase
versions prefixed with a dash `-`. For example `MyProvider::getCustomerList()`
could be routed by either `MyProvider/CustomerList` or by `my-provider/customer-list`.

### Controller Actions

```php?start_inline=1
class MyProvider extends RestfulController
{
    // Example resource URI: GET my-provider/client/1
    public function getClient($id)
    {
        // GET request to find the client with $id for an identifier.
    }

    // Routable by POST /my-provider/client
    public function postClient()
    {
        // POST request to add clients to the system
    }
    
    // PUT /my-provider/client/1
    public function putClient($id)
    {
        // PUT request to update the client resouce with the provided data.
    }

    private function internalOnly()
    {
        // This function is for internal use of the provider.
    }
}
```

The above provider provides create, update and retrieve functionality for `client`
resources. It also shows the use of an internal use function `internalOnly()` that
accessible outside of the class.

## Action Results

The return of a provider action will determine what the front controller
returns in its response. If the return of a controller-action is a `View`
object, then the view will be rendered and returned to the user. If the
result is a string, the string will be output and returned. Returned objects
will be attempted to be converted to JSON and returned, otherwise they
will be output as a text `var_dump` of the object.