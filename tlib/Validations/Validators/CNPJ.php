<?php

namespace TLib\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

class CNPJ extends Validator
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
			$value != '00000000000000' &&
			$value != '11111111111111' &&
			$value != '22222222222222' &&
			$value != '33333333333333' &&
			$value != '44444444444444' &&
			$value != '55555555555555' &&
			$value != '66666666666666' &&
			$value != '77777777777777' &&
			$value != '88888888888888' &&
			$value != '99999999999999'
		) {
			$valid = true;
			for($t = 12; $t < 14; $t++)
			{
				$sum = 0;
				for($c = 0; $c < $t; $c++)
				{
					if($t == 12) $mult = $c < 4 ? 5 - $c : (13 - $c);
					else $mult = $c < 5 ? 6 - $c : (14 - $c);
					$sum += $value[$c] * $mult;
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
			if(!$message) $message = 'Field ' . $attribute . ' is not a valid CNPJ';

			$validation->appendMessage(new Message($message, $attribute, 'CNPJ'));

			return false;
		}

		return true;
	}

}
