<?php

use Phalcon\Di\FactoryDefault;

if(!extension_loaded('phalcon'))
{
	throw new \Exception('Phalcon extension not loaded');
}

error_reporting(E_ALL);
set_time_limit(600);

define('APP_PATH', __DIR__);

$init_time = microtime(true);

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack
 * @var FactoryDefault $di
 */
if(php_sapi_name() === 'cli') {
	$di = new FactoryDefault\Cli();
} else {
	$di = new FactoryDefault();
}

if(is_readable(APP_PATH . '/vendor/autoload.php')) {
	require_once APP_PATH . '/vendor/autoload.php';
}

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

/** Start autoloading */
$di->getLoader();
