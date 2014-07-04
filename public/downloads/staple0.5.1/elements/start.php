<?php
defined('FOLDER_ROOT')
    || define('FOLDER_ROOT', realpath(dirname(__FILE__) . '/../'));

defined('LIBRARY_ROOT')
    || define('LIBRARY_ROOT', FOLDER_ROOT . '/library/');

defined('SITE_ROOT')
    || define('SITE_ROOT', FOLDER_ROOT . '/public/');
    
defined('CONFIG_ROOT')
    || define('CONFIG_ROOT', FOLDER_ROOT . '/application/config/');

defined('FORMS_ROOT')
    || define('FORMS_ROOT', FOLDER_ROOT . '/application/forms/');

defined('PROGRAM_ROOT')
    || define('PROGRAM_ROOT', FOLDER_ROOT . '/application/');

defined('ELEMENTS_ROOT')
	|| define('ELEMENTS_ROOT', FOLDER_ROOT . '/elements/');

include(LIBRARY_ROOT.'Staple/Main.class.php');

$main = Staple_Main::get();
$main->run();