<?php

use TCommerce\Core\Application;

require_once __DIR__ . '/../base.php';

$application = new Application($di);
$uri = isset($_GET['_url']) ? $_GET['_url'] : explode('?', $_SERVER['REQUEST_URI'])[0];
$response = $application->handle($uri);
$response->send();
