<?php

namespace TCommerce\Api\Controllers;

use TCommerce\Api\Exceptions\NotFoundAPIException;

class IndexController extends Controller
{

	public function indexAction()
	{
		throw new NotFoundAPIException();
	}

}
