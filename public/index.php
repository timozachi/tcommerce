<?php

use TCommerce\Core\Application;

require_once __DIR__ . '/../base.php';

$application = new Application($di);
$response = $application->handle()->send();
