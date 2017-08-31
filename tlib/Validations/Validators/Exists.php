<?php

namespace TLib\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

class Exists extends Validator
{

	/**
	 * @param PhalconValidation $validation
	 * @param string $attribute
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function validate(PhalconValidation $validation, $attribute)
	{
		$value = $validation->getValue($attribute);

		$model = $this->getOption('model');
		if(is_array($model))
		{
			if(isset($model[$attribute])) $model = $model[$attribute];
			else $model = null;
		}
		if(empty($model)) throw new \Exception('model option is required for Exists validator');

		$field = $this->getOption('field');
		if(is_array($field))
		{
			if(isset($field[$attribute])) $field = $field[$attribute];
			else $field = null;
		}

		try {
			$exists = true;
			$rm = new \ReflectionMethod($model, 'exists');
		} catch(\Exception $e) {
			$exists = false;
		}

		$valid = false;
		if($exists && $rm->isStatic() && $rm->isPublic())
		{
			$call = [$value];
			if(!empty($field)) $call[] = $field;

			if(call_user_func_array("$model::exists", $call)) $valid = true;
		}
		else
		{
			if(empty($field)) $field = 'id';
			/** 
			 * This assumes a valid model was passed, all Phalcon models have the findFirst method
			 */
			$exists = call_user_func(
				"$model::findFirst",
				[
					'columns' => ["[$field]"],
					'conditions' => "[{$field}] = :value:",
					'bind' => ['value' => $value]
				]
			);

			$valid = $exists ? true : false;
		}

		if(!$valid)
		{
			$message = $this->getOption('message');
			if(!$message) $message = 'Inexistent ' . $attribute . ' value';

			$validation->appendMessage(new Message($message, $attribute, 'Exists'));

			return false;
		}

		return true;
	}

}