<?php
defined('FOLDER_ROOT')
|| define('FOLDER_ROOT', realpath(dirname(__FILE__) . '/../'));

defined('LIBRARY_ROOT')
|| define('LIBRARY_ROOT', FOLDER_ROOT . '/library/');

defined('SITE_ROOT')
|| define('SITE_ROOT', FOLDER_ROOT . '/public/');

defined('TEST_ROOT')
|| define('TEST_ROOT', FOLDER_ROOT . '/tests/Staple/');

defined('APPLICATION_ROOT')
|| define('APPLICATION_ROOT', TEST_ROOT . 'application/');

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

defined('PROVIDER_ROOT')
|| define('PROVIDER_ROOT', APPLICATION_ROOT . 'providers/');

defined('CONTROLLER_ROOT')
|| define('CONTROLLER_ROOT', APPLICATION_ROOT . 'controllers/');

defined('VIEW_ROOT')
|| define('VIEW_ROOT', APPLICATION_ROOT . 'views/');

defined('SCRIPT_ROOT')
|| define('SCRIPT_ROOT',APPLICATION_ROOT . 'scripts/');

defined('STAPLE_ROOT')
|| define('STAPLE_ROOT',LIBRARY_ROOT . 'Staple/');

require_once LIBRARY_ROOT . 'Staple/Alias.php';
require_once LIBRARY_ROOT . 'Staple/Config.php';
require_once LIBRARY_ROOT . 'Staple/Autoload.php';

//Staple AutoLoader
$loader = new \Staple\Autoload(false);

//Register the Autoload class
spl_autoload_register(array($loader, 'load'));