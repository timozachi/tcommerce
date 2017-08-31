<?php

namespace TLib\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

/**
 * TLib\Validations\Validators\NotEmpty
 *
 * Validates if a value is not empty, trimming it if necessary
 */
class NotEmpty extends Validator
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
		$value = ($this->getOption('trim', true) && is_string($value)) ? trim($value) : $value;

		if(empty($value))
		{
			$message = $this->getOption('message');
			if(!$message) $message = 'Field ' . $attribute . ' cannot be empty';

			$validation->appendMessage(new Message($message, $attribute, 'NotEmpty'));

			return false;
		}

		return true;
	}

}
