<?php
return [
	'application'	=> [
		'public_location'		=>	'/',
		'loader'			=>	'', //Default value is blank. Used to specify your own autoloader.
		'throw_on_loader_failure'	=>	true,	//Throws an Exception if the autoloader cannot find a class.
	],

	'page'	=>	[
		'title' 	=>	'STAPLE Code - A PHP 5 Model-View-Controller Framework for Rapid Application Development',
	],

	'session'	=>	[
		//automatically create a session on every call to the framework.
		'auto_create' 		=> 1,
		'max_lifetime' 		=> 1440,


		//Database Session Handler
		//'handler' => 'Staple\Session\DatabaseHandler',
		//'connection' 		=> '',            //if none specified, the default database connection is used.
		//'table' 			=> 'sessions',
		//'encrypt_key' 	=> '',

		//File Session Handler
		'handler' 			=> 'Staple\Session\FileHandler',
		'file_location' 	=> __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp',

		//Redis Session Handler
		//'handler' 			=> 'Staple\Session\RedisHandler',
		//'scheme' 			=> 'tcp',
		//'host' 				=> 'localhost',
		//'port' 				=> '6379',
		//'cafile'			=> '',
		//'password' 			=> '',
		//'encrypt_key' 		=> '',
		//'prefix' 			=> 'session:',
	],

	'layout' => [
		'default' => 'main',
		'scripts' => [
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
			'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js',
		],
		'styles' => [
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
			'https://netdna.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css',
		],
		'meta_keywords' => '',
		'meta_description' => '',
	],

	'forms'	=> [
		//'elementViewAdapter' => 'Staple\Form\ViewAdapters\BootstrapViewAdapter',
		//'elementViewAdapter' => 'Staple\Form\ViewAdapters\FoundationViewAdapter',
	],

	'db' => [
		//'dsn' 		=> '',   //Use this option to create a custom DSN for your database connection
		'driver' 		=> 'mysql',    //specify the database connection type
		'host' 			=> 'localhost',
		'username' 		=> '',
		'password' 		=> '',
		'db' 			=> 'staple',
		//'schema'		=> '',		//For SQL Server you can specify a default schema here.
		//options 		=> [],     //Specify any additional options here
	],

	'auth' => [
		'adapter' 			=> 'Staple\Auth\DBAuthAdapter',
	],

	'oauth'	=> [
		'supported_algs' => ['RS256'],
		'valid_audiences' => [],
		'authorized_iss' => [],
	],

	//Configuration Values for the DBAuthAdapter
	'DBAuthAdapter' => [
		'authtable' 		=> 'accounts',
		'uidfield' 			=> 'username',
		'pwfield' 			=> 'password',
		'rolefield' 		=> 'accountType',
		//'ADDefaultPrivs' => '10',
	],

	'encrypt' => [
		'AES_Key' => '',
		'AES_IV' => '',
	],

	'email' => [
		'html' 		=> 1,
		'from' 		=> 'no-reply@staplecode.org',
		'bcc' 		=> '',
		'server' 	=> '',
		'template' 	=> MODULES_ROOT.'email/template.html',
	],

	'errors' => [
		'devmode' 		=> 1,
		'enable_timer' 	=> 0,
	],

	/*'ActiveDirectory' => [
		'host' 			=> '',
		'domain' 		=> '',
		'baseDN' 		=> '',
		'LDAPSenabled' 	=> '',
		'username' 		=> '',
		'password' 		=> '',
	],*/
];