<?php

return new Phalcon\Config([
	'security' => [
		'encryptKey' => 'cWHx3vRq5WGCbbc6wms1UAA3JbTcJ9Ba',
	],
	'application' => [
		'logsDir'  => $logs_dir = $config->application->logsDir . 'api/'
	],
	'api' => [
		'forceSSL' => false,
		'logRequests' => true,
		'logFile' => $logs_dir . 'apirequests.log'
	],
	'jwt' => [
		'secret' => 'ZOoT3FFgGzHaFBvDvns1afymN8tEwN7n',
		'ttl' => 3600,
		'algo' => 'HS256'
	]
]);
