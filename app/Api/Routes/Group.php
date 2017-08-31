<?php

namespace TCommerce\Api\Routes;

use Phalcon\Mvc\Router\Group as RouterGroup;

class Group extends RouterGroup
{

	public function setPrefix($prefix, $version = 'v1')
	{
		global $modules;

		return parent::setPrefix(rtrim($modules['api']['prefix'], '/') . "/{$version}" . $prefix);
	}

}
