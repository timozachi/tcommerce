<?php

namespace TCommerce\Core;

use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

	protected $_moduleDir = __DIR__;

	/**
	 * Registers an autoloader related to the module
	 *
	 * @param mixed $dependencyInjector
	 */
	public function registerAutoloaders(DiInterface $dependencyInjector = null)
	{
		
	}

	/**
	 * Registers services related to the module
	 *
	 * @param mixed $dependencyInjector
	 */
	public function registerServices(DiInterface $dependencyInjector = null)
	{
		/** @var \Phalcon\Di\FactoryDefault $di */
		$di = $dependencyInjector;

		/** @var \Phalcon\Config $config */
		$config = $di->getConfig();

		if(is_readable($this->_moduleDir . '/Config/config.php')) {
			$config->merge(require_once $this->_moduleDir . '/Config/config.php');
		}

		if(is_readable($this->_moduleDir . '/Config/config.env.php')) {
			$config->merge(include $this->_moduleDir . '/Config/config.env.php');
		}

		if(is_readable($this->_moduleDir . '/Config/services.php')) {
			require_once $this->_moduleDir . '/Config/services.php';
		}
	}

}
