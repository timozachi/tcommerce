<?php

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Session\Adapter\Files as Session;
use TCommerce\Api\Plugins\Exception as ExceptionPlugin;
use TCommerce\Api\Plugins\Auth as AuthPlugin;
use TCommerce\Api\Plugins\Log as LogPlugin;
use TCommerce\Api\Security\Auth;

/**
 * @var Config $config
 * @var FactoryDefault $di
 */

$di->getShared('view')->disable();

/**
 * @var Dispatcher $dispatcher
 */
$dispatcher = $di->getShared('dispatcher');
$dispatcher->setDefaultNamespace('TCommerce\Api\Controllers');

$em = $dispatcher->getEventsManager();

/**
 * Check if the API token is allowed to access certain action using the TokenPlugin
 */
$em->attach('dispatch:beforeException', new ExceptionPlugin());

/**
 * Check if the API token is allowed to access certain action using the TokenPlugin
 */
$em->attach('dispatch', new AuthPlugin());

if($config->api->logRequests) {
	$em->attach('dispatch:afterDispatchLoop', new LogPlugin());
}

/**
 * Setup for api calls
 */
$di->setShared('auth', function () use ($config)
{
	return new Auth();
});

/**
 * Set up the session service
 */
$di->setShared('session', function () use ($config)
{
	ini_set('session.save_path', $config->application->sessionsDir);

	$session = new Session();
	$session->setName('tcommerce-admin');
	$session->start();

	return $session;
});
