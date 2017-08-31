<?php

namespace TLib\Validations\Messages;

use Phalcon\Validation\Message\Group;

class Parser
{

	/**
	 * Turn a group of messages to an array with $field key and values as string messages
	 *
	 * @param Group $messages
	 * @return array
	 */
	public static function parse(Group $messages)
	{
		$errors = [];

		foreach($messages as $message)
		{
			$fields = $message->getField();
			if(!is_array($fields)) $fields = [$fields];

			foreach($fields as $field)
			{
				$field = explode('-', $field); $new_errors = &$errors;
				foreach($field as $i=>$f)
				{
					if(!isset($new_errors[$f])) $new_errors[$f] = [];
					$new_errors = &$new_errors[$f];

					if($i == count($field) - 1)
					{
						$new_errors[] = $message->getMessage();
					}
				}
				unset($new_errors);
			}
		}

		return $errors;
	}

}
