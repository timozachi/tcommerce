<?php

namespace TCommerce\Api\Controllers;

use Phalcon\Http\Response;
use TCommerce\Api\Exceptions\SessionException;
use TCommerce\Api\Exceptions\LoggedInUserRequiredException;
use TCommerce\Api\Security\Auth;
use TCommerce\Core\Controllers\Controller as CoreController;

/**
 * @pa
 * @property Auth $auth
 */
class Controller extends CoreController
{

	/** @var int */
	public $initTime = null;

	/** @var Response */
	public $response = null;

	/**
	 * Function
	 */
	public function initialize()
	{
		$this->initTime = microtime(true);
	}

	/**
	 * @param mixed $data The data to be set.
	 * @param int $status [optional] Response status code
	 * @param array $meta [optional] Extra meta data about the response
	 * @param array $headers [optional] Extra headers to be sent
	 *
	 * @return Response
	 */
	public function createResponse($data, $status = 200, $headers = [], $meta = [])
	{
		$main = [
			'meta' => $this->_getMeta('data', $meta),
			'data' => $data
		];

		return $this->createJsonResponse($main, $status, $headers);
	}

	/**
	 * @param array $error A single error object.
	 * @param array $errors [optional] A single error object or many.
	 * @param int $statusCode [optional] Error status code
	 * @param array $headers [optional] Extra headers to be sent
	 *
	 * @return Response
	 */
	public function createErrorsResponse($error, $errors = [], $statusCode = 400, $headers = [])
	{
		if(!isset($errors[0])) $errors = empty($errors) ? [] : [$errors];

		$main = [
			'meta' => $this->_getMeta('error'),
			'error' => $error,
			'errors' => $errors
		];

		return $this->createJsonResponse($main, $statusCode, $headers);
	}

	/**
	 * @param array $error A single error object.
	 * @param int $statusCode [optional] Error status code
	 * @param array $headers [optional] Extra headers to be sent
	 *
	 * @return Response
	 */
	public function createErrorResponse($error, $statusCode = 400, $headers = [])
	{
		return $this->createErrorsResponse($error, [], $statusCode, $headers);
	}

	protected function _getMeta($type = null, $extraMeta = [])
	{
		global $init_time;

		$microtime = microtime(true);
		$request = $this->request->getMethod() . ' ' . $this->request->getURI();

		return array_merge([
			'type' => $type,
			'total_time' => round(($microtime - $init_time) * 1000, 2),
			'time' => round(($microtime - $this->initTime) * 1000, 2),
			'request' => $request
		], $extraMeta);
	}

	protected function _requireLoggedInUser($detail = 'A logged in user is required to perform this action.')
	{
		if(!$this->auth->check()) {
			throw new LoggedInUserRequiredException($detail);
		}

		return true;
	}


}
