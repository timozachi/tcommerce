<?php

namespace TCommerce\Api\Validations;

use Phalcon\Validation as PhalconValidation;

class Validation extends PhalconValidation
{

	protected static $_createWhiteList = [

	];

	protected static $_updateWhiteList = [

	];

	const CREATE = 'create';
	const UPDATE = 'update';

	public $mode = self::CREATE;

	public function getWhiteList()
	{
		if($this->mode == static::CREATE) {
			return static::$_createWhiteList;
		}

		return static::$_updateWhiteList;
	}

}
