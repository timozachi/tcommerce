<?php

/**
 * File under construction, the purpose of this file is to create routes for cli commands (if that's possible)
 */

use Phalcon\Cli\Router;

$router = new Router(false);

//Load module routes
$module_routes = $config->application->appDir . ucfirst($application->module) . '/config/routes.php';
if (is_readable($module_routes)) {
    require_once $module_routes;
}

return $router;
