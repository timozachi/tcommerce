<?php

namespace TCommerce\Frontend\Controllers;

use Exception;

class IndexController extends Controller
{

	public function indexAction()
	{
		$this->view(
			'index/index',
			[],
			200,
			[]
		);
	}

	public function debuggerAction()
    {
        throw new Exception(
            'Test exception so it displays debug information'
        );
    }

}