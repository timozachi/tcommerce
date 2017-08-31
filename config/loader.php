<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(
	[
		'TCommerce' => $config->application->appDir,
	    'TLib'      => $config->application->libDir
	]
);

$loader->register();

return $loader;
