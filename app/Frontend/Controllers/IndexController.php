<?php

namespace TCommerce\Frontend\Controllers;

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

}