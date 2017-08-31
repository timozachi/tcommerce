<?php

namespace TCommerce\Admin\Routes;

class Index extends Group
{

	const NAME_PREFIX = 'admin-index';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'index'
			]
		);

		$this->setPrefix('');

		$this->addGet('', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');

		$this->addGet('/login', [
			'action' => 'login'
		])->setName(static::NAME_PREFIX . '-login');

		$this->addPost('/login', [
			'action' => 'loginPost'
		])->setName(static::NAME_PREFIX . '-login-post');
	}

}