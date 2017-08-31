<?php

namespace TCommerce\Api\Exceptions;

use Exception;
use TCommerce\Core\Exceptions\HttpException;

class APIException extends HttpException
{

	protected $_id = null;
	protected $_title = null;
	protected $_detail = null;
	protected $_errors = null;
	protected $_statusCode = null;
	protected $_headers = null;

	public function __construct(
		$id, $title, $detail = '',
		array $errors = null,
		$statusCode = 400,
		array $headers = [],
		$code = 0,
		Exception $previous = null
	) {
		parent::__construct($statusCode, $detail, $headers, $code, $previous);

		$this->_id = $id;
		$this->_title = $title;
		$this->_detail = $detail;
		$this->_errors = $errors;
		$this->_statusCode = $statusCode;
		$this->_headers = $headers;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function getDetail()
	{
		return $this->_detail;
	}

	public function getErrors()
	{
		return $this->_errors;
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
	public function setHeaders($headers)
	{
		$this->_headers = $headers;
	}

}
