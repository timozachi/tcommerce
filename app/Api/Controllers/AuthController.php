<?php

namespace TCommerce\Api\Controllers;

use Firebase\JWT\JWT;
use Phalcon\Crypt;
use Phalcon\Security\Random;
use Phalcon\Validation\Message\Group;
use TCommerce\Api\Exceptions\APIException;
use TCommerce\Api\Exceptions\ForbiddenAPIException;
use TCommerce\Api\Plugins\Auth;
use TCommerce\Api\Validations\ApiLoginValidation;
use TLib\Validations\Messages\Parser as MessagesParser;
use TCommerce\Core\Models\Api;
use TLib\Http\Input;

class AuthController extends Controller
{

	public function loginAction()
	{
		$input = Input::post();

		$validation = new ApiLoginValidation();

		/** @var Group $messages */
		$messages = $validation->validate($input);
		if($messages->count())
		{
			$errors = MessagesParser::parse($messages);
		}
		else
		{
			$api = Api::findFirst(
				[
					'columns' => ['id', 'token'],
					'conditions' => "[id] = :id: AND [active] = 1",
					'bind' => ['id' => $input['id']],
				]
			);

			if($api)
			{
				if($api->token !== $input['secret'])
				{
					$errors = [
						'secret' => ['Invalid secret']
					];
				}
				else
				{
					$config = $this->config->jwt;

					$time = time(); $random = new Random();
					return $this->createResponse([
						'authorization' => [
							'type' => 'Bearer',
							'token' => JWT::encode(
								[
									'sub' => $api->id,
									'iss' => $this->url->get(['for' => 'apiv1-auth-login']),
									'iat' => $time,
									'exp' => $time + $config->ttl,
									'nbf' => $time,
									/** @todo Mudar esse uuid para algo universal */
									'jti' => $random->uuid(),
								],
								$config->secret,
								$config->algo
							),
							'expires' => $time + $config->ttl
						],
					], 201);
				}
			}
			else
			{
				$errors = [
					'id' => ['ID not found']
				];
			}
		}

		return $this->createErrorsResponse(
			[
				'id' => 'invalid-resource-input',
				'title' => 'Invalid resource input',
				'detail' => 'Invalid resource input. Please fix the errors and try again.'
			],
			$errors,
			422
		);
	}

	public function refreshAction()
	{
		$jwt = Auth::$jwt;
		if(empty($jwt->refresh))
		{
			throw new APIException(
				'refresh-token-required',
				'Refresh Token Required',
				'The specified token is not a refresh token, you must use a refresh token in order to do this action',
				null,
				403
			);
		}

		$api = Api::findFirst(
			[
				'columns' => ['id'],
				'conditions' => "[id] = :id: AND [active] = 1",
				'bind' => ['id' => Auth::$apiId],
			]
		);
		if(!$api) {
			throw new ForbiddenAPIException();
		}

		$config = $this->config->jwt;

		$crypt = new Crypt();
		$random = new Random();

		$time = time();
		$token = [
			'sub' => Auth::$apiId,
			'iss' => $this->url->get(['for' => 'apiv1-auth-refresh']),
			'iat' => $time,
			'exp' => $time + $config->ttl,
			'nbf' => $time,
			/** @todo Mudar esse uuid para algo universal */
			'jti' => $random->uuid()
		];
		if(Auth::$userId) {
			$token['uid'] = base64_encode($crypt->encrypt((string)Auth::$userId, $this->config->security->encryptKey));
		}

		return $this->createResponse([
			'authorization' => [
				'type' => 'Bearer',
				'token' => JWT::encode(
					$token,
					$config->secret,
					$config->algo
				),
				'expires' => $time + $config->ttl
			],
		], 201);
	}

	public function checkAction()
	{
		return $this->createResponse(['todo' => 'Create this action']);
	}

}
