# STAPLE-Code (PHP MVC Framework)
![Build Status](https://travis-ci.org/Staple-Code/Staple-Code.svg?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/41ca2c4097d049e29e1e44a77141f94d)](https://www.codacy.com/app/contact_8/Staple-Code?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ironpilot/Staple-Code&amp;utm_campaign=Badge_Grade)

## Install

To create a new site with the STAPLE MVC Framework either download a released version or pull a recent copy of
master or development. Put these files in your base web directory and point your server to the `/public` folder
to start serving a website.

You will need a few things for the server to be able to process your site:

 - PHP 7.0 or higher.
 - A URL rewrite module.

Both IIS (web.config) and Apache (.htaccess) rewrite rules are included in the repository. For nginx, you will
have to add the following in your server configuration for nginx:

```
location / {
   index  index.php index.html index.htm;
   try_files $uri $uri/ @staple;
}

location @staple
{
    rewrite ^(.*)$ /index.php last;
}
```

## Composer

STAPLE also has support for composer. It has no dependencies out of the gate, so composer is an optional feature
to add any dependencies that you might require. Simply add the dependencies to the included composer.json file.

More information about composer can be found here: https://getcomposer.org/

## Getting Started

To start your new site, the first file you will want to work with is the included `indexController.php` in
`/application/controllers`. This file is the default homepage for your website. and it looks like this:

```php
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

#### New Controller Action

To create a new action on a controller, create a new public method that consists of only letters and numbers
for the method name.

```php
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

## License

GNU Lesser GPLv3, See LICENSE file for the license contents.
