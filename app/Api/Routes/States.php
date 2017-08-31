<?php

namespace TCommerce\Api\Routes;

class States extends Group
{

	const NAME_PREFIX = 'apiv1-states';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'states'
			]
		);

		$this->setPrefix('/states');

		$this->addGet('', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');

		$this->addGet('/{id:[1-9][0-9]*}', [
			'action' => 'show'
		])->setName(static::NAME_PREFIX . '-show');
	}

}
