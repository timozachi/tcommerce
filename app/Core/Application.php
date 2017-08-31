<?php

namespace TCommerce\Core;

use Phalcon\DiInterface;
use Phalcon\Mvc\Application as PhalconApplication;
use Phalcon\Mvc\Router;

class Application extends PhalconApplication
{

	public $modules = [];
	public $module;

	public function __construct(DiInterface $dependencyInjector)
	{
		global $modules;

		parent::__construct($dependencyInjector);

		$this->modules = $modules;
		$this->registerModules($modules);

		$this->_detectModule();
	}

	protected function _detectModule()
	{
		/** @var Router $module_router A router to detect the current module, and set it as default */
		$module_router = new Router(false);
		foreach($this->modules as $module=>$paths)
		{
			if(isset($paths['prefix']))
			{
				$module_router->add(
					"{$paths['prefix']}/(.*)",
					['module' => $module]
				);
			}
		}

		$module_router->handle();
		$this->module = $module_router->getModuleName();
		$this->setDefaultModule($module_router->getModuleName());
	}

}
