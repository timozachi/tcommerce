<?php

return new Phalcon\Config([
    'application' => [
        'logsDir'          => $logs_dir = $config->application->logsDir . 'cli/',
    ]
]);
