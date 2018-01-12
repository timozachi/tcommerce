<?php

global $modules;

$modules = [
	//The module without a prefix must be the first module
	'frontend' => [
		'className' => 'TCommerce\Frontend\Module',
	    'path' => APP_PATH . '/app/Frontend/Module.php',
		'prefix' => ''
	],
	'admin' => [
		'className' => 'TCommerce\Admin\Module',
		'path' => APP_PATH . '/app/Admin/Module.php',
		'prefix' => '/admin'
	],
	'api' => [
		'className' => 'TCommerce\Api\Module',
		'path' => APP_PATH . '/app/Api/Module.php',
	    'prefix' => '/api'
	],
	'cli' => [
		'className' => 'TCommerce\Cli\Module',
		'path' => APP_PATH . '/app/Cli/Module.php'
	]
];

$folder = '/';
if (! empty($_SERVER['DOCUMENT_ROOT']))
{
	$folder = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath('.'));
	$folder = str_replace([DIRECTORY_SEPARATOR, '/public'], ['/', ''], $folder);
	if($folder != '/') { $folder .= '/'; }
}

$config = new Phalcon\Config([
	//Values are prod, dev
	'env' => 'prod',
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'tcommerce',
        'charset'     => 'utf8mb4',
        'logQueries'  => false,
        'logFile'     => 'queries.log',
        'logTrace'    => false
    ],
    'application' => [
	    'appDir'      => APP_PATH . '/app/',
	    'libDir'      => APP_PATH . '/tlib/',
	    'configDir'   => $config_dir = APP_PATH . '/config/',
        'storageDir'  => $storage_dir = APP_PATH . '/storage/',
        'appDataDir'  => $app_data_dir = $storage_dir . 'app/data/',
		'sessionsDir' => $storage_dir . 'app/sessions',
	    'logsDir'     => $logs_dir = $storage_dir . 'logs/',
        'baseUri'     => $base_uri = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' .
			(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . $folder,
		'forceSSL'    => false
    ],
	'templating' => [
		'compileAlways'    => false
	],
	'api' => [
		'baseUri'          => $base_uri . 'api/v1',
		'id'               => 1,
		'secret'           => 'TATKDD94315970267CE8A8BB9D94875618A2FFBD53D2',
		'tokenFile'        => $app_data_dir . 'apitoken.json',
		'verifyPeer'       => true,
		'timeout'          => 10,
		'logCalls'         => true,
		'logTrace'         => false,
		'logFile'          => $logs_dir . 'apicalls.log',
		'logLongCalls'     => true,
		'longCallsTimeout' => 2,
		'longCallsLogFile' => $logs_dir . 'apilongcalls.log'
	],
	'cache' => [
		'adapter' => 'Memory',
		'data' =>  null
		/*
		//Use this option for redis adapter
		'data' => [
			'host'       => '120.0.0.1',
			'port'       => 6379
		]
		*/
	]
]);
if (is_readable($config_dir . 'config.env.php')) {
	$config->merge(include $config_dir . 'config.env.php');
}

return $config;
