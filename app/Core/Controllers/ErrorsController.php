<?php

namespace TCommerce\Core\Controllers;

use TCommerce\Core\Http\Exceptions\HttpException;
use TLib\Http\Status;

class ErrorsController extends Controller
{

	public function httpExceptionAction(HttpException $exception)
	{
		$this->view(
			'errors/exception',
			[
				'code' => $exception->getStatusCode(),
				'message' => Status::$texts[$exception->getStatusCode()]
			],
			$exception->getStatusCode(),
			$exception->getHeaders()
		);
	}

	public function show404Action()
	{
		$this->view(
			'errors/show404',
			[],
			404
		);
	}

	public function show500Action()
	{
		$this->view(
			'errors/show500',
			[],
			500
		);
	}

}
