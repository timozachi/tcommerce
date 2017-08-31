<?php

namespace TCommerce\Api\Exceptions;

use Exception;

class ForbiddenAPIException extends APIException
{

	public function __construct(
		$message = 'You are not authorized to access this resource.',
		$code = 0,
		Exception $previous = null
	) {
		parent::__construct('forbidden', 'Forbidden', $message, null, 403, [], $code, $previous);
	}

}
