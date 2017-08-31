<?php

namespace TCommerce\Core\Exceptions;

use Exception;
use RuntimeException;

class HttpException extends RuntimeException
{

	protected $_statusCode;
	protected $_headers;

	public function __construct($statusCode, $message = null, array $headers = [], $code = 0, Exception $previous = null)
	{
		$this->_statusCode = $statusCode;
		$this->_headers = $headers;

		parent::__construct($message, $code, $previous);
	}

	public function getStatusCode()
	{
		return $this->_statusCode;
	}
	public function setStatusCode($statusCode)
	{
		$this->_statusCode = $statusCode;
	}

	public function getHeaders()
	{
		return $this->_headers;
	}
	public function setHeaders(array $headers)
	{
		$this->_headers = $headers;
	}

}
