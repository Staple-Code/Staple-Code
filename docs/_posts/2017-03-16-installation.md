---
layout: document
title: "Installation"
date: 2017-03-16 00:00:00
categories: Introduction
---

## Installation

To create a new site with the STAPLE MVC Framework either download a released version or pull a recent copy of
master or development. Put these files in your base web directory and point your server to the `/public` folder
to start serving a website.

You will need a few things for the server to be able to process your site:

 - PHP 7.0 or higher.
 - A URL rewrite module.

Both IIS (web.config) and Apache (.htaccess) rewrite rules are included in the repository. For nginx, you will
have to add the following in your server configuration for nginx:

```bash
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
