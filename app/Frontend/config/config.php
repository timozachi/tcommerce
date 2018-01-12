<?php

return new Phalcon\Config([
	'application' => [
		'logsDir'          => $logs_dir = $config->application->logsDir . 'frontend/',
		'viewsDir'         => $config->application->appDir . 'Frontend/views/'
	],
	'api' => [
		'logFile'          => $logs_dir . 'apicalls.log',
		'longCallsLogFile' => $logs_dir . 'apilongcalls.log'
	]
]);