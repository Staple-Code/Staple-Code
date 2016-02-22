<?php
defined('FOLDER_ROOT')
|| define('FOLDER_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

defined('LIBRARY_ROOT')
|| define('LIBRARY_ROOT', FOLDER_ROOT . 'library' . DIRECTORY_SEPARATOR);

defined('SITE_ROOT')
|| define('SITE_ROOT', FOLDER_ROOT . 'public' . DIRECTORY_SEPARATOR);

defined('TEST_ROOT')
|| define('TEST_ROOT', FOLDER_ROOT . 'tests' . DIRECTORY_SEPARATOR);

defined('APPLICATION_ROOT')
|| define('APPLICATION_ROOT', TEST_ROOT . 'Staple' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

defined('MODULES_ROOT')
|| define('MODULES_ROOT', FOLDER_ROOT . 'modules' . DIRECTORY_SEPARATOR);

defined('VENDOR_ROOT')
|| define('VENDOR_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR);

//Setup STAPLE Constants
defined('CONFIG_ROOT')
|| define('CONFIG_ROOT', APPLICATION_ROOT . 'config' . DIRECTORY_SEPARATOR);

defined('LAYOUT_ROOT')
|| define('LAYOUT_ROOT', APPLICATION_ROOT . 'layouts' . DIRECTORY_SEPARATOR);

defined('FORMS_ROOT')
|| define('FORMS_ROOT', APPLICATION_ROOT . 'forms' . DIRECTORY_SEPARATOR);

defined('MODEL_ROOT')
|| define('MODEL_ROOT', APPLICATION_ROOT . 'models' . DIRECTORY_SEPARATOR);

defined('CONTROLLER_ROOT')
|| define('CONTROLLER_ROOT', APPLICATION_ROOT . 'controllers' . DIRECTORY_SEPARATOR);

defined('VIEW_ROOT')
|| define('VIEW_ROOT', TEST_ROOT . 'Staple' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);

defined('SCRIPT_ROOT')
|| define('SCRIPT_ROOT' , FOLDER_ROOT . 'scripts' . DIRECTORY_SEPARATOR);

defined('STAPLE_ROOT')
|| define('STAPLE_ROOT' , LIBRARY_ROOT . 'Staple' . DIRECTORY_SEPARATOR);

require_once LIBRARY_ROOT.'Staple'.DIRECTORY_SEPARATOR.'Alias.class.php';
require_once LIBRARY_ROOT.'Staple'.DIRECTORY_SEPARATOR.'Autoload.class.php';

// For some reason Travis CI can't fully load Twig through composer
// @todo remove this once Travis CI starts acting properly.
require_once VENDOR_ROOT.'twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

//Staple AutoLoader
$loader = new \Staple\Autoload();
$loader->setThrowOnFailure(false);

//Register the Autoload class
spl_autoload_register(array($loader, 'load'));