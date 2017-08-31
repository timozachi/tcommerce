<?php

namespace TCommerce\Api\Routes;

class Users extends Group
{

	const NAME_PREFIX = 'apiv1-users';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'users'
			]
		);

		$this->setPrefix('/users');

		$this->addPost('', [
			'action' => 'store'
		])->setName(static::NAME_PREFIX . '-store');

		$this->addPost('/login', [
			'action' => 'login'
		])->setName(static::NAME_PREFIX . '-login');

		$this->addGet('/logged', [
			'action' => 'logged'
		])->setName(static::NAME_PREFIX . '-logged');

		$this->addGet('/me', [
			'action' => 'me'
		])->setName(static::NAME_PREFIX . '-me');
	}

}
