<?php

use TLib\Utils\Stringify;
use Phalcon\Cache\Backend;
use Phalcon\Cache\Frontend\Data as FrontendCache;
use Phalcon\Cli\Dispatcher as CliDispatcher;
use Phalcon\Config;
use Phalcon\Db\Adapter as DbAdapter;
use Phalcon\Db\Profiler;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

/**
 * @var FactoryDefault $di
 */

/**
 * Shared configuration service
 */

$di->setShared('config', function ()
{
	return include APP_PATH . '/config/config.php';
});

/**
 * Shared loader service
 */
$di->setShared('loader', function ()
{
	$config = $this->getConfig();

	return include APP_PATH . '/config/loader.php';
});

/**
 * Set router
 */
$di->setShared('router', function ()
{
	/** @var Config $config */
	$config = $this->getConfig();

	global $modules, $application;

	/**
	 * Include Routes
	 */
	if(php_sapi_name() === 'cli') {
		$router = include APP_PATH . '/config/cli-routes.php';
	} else {
		$router = include APP_PATH . '/config/routes.php';
	}

	return $router;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function ()
{
    $url = new UrlResolver();
    $url->setBaseUri($this->getConfig()->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function ()
{
	$config = $this->getConfig();

	$view = new View();
	$view->registerEngines(
		[
			'.volt' => function ($view, $di) use ($config)
			{
				$volt = new VoltEngine($view, $di);

				$volt->setOptions([
					'compiledPath' => $config->application->storageDir . 'app/views/',
					'compiledSeparator' => '_',
				    'compileAlways' => $config->templating->compileAlways
				]);

				return $volt;
			}
		]
	);
	$view->setRenderLevel(View::LEVEL_ACTION_VIEW);

	return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function ()
{
	$config = $this->getConfig();

	$db_config = $config->database;
	$class = 'Phalcon\Db\Adapter\Pdo\\' . $db_config->adapter;
	/** @var Phalcon\Db\Adapter\Pdo $connection */
	$connection = new $class($db_config->toArray());

	if(!empty($db_config->logQueries))
	{
		$logger = new Logger(
			$config->application->logsDir . $db_config->logFile,
			['mode' => 'a']
		);
		$profiler = new Profiler();

		$em = new EventsManager();

		$stringify = new Stringify(['max_array_elements' => 20]); $microtime = null;
		$em->attach('db', function (Event $event, DbAdapter $connection) use ($db_config, $logger, $stringify, $profiler)
		{
			if($event->getType() == 'beforeQuery')
			{
				$profiler->startProfile(
					$connection->getSQLStatement(),
					$connection->getSqlVariables(),
					$connection->getSQLBindTypes()
				);
			}
			elseif($event->getType() == 'afterQuery')
			{
				$profiler->stopProfile();
				$profile = $profiler->getLastProfile();

				$logger->info(
					"\nSQL: " . $profile->getSQLStatement() .
					"\nPARAMS: " . $stringify->variable($profile->getSqlVariables()) . "\n" .
					"TIME: " . round($profile->getTotalElapsedSeconds() * 1000, 2) . "\n" .
					($db_config->logTrace ? "TRACE:\n" . $stringify->backtrace() : '')
				);
			}
		});

		$connection->setEventsManager($em);
	}

	return $connection;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function ()
{
	if(false && extension_loaded('apcu')) {
		/** @todo Criar classe TLib\Mvc\Model\Metadata\Apcu */
		return new TLib\Mvc\Model\Metadata\Apcu();
	} elseif(extension_loaded('apc')) {
		return new Phalcon\Mvc\Model\Metadata\Apc();
	}

    return new Phalcon\Mvc\Model\Metadata\Memory();
});

/*
 * Set the models cache service
 */
$di->set('modelsCache', function ()
{
	/*
	 * Cache data for 30 minutes by default
	 */
	$frontCache = new FrontendCache(
		['lifetime' => 1800]
	);

	if(false && extension_loaded('apcu')) {
		/** @todo Criar classe TLib\Cache\Backend\Apcu */
		return new TLib\Cache\Backend\Apcu();
	} elseif(extension_loaded('apc')) {
		return new Backend\Apc($frontCache);
	}

	return new Backend\Memory($frontCache);
});

$di->set('log', function ()
{
	$logger = new Logger(
		$this->getConfig()->application->logsDir . 'default.log',
		['mode' => 'a']
	);

	return $logger;
});

/**
 * We register the events manager
 */
$di->setShared('dispatcher', function ()
{
	if(php_sapi_name() === 'cli') {
		$dispatcher = new CliDispatcher();
	} else {
		$dispatcher = new Dispatcher();
	}
	$dispatcher->setEventsManager(new EventsManager());

	return $dispatcher;
});
