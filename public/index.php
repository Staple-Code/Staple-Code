<?php
defined('FOLDER_ROOT')
    || define('FOLDER_ROOT', realpath(dirname(__FILE__) . '/../'));

defined('LIBRARY_ROOT')
    || define('LIBRARY_ROOT', FOLDER_ROOT . '/library/');

defined('SITE_ROOT')
    || define('SITE_ROOT', FOLDER_ROOT . '/public/');

defined('PROGRAM_ROOT')
    || define('PROGRAM_ROOT', FOLDER_ROOT . '/application/');

defined('MODULES_ROOT')
	|| define('MODULES_ROOT', FOLDER_ROOT . '/modules/');

require_once LIBRARY_ROOT.'Staple/Main.class.php';

$main = \Staple\Main::get();
$main->run();