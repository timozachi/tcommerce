<?php

namespace TCommerce\Core;

use Phalcon\Cli\Console as PhalconApplication;
use Phalcon\DiInterface;

class CliApplication extends PhalconApplication
{

	public $modules = [];
	public $module = 'cli';

	public function __construct(DiInterface $dependencyInjector = null)
	{
		parent::__construct($dependencyInjector);

		global $modules;

		$this->modules = $modules;
		$this->registerModules($modules);
		$this->setDefaultModule($this->module);
	}

}
