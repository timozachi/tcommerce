<?php

namespace TCommerce\Admin\Controllers;

use TCommerce\Core\Plugins\Auth;
use TLib\Http\Input;

class IndexController extends Controller
{

	public function indexAction()
	{
		return $this->response->redirect(['for' => 'admin-dashboard-index']);
	}

	public function loginAction()
	{
		$this->view(
			'index/login'
		);
	}

	public function loginPostAction()
	{
		$post = Input::post();

		if(trim($post['email']) != '' && !empty($post['password']))
		{
			if($this->auth->attempt($post, $errors))
			{
				$this->response->redirect(!empty($_GET['redirect']) ? $_GET['redirect'] : ['for' => 'admin-dashboard-index']);
				return;
			}
			var_dump($errors); exit();
		}

		$this->flashSession->error('E-mail and/or password incorrect');
		$this->response->redirect(['for' => 'admin-index-login']);
	}

	public function logoutAction()
	{
		$this->auth->logout();
	}

}
