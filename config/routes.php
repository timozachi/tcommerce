<?php

/**
 * This file loads all the routes for all modules. This might be necessary if you wish
 * to generate routes from other modules
 */

use Phalcon\Mvc\Router;

$router = new Router(false);
$router->removeExtraSlashes(true);

foreach($modules as $module=>$paths)
{
	//Load module routes
	$module_routes = $config->application->appDir . ucfirst($module) . '/Config/routes.php';
	if(is_readable($module_routes)) require_once $module_routes;
}

$router->notFound([
	'controller' => 'errors',
	'action'     => 'show404'
]);

return $router;
