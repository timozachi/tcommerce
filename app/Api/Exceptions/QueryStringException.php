<?php

namespace TCommerce\Api\Exceptions;

use Exception;

class QueryStringException extends APIException
{

	public function __construct(
		$detail = 'Invalid query string parameter(s). Please fix the errors and try again.',
		array $errors = null,
		$code = 0,
		Exception $previous = null
	) {
		parent::__construct('invalid-query-string', 'Invalid Query String', $detail, $errors, 422, [], $code, $previous);
	}

}