<?php

use Phalcon\Cli\Router;

$router = new Router(false);

//Load module routes
$module_routes = $config->application->appDir . ucfirst($application->module) . '/Config/routes.php';
if(is_readable($module_routes)) require_once $module_routes;

return $router;
