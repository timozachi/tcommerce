<?php

namespace TCommerce\Core\Exceptions;

use Exception;

class UnauthorizedHttpException extends HttpException
{

	public function __construct($challenge, $message = null, $code = 0, Exception $previous = null)
	{
		parent::__construct(401, $message, ['WWW-Authenticate' => $challenge], $code, $previous);
	}

}
