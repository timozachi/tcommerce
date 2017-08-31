<?php

namespace TCommerce\Admin\Routes;

class Login extends Group
{

	const NAME_PREFIX = 'admin-login';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'login'
			]
		);

		$this->setPrefix('/login');

		$this->addGet('', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');
	}

}
