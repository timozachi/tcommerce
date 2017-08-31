<?php

namespace TLib\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

class CPF extends Validator
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

		$value = str_pad(preg_replace('/\\D/', '', trim($value)), 11, '0', STR_PAD_LEFT);

		$valid = false;
		if(
			$value != '00000000000' &&
			$value != '11111111111' &&
			$value != '22222222222' &&
			$value != '33333333333' &&
			$value != '44444444444' &&
			$value != '55555555555' &&
			$value != '66666666666' &&
			$value != '77777777777' &&
			$value != '88888888888' &&
			$value != '99999999999'
		) {
			$valid = true;
			for($t = 9; $t < 11; $t++)
			{
				$sum = 0;
				for($c = 0; $c < $t; $c++)
				{
					$sum += $value[$c] * ($t + 1 - $c);
				}
				$digit = ((10 * $sum) % 11) % 10;

				if($value[$c] != $digit)
				{
					$valid = false;
					break;
				}
			}
		}

		if(!$valid)
		{
			$message = $this->getOption('message');
			if(!$message) $message = 'Field ' . $attribute . ' is not a valid CPF';

			$validation->appendMessage(new Message($message, $attribute, 'CPF'));

			return false;
		}

		return true;
	}

}
