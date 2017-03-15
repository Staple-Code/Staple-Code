---
layout: document
title:  "Getting Started"
date:   2017-03-10 14:35:52 -0500
categories: Introduction
---
## Getting Started

To start your new site, the first file you will want to work with is the included `indexController.php` in
`/application/controllers`. This file is the default homepage for your website. and it looks like this:

```
use Staple\Controller;

/** ... */
class indexController extends Controller
{
	public function _start()
	{
		// Controller Startup Code
	}

	public function index()
	{
		// Index Action Code Goes Here.
	}
}
```

You will place new controllers in the `application/controllers` folder. Inside of each controller a new
controller action is created by making a public function that only consists of letters and numbers.

## New Controller Action

To create a new action on a controller, create a new public method that consists of only letters and numbers
for the method name.

```
class indexController extends Controller
{
    ...
    // Accessible from /index/my-action
    public function myAction()
    {
        echo "Hello World";
    }
}
```

The new action is accessible from `/index/my-action` relative to the root of your web directory.
