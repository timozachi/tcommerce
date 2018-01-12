<?php

namespace TCommerce\Core\Controllers;

use TCommerce\Core\Exceptions\HttpException;
use TLib\Http\Status;

class ErrorsController extends Controller
{

	public function httpExceptionAction(HttpException $exception)
	{
		$this->view(
			'errors/http-exception',
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
			'errors/404',
			[],
			404
		);
	}

	public function show500Action()
	{
		$this->view(
			'errors/500',
			[],
			500
		);
	}

}
