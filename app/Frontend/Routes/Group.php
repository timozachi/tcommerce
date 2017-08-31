<?php

namespace TCommerce\Frontend\Routes;

use Phalcon\Mvc\Router\Group as RouterGroup;

class Group extends RouterGroup
{

	public function setPrefix($prefix)
	{
		global $modules;

		return parent::setPrefix(rtrim($modules['frontend']['prefix'], '/') . $prefix);
	}

}
