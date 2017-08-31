<?php

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use TLib\SmartSession\Adapter\Files as Session;
use TCommerce\Core\Http\API;
use TCommerce\Core\Plugins\Auth as AuthPlugin;
use TCommerce\Core\Plugins\Exception as ExceptionPlugin;
use TCommerce\Core\Security\Auth;

/** @var FactoryDefault $di */
/** @var Config $config */

/** @var View $view */
$view = $di->getShared('view');
$view->setViewsDir($config->application->viewsDir);

/** @var Dispatcher $dispatcher */
$dispatcher = $di->getShared('dispatcher');
$dispatcher->setDefaultNamespace('TCommerce\Admin\Controllers');

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

/**
 * Set up the flash service
 */
$di->set('flash', function ()
{
	return new FlashDirect();
});

/**
 * Set up the flash session service
 */
$di->set('flashSession', function ()
{
	return new FlashSession();
});

/**
 * Setup for api calls
 */
$di->setShared('api', function () use ($config)
{
	return new API($config->api->toArray());
});

/**
 * Setup for api calls
 */
$di->setShared('auth', function () use ($config)
{
	return new Auth();
});

$em = $dispatcher->getEventsManager();

/**
 * Check if the API token is allowed to access certain action using the TokenPlugin
 */
$em->attach('dispatch:beforeException', new ExceptionPlugin());

/**
 * Checks if a user is logged and redirects to login page if not
 */
$em->attach(
	'dispatch:beforeExecuteRoute',
	new AuthPlugin(
		$di->getShared('url')->get(['for' => 'admin-index-login']),
		[
			'index' => ['login', 'loginPost', 'logout'],
			'errors' => ['show404', 'show500']
		]
	)
);