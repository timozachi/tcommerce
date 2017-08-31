<?php

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;

/**
 * @var Config $config
 * @var FactoryDefault $di
 */
$di->getShared('view')->disable();

/**
 * @var Dispatcher $dispatcher
 */
$dispatcher = $di->getShared('dispatcher');
$dispatcher->setDefaultNamespace('TCommerce\Cli\Tasks');

$em = $dispatcher->getEventsManager();
