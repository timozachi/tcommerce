<?php

use Phalcon\Di\FactoryDefault;

define('TCOMMERCE_START', microtime(true));

if (! extension_loaded('phalcon')) {
	throw new Exception('Phalcon extension is not loaded, please install phalcon');
}

set_time_limit(600);

define('APP_PATH', __DIR__);

if (is_readable(APP_PATH . '/vendor/autoload.php')) {
	require_once APP_PATH . '/vendor/autoload.php';
}

/**
 * Shared configuration service
 */
$config = include APP_PATH . '/config/config.php';

// Debug component if environment is not production, errors that happen before this line of code
// will not be displayed in debug form
if ($config->env !== 'prod')
{
    $debug = new Phalcon\Debug();
    $debug->listen();
}

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack
 * @var FactoryDefault $di
 */
if (php_sapi_name() === 'cli') {
    $di = new FactoryDefault\Cli();
} else {
    $di = new FactoryDefault();
}

$di->setShared('config', $config);

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

/** Start autoloading */
$di->getLoader();
