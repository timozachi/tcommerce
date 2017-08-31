<?php

namespace TLib\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

class Integer extends Validator
{

	/**
	 * Executes the validation
	 *
	 * @param PhalconValidation $validation
	 * @param string $attribute
	 * @return boolean
	 */
	public function validate(PhalconValidation $validation, $attribute)
	{
		$value = $validation->getValue($attribute);
		$abs = $this->getOption('absolute', false);

		if(
			!filter_var($value, FILTER_VALIDATE_INT) ||
			$abs && (int)$value < 0
		) {
			$message = $this->getOption('message');
			if(!$message) $message = 'Field ' . $attribute . ' does not have a valid ' . ($abs ? 'absolute ' : '') . 'integer format';

			$validation->appendMessage(new Message($message, $attribute, 'Integer'));

			return false;
		}

		return true;
	}

}
