<?php

namespace TCommerce\Api\Routes;

class Products extends Group
{

	const NAME_PREFIX = 'apiv1-products';

	public function initialize()
	{
		$this->setPaths(
			[
				'controller' => 'products'
			]
		);

		$this->setPrefix('/products');

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
