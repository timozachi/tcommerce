<?php

namespace TCommerce\Api\Controllers;

use Firebase\JWT\JWT;
use Phalcon\Crypt;
use Phalcon\Security\Random;
use TCommerce\Api\Plugins\Auth;
use TLib\Http\Input;
use Phalcon\Http\Response;
use Phalcon\Validation\Message\Group;
use TCommerce\Core\Models\ApiUser;
use TCommerce\Api\Plugins\Token;
use TCommerce\Api\Validations\UserValidation;
use TCommerce\Api\Validations\Validation;
use TLib\Validations\Messages\Parser as MessagesParser;
use TCommerce\Core\Models\User;

class UsersController extends Controller
{

	/**
	 * Runs when the controller is initialized
	 */
	public function initialize()
	{
		parent::initialize();

		if(!in_array($this->dispatcher->getActionName(), ['store', 'login']))
		{
			$this->_requireLoggedInUser();
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeAction()
	{
		$input = Input::post();

		$validation = new UserValidation();
		$validation->mode = Validation::CREATE;
		/** @var Group $messages */
		$messages = $validation->validate($input);
		if($messages->count())
		{
			return $this->createErrorsResponse(
				[
					'id' => 'invalid-resource-input',
					'title' => 'Invalid resource input',
					'detail' => 'Invalid user input. Please fix the errors and try again.'
				],
				MessagesParser::parse($messages),
				422
			);
		}

		$user = new User();
		$user->password = $this->security->hash($input['password']);
		$user->create($input, $validation->getWhiteList());

		//Connects the API to the user
		$api_user = new ApiUser();
		$api_user->create(
			[
				'api_id' => Auth::$apiId,
				'user_id' => $user->id
			]
		);

		return $this->createResponse(
			['id' => $user->id],
			201
		);
	}

	/**
	 * Logs in a user to the session
	 *
	 * @return Response
	 */
	public function loginAction()
	{
		if($this->auth->attempt(null, $errors))
		{
			$config = $this->config->jwt;

			$crypt = new Crypt();
			$random = new Random();

			$time = time();
			$current_jwt = Auth::$jwt;
			$user_id_encoded = base64_encode($crypt->encrypt((string)Auth::$userId, $this->config->security->encryptKey));
			$token = JWT::encode(
				[
					'sub' => Auth::$apiId,
					'iss' => $this->url->get(['for' => 'apiv1-users-login']),
					'iat' => $time,
					'exp' => $current_jwt->exp,
					'nbf' => $time,
					/** @todo Mudar esse uuid para algo universal */
					'jti' => $random->uuid(),
					'uid' => $user_id_encoded
				],
				$config->secret,
				$config->algo
			);
			$refresh_token = JWT::encode(
				[
					'sub' => Auth::$apiId,
					'iss' => $this->url->get(['for' => 'apiv1-users-login']),
					'iat' => $time,
					'exp' => $time + Input::post('lifetime', $config->ttl),
					'nbf' => $time,
					/** @todo Mudar esse uuid para algo universal */
					'jti' => $random->uuid(),
					'uid' => $user_id_encoded,
					'refresh' => 1
				],
				$config->secret,
				$config->algo
			);

			return $this->createResponse(
				[
					'authorization' => [
						'type' => 'Bearer',
						'token' => $token,
						'refresh_token' => $refresh_token,
						'expires' => $current_jwt->exp,
						'refresh_expires' => $time + Input::post('lifetime', $config->ttl)
					],
					'user' => $this->auth->getUser()
				],
				200
			);
		}

		return $this->createErrorsResponse(
			[
				'id' => 'invalid-resource-input',
				'title' => 'Invalid resource input',
				'detail' => 'Invalid login input. Please fix the errors and try again.'
			],
			$errors,
			422
		);
	}

	public function loggedAction()
	{
		return $this->createResponse([
			'logged' => $this->auth->check()
		]);
	}

	public function meAction()
	{
		return $this->createResponse([
			'user' => $this->auth->getUser()
		]);
	}

}
