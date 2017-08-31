<?php

namespace TCommerce\Api\Validations;

use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use TLib\Validations\Validators\Integer;

class LoginValidation extends Validation
{

	public function validate($data = null, $entity = null)
	{
		$this->add(
			['email', 'password'],
			new PresenceOf(['cancelOnFail' => true])
		);
		$this->add(
			'email',
			new Email()
		);

		if(array_key_exists('lifetime', $data))
		{
			$this->add(
				'lifetime',
				new Integer(['absolute' => true])
			);
		}

		return parent::validate($data, $entity);
	}

}
