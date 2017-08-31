<?php

namespace TCommerce\Admin\Routes;

class Dashboard extends Group
{

	const NAME_PREFIX = 'admin-dashboard';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'dashboard'
			]
		);

		$this->setPrefix('/dashboard');

		$this->addGet('', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');
	}

}
