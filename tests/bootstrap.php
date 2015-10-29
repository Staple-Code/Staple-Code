<?php
defined('FOLDER_ROOT')
|| define('FOLDER_ROOT', realpath(dirname(__FILE__) . '/../'));

defined('LIBRARY_ROOT')
|| define('LIBRARY_ROOT', FOLDER_ROOT . '/library/');

defined('SITE_ROOT')
|| define('SITE_ROOT', FOLDER_ROOT . '/public/');

defined('APPLICATION_ROOT')
|| define('APPLICATION_ROOT', FOLDER_ROOT . '/application/');

defined('TEST_ROOT')
|| define('TEST_ROOT', FOLDER_ROOT . '/tests/');

defined('MODULES_ROOT')
|| define('MODULES_ROOT', FOLDER_ROOT . '/modules/');

defined('VENDOR_ROOT')
|| define('VENDOR_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR);

//Setup STAPLE Constants
defined('CONFIG_ROOT')
|| define('CONFIG_ROOT', APPLICATION_ROOT . 'config/');

defined('LAYOUT_ROOT')
|| define('LAYOUT_ROOT', APPLICATION_ROOT . 'layouts/');

defined('FORMS_ROOT')
|| define('FORMS_ROOT', APPLICATION_ROOT . 'forms/');

defined('MODEL_ROOT')
|| define('MODEL_ROOT', APPLICATION_ROOT . 'models/');

defined('CONTROLLER_ROOT')
|| define('CONTROLLER_ROOT', APPLICATION_ROOT . 'controllers/');

defined('VIEW_ROOT')
|| define('VIEW_ROOT', TEST_ROOT . 'Staple/views/');

defined('SCRIPT_ROOT')
|| define('SCRIPT_ROOT',APPLICATION_ROOT . 'scripts/');

defined('STAPLE_ROOT')
|| define('STAPLE_ROOT',LIBRARY_ROOT . 'Staple/');

require_once LIBRARY_ROOT.'Staple/Alias.class.php';
require_once LIBRARY_ROOT.'Staple/Autoload.class.php';
require_once VENDOR_ROOT.'autoload.php';

//Apparently Travis CI can't load files properly. Let's try this:
include VENDOR_ROOT.'twig/twig/lib/Twig/Loader/Array.php';

$loader = new \Staple\Autoload();
$loader->setThrowOnFailure(false);

//Register the Autoload class
spl_autoload_register(array($loader, 'load'));