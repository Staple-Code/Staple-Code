<?php
use Staple\Main;

include_once '../vendor/autoload.php';

//Load Environment Variables
$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
$dotenv = Dotenv\Dotenv::createImmutable($path);
$dotenv->safeLoad();

//Run the Application
$main = Main::get();
$main->run();