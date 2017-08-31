<?php

namespace TCommerce\Api\Security;

use Phalcon\Di\Injectable;
use Phalcon\Validation\Message\Group;
use TCommerce\Api\Plugins\Auth as AuthPlugin;
use TCommerce\Api\Validations\LoginValidation;
use TLib\Validations\Messages\Parser as MessagesParser;
use TCommerce\Core\Models\User;
use TLib\Http\Input;

class Auth extends Injectable
{

	/** @var User */
	protected $_user;

	public function attempt($input = null, &$errors = null)
	{
		if(is_null($input)) {
			$input = Input::post();
		}

		$validation = new LoginValidation();

		/** @var Group $messages */
		$messages = $validation->validate($input);
		if($messages->count())
		{
			$errors = MessagesParser::parse($messages);
		}
		else
		{
			$user = User::findFirst(
				[
					'columns' => ['id', 'password'],
					'conditions' => "[email] = :email:",
					'bind' => ['email' => $input['email']],
				]
			);

			if($user)
			{
				if($this->security->checkHash($input['password'], $user->password))
				{
					AuthPlugin::$userId = $user->id;
					return (int)$user->id;
				}
				else
				{
					$errors = [
						'password' => ['Invalid password']
					];
				}
			}
			else
			{
				$errors = [
					'password' => ['Email not found']
				];
				$this->security->hash(rand());
			}
		}

		return false;
	}

	public function check()
	{
		if(AuthPlugin::$userId) {
			return true;
		}

		return false;
	}

	public function getUserId()
	{
		if($this->check()) {
			return AuthPlugin::$userId;
		}

		return null;
	}

	public function getUser($asAnArray = true)
	{
		if($this->check())
		{
			if(!$this->_user) {
				$this->_user = User::findFirst($this->getUserId());
			}

			if($asAnArray)
			{
				$user = $this->_user->toArray();
				User::parse($user);

				return $user;
			}

			return $this->_user;
		}

		return null;
	}

}
