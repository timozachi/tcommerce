<?php

namespace TCommerce\Admin\Routes;

use Phalcon\Mvc\Router\Group as RouterGroup;

class Group extends RouterGroup
{

	public function setPrefix($prefix)
	{
		global $modules;

		return parent::setPrefix(rtrim($modules['admin']['prefix'], '/') . $prefix);
	}

}
