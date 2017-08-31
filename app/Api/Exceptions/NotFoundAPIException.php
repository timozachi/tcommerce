<?php

namespace TCommerce\Api\Exceptions;

use Exception;

class NotFoundAPIException extends APIException
{

	public function __construct(
		$message = 'The resource you are trying to access was not found on this server.',
		$code = 0,
		Exception $previous = null
	) {
		parent::__construct('not-found', 'Not Found', $message, null, 404, [], $code, $previous);
	}

}
