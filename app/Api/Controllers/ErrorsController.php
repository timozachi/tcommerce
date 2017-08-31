<?php

namespace TCommerce\Api\Controllers;

use Phalcon\Http\Response;
use TCommerce\Api\Exceptions\APIException;

class ErrorsController extends Controller
{

	/**
	 * Bad request
	 *
	 * The 400 (Bad Request) status code indicates that the server cannot or
	 * will not process the request due to something that is perceived to be
	 * a client error (e.g., malformed request syntax, invalid request
	 * message framing, or deceptive request routing).
	 *
	 * @param APIException $exception
	 *
	 * @return Response
	 */
	public function exceptionAction(APIException $exception)
	{
		$error = [
			'id' => $exception->getId(),
			'title' => $exception->getTitle(),
			'detail' => $exception->getDetail()
		];

		$errors = null;
		if(!is_null($exception->getErrors())) $errors = $exception->getErrors();

		return $this->createErrorsResponse(
			$error,
			$errors,
			$exception->getStatusCode(),
			$exception->getHeaders()
		);
	}

	/**
	 * Unauthorized
	 *
	 * The 401 (Unauthorized) status code indicates that the request has not
	 * been applied because it lacks valid authentication credentials for
	 * the target resource. The server generating a 401 response MUST send
	 * a WWW-Authenticate header field containing at least one
	 * challenge applicable to the target resource.
	 *
	 * @param string $detail
	 * @param mixed $errors [optional]
	 *
	 * @return Response
	 */
	public function show401Action($detail = null, $errors = [])
	{
		return $this->createErrorsResponse(
			[
				'id' => 'unauthorized',
				'title' => 'Unauthorized',
				'detail' => $detail ? $detail : 'You are not authorized to access this resource.'
			],
			$errors,
			401,
			['WWW-Authenticate' => 'Bearer']
		);
	}

	/**
	 * Forbidden
	 *
	 * The 403 (Forbidden) status code indicates that the server understood
	 * the request but refuses to authorize it.  A server that wishes to
	 * make public why the request has been forbidden can describe that
	 * reason in the response payload (if any).
	 *
	 * @param string $detail
	 * @param mixed $errors [optional]
	 *
	 * @return Response
	 */
	public function show403Action($detail = null, $errors = [])
	{
		return $this->createErrorsResponse(
			[
				'id' => 'forbidden',
				'title' => 'Forbidden',
				'detail' => $detail ? $detail : 'You are not authorized to access this resource.'
			],
			$errors,
			403
		);
	}

	/**
	 * The 404 (Not Found) status code indicates that the origin server did
	 * not find a current representation for the target resource or is not
	 * willing to disclose that one exists.  A 404 status code does not
	 * indicate whether this lack of representation is temporary or
	 * permanent; the 410 (Gone) status code is preferred over 404 if the
	 * origin server knows, presumably through some configurable means, that
	 * the condition is likely to be permanent.
	 *
	 * @param string $detail
	 * @param mixed $errors [optional]
	 *
	 * @return Response
	 */
	public function show404Action($detail = null, $errors = [])
	{
		return $this->createErrorsResponse(
			[
				'id' => 'not-found',
				'title' => 'Not Found',
				'detail' => $detail ? $detail : 'The resource you are trying to access was not found on this server.'
			],
			$errors,
			404
		);
	}

	/**
	 * Internal Server Error
	 *
	 * The 500 (Internal Server Error) status code indicates that the server
	 * encountered an unexpected condition that prevented it from fulfilling
	 * the request.
	 *
	 * @param string $detail
	 * @param mixed $errors [optional]
	 *
	 * @return Response
	 */
	public function show500Action($detail = null, $errors = [])
	{
		return $this->createErrorsResponse(
			[
				'id' => 'internal-server-error',
				'title' => 'Internal Server Error',
				'detail' => $detail ? $detail : 'An unexpected error occurred in our server, please try again later.'
			],
			$errors,
			500
		);
	}

}
