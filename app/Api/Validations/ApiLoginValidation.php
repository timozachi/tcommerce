<?php

namespace TCommerce\Api\Validations;

use Phalcon\Validation\Validator\PresenceOf;
use TLib\Validations\Validators\Integer;
use TLib\Validations\Validators\NotEmpty;

class ApiLoginValidation extends Validation
{

	public function initialize()
	{
		$this->add(
			['id', 'secret'],
			new PresenceOf()
		);

		$this->add(
			['id', 'secret'],
			new NotEmpty()
		);

		$this->add(
			'id',
			new Integer()
		);
	}

}
