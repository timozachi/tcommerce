<?php

namespace TCommerce\Api\Routes;

class Variants extends Group
{

	const NAME_PREFIX = 'apiv1-variants';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'variants'
			]
		);

		$this->setPrefix('/variants');

		//CRUD
		$this->addGet('', [
			'action' => 'index'
		])->setName(static::NAME_PREFIX . '-index');

		$this->addPost('', [
			'action' => 'store'
		])->setName(static::NAME_PREFIX . '-store');

		$this->addGet('/{id:[1-9][0-9]*}', [
			'action' => 'show'
		])->setName(static::NAME_PREFIX . '-show');

		$this->addPut('/{id:[1-9][0-9]*}', [
			'action' => 'update'
		])->setName(static::NAME_PREFIX . '-update');

		$this->addDelete('/{id:[1-9][0-9]*}', [
			'action' => 'destroy'
		])->setName(static::NAME_PREFIX . '-destroy');
	}

}
