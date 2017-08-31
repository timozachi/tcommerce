<?php

namespace TCommerce\Validations\Validators;

use Phalcon\Validation\Validator;
use Phalcon\Validation as PhalconValidation;
use Phalcon\Validation\Message;

class Image extends Validator
{

	public function validate(PhalconValidation $validation, $attribute)
	{
		$value = $validation->getValue($attribute);
		if($this->getOption('base64', true)) {
			$value = base64_decode($value);
		}
		$types = $this->getOption('types', [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF]);
		if(!is_array($types) || empty($types)) {
			throw new \Exception('Types must be an array');
		}

		$info = @getimagesizefromstring($value);
		if(
			$info === false ||
			!in_array($info[2], $types, true)
		) {
			$message = $this->getOption(
				'message',
				'Field %s is not a valid image. Accepted types are: ' . $this->_getTypeNames($types)
			);
			if(strpos($message, '%s') !== false) {
				$message = sprintf($message, $validation->getLabel($attribute));
			}

			$validation->appendMessage(
				new Message($message, $attribute, 'Image')
			);

			return false;
		}

		return true;
	}

	protected function _getTypeNames(array $types)
	{
		$type_names = ''; $comma = '';
		foreach($types as $type)
		{
			switch($type)
			{
				case IMAGETYPE_JPEG: $type_name = 'jpeg'; break;
				case IMAGETYPE_PNG: $type_name = 'png'; break;
				case IMAGETYPE_GIF: $type_name = 'gif'; break;
				case IMAGETYPE_BMP: $type_name = 'bmp'; break;
				case IMAGETYPE_ICO: $type_name = 'ico'; break;
				default:
					$type_name = $type;
			}

			$type_names .= $comma . $type_name;

			$comma = ', ';
		}

		return $type_names;
	}

}