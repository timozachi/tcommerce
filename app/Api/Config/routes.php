<?php

/** @var Phalcon\Mvc\Router $router */

use TCommerce\Api\Routes\Auth;
use TCommerce\Api\Routes\Products;
use TCommerce\Api\Routes\States;
use TCommerce\Api\Routes\Users;

$router->mount(new Auth());

$router->mount(new Products());

$router->mount(new States());

$router->mount(new Users());