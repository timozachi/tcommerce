<?php

namespace TCommerce\Api\Exceptions;

use Exception;

class LoggedInUserRequiredException extends APIException
{

	public function __construct(
		$message = 'A logged in user is required to perform this action.',
		$code = 0,
		Exception $previous = null
	) {
		parent::__construct('logged-in-user-required', 'Logged In User Required', $message, null, 403, [], $code, $previous);
	}

}
