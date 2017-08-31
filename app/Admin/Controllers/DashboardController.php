<?php

namespace TCommerce\Admin\Controllers;

class DashboardController extends Controller
{

	public function indexAction()
	{
		$this->view(
			'dashboard/index',
			[
				'userLogged' => $this->auth->check(),
				'user' => $this->auth->getUser()
			]
		);
	}

}
