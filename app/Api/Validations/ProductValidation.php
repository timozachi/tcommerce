<?php

namespace TCommerce\Api\Validations;

use TLib\Validations\Validators\Exists;
use TLib\Validations\Validators\Integer;
use TLib\Validations\Validators\Date;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;

class ProductValidation extends Validation
{

	protected static $_createWhiteList = [
		'store_id', 'shipping_category_id', 'sku', 'name', 'permalink', 'description',
		'meta_keywords', 'meta_description', 'cost_price', 'price', 'available_on',
		'weight', 'width', 'height', 'depth', 'active'
	];

	protected static $_updateWhiteList = [
		'shipping_category_id', 'sku', 'name', 'permalink', 'description',
		'meta_keywords', 'meta_description', 'cost_price', 'price', 'available_on',
		'weight', 'width', 'height', 'depth', 'active'
	];

	public function validate($data = null, $entity = null)
	{
		$keys = [];
		if(is_array($data)) $keys = array_keys($data);

		if($this->mode == Validation::CREATE)
		{
			$this->add(
				['store_id', 'shipping_category_id', 'name', 'cost_price', 'price', 'weight'],
				new PresenceOf()
			);
		}

		$intersect = array_intersect($keys, ['cost_price', 'price', 'weight', 'width', 'height', 'depth']);
		if(!empty($intersect))
		{
			$this->add(
				$intersect,
				new Numericality()
			);
		}

		$intersect = array_intersect($keys, ['available_on']);
		if(!empty($intersect))
		{
			$this->add(
				$intersect,
				new Date()
			);
		}

		$intersect = array_intersect($keys, ['cost_price', 'price']);
		if(!empty($intersect))
		{
			$this->add(
				$intersect,
				new Between(
					[
						'minimum' => 0,
						'maximum' => 1000000
					]
				)
			);
		}

		$check = ['shipping_category_id'];
		$models = [
			'shipping_category_id' => 'TCommerce\Core\Models\ShippingCategory'
		];
		if($this->mode == Validation::CREATE)
		{
			array_unshift($check, 'store_id');
			$models['store_id'] = 'TCommerce\Core\Models\Store';
		}

		$intersect = array_intersect($keys, $check);
		if(!empty($intersect))
		{
			$this->add(
				$intersect,
				new Integer()
			);
			$this->add(
				$intersect,
				new Exists(['model' => $models])
			);
		}

		return parent::validate($data, $entity);
	}

}
