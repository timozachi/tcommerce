<?php

namespace TCommerce\Api\Routes;

class Auth extends Group
{

	const NAME_PREFIX = 'apiv1-auth';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'auth'
			]
		);

		$this->setPrefix('/auth');

		$this->addPost('/login', [
			'action' => 'login'
		])->setName(static::NAME_PREFIX . '-login');

		$this->addPost('/refresh', [
			'action' => 'refresh'
		])->setName(static::NAME_PREFIX . '-refresh');

		$this->addGet('/check', [
			'action' => 'check'
		])->setName(static::NAME_PREFIX . '-check');
	}

}
