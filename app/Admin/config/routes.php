<?php

/** @var Phalcon\Mvc\Router $router */

use TCommerce\Admin\Routes\Dashboard;
use TCommerce\Admin\Routes\Index;

$router->mount(new Index());

$router->mount(new Dashboard());
