<?php
defined('FOLDER_ROOT')
    || define('FOLDER_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

defined('LIBRARY_ROOT')
    || define('LIBRARY_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);

defined('SITE_ROOT')
    || define('SITE_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

defined('APPLICATION_ROOT')
    || define('APPLICATION_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

defined('MODULES_ROOT')
	|| define('MODULES_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR);

require_once LIBRARY_ROOT . 'Staple' . DIRECTORY_SEPARATOR . 'Main.class.php';

$main = \Staple\Main::get();
$main->run();