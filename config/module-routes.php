<?php

/**
 * This file only loads the current module's routes instead of all routes from all modules
 */

use Phalcon\Mvc\Router;

$router = new Router(false);
$router->removeExtraSlashes(true);

$router->notFound([
	'controller' => 'errors',
	'action'     => 'show404'
]);

//Load module routes
$module_routes = $config->application->appDir . ucfirst($application->module) . '/Config/routes.php';
if(is_readable($module_routes)) require_once $module_routes;

return $router;
