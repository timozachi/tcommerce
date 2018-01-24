<?php

use Phalcon\Mvc\Dispatcher;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;

/**
 * @var Config $config
 * @var FactoryDefault $di
 */

/**
 * @var Dispatcher $dispatcher
 */
$dispatcher = $di->getShared('dispatcher');
$dispatcher->setDefaultNamespace('TCommerce\Cli\Tasks');
$dispatcher->setNamespaceName('TCommerce\Cli\Tasks');

$em = $dispatcher->getEventsManager();
