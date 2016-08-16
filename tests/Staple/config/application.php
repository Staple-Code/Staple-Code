<?php
return [
	'application'	=> [
		'public_location' 	=>	'/',
		'loader' 			=>	'', //Default value is blank. Used to specify your own autoloader.
	],

	'page'	=>	[
		'header' 	=>	'', //Deprecated - MODULES_ROOT'header.php'
		'footer' 	=>	'', //Deprecated - MODULES_ROOT'footer.php'
		'title' 	=>	'STAPLE Code - A PHP 5 Model-View-Controller Framework for Rapid Application Development',
	],

	'session'	=>	[
		//automatically create a session on every call to the framework.
		'auto_create'	=>	1,
		'max_lifetime'	=>	1440,

		//Array Session Handler
		'handler' 		=>	'Staple\Session\ArrayHandler',
	],

	'layout'	=>	[
		'default' 			=>	'bootstrap',
		'scripts'			=>	[
			'/bootstrap/js/bootstrap.min.js',
			'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
			],
		'styles'			=>	['/bootstrap/css/bootstrap.min.css'],
		'meta_keywords' 	=>	'',
		'meta_description' 	=>	'',
	],

	'forms'	=>	[
		'elementViewAdapter' => NULL,	//BootstrapViewAdapter and FoundationViewAdapter are included with the framework
	],

	'db'	=>	[
		'dsn'		=>	'sqlite::memory:',   		//Use this option to create a custom DSN for your database connection
		'driver'	=>	'sqlite',    	//specify the database connection type
		'host'		=> 	'localhost',
		'username'	=>	NULL,
		'password'	=>	NULL,
		'db'		=>	'staple',
		'options'	=>	[],     		//Specify any additional options here
	],

	'auth'	=>	[
		'enabled'			=>	0,
		'adapter'			=>	'DBAuthAdapter',
		'controller'		=>	'accountController',
		'authtable'			=>	'accounts',
		'uidfield'			=>	'username',
		'pwfield'			=>	'password',
		'rolefield'			=>	'accountType',
		'pwenctype'			=>	'SHA1',
		'allowedRoute'		=>	['index/index'],
		//ADDefaultPrivs	=>	'10',
	],

	'encrypt'	=>	[
		'AES_Key'	=>	NULL,
		'AES_IV'	=>	NULL,
	],

	'email'	=>	[
		'html'		=>	1,
		'from'		=>	'no-reply@staplecode.org',
		'bcc'		=>	NULL,
		'server'	=>	NULL,
		'template'	=>	MODULES_ROOT.'email/template.html',
	],

	'errors'	=>	[
		'devmode'		=>	1,
		'enable_timer'	=>	0,
	],

	'ActiveDirectory'	=>	[
		'host'			=>	NULL,
		'domain'		=>	NULL,
		'baseDN'		=>	NULL,
		'LDAPSenabled'	=>	NULL,
		'username'		=>	NULL,
		'password'		=>	NULL,
	],
];