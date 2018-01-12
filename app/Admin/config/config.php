<?php

return new Phalcon\Config([
	'application' => [
		'logsDir'          => $logs_dir = $config->application->logsDir . 'admin/',
		'viewsDir'         => $config->application->appDir . 'Admin/views/'
	],
	'api' => [
		'logFile'          => $logs_dir . 'apicalls.log',
		'longCallsLogFile' => $logs_dir . 'apilongcalls.log'
	]
]);
