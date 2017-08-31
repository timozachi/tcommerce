<?php

namespace TCommerce\Frontend\Routes;

class Index extends Group
{

	const NAME_PREFIX = 'frontend-index';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'index'
			]
		);

		$this->addGet('/', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');
	}

}