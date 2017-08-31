<?php

namespace TCommerce\Api\Validations;

use TLib\Validations\Validators\Exists;
use TLib\Validations\Validators\Integer;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;

class VariantValidation extends Validation
{

	protected static $_createWhiteList = [
		'product_id', 'sku', 'name', 'cost_price', 'price',
		'weight', 'width', 'height', 'depth', 'active'
	];

	protected static $_updateWhiteList = [
		'sku', 'name', 'cost_price', 'price',
		'weight', 'width', 'height', 'depth', 'active'
	];

	public function validate($data = null, $entity = null)
	{
		$keys = [];
		if(is_array($data)) $keys = array_keys($data);

		if($this->mode == Validation::CREATE)
		{
			$this->add(
				['product_id'],
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

		if($this->mode == Validation::CREATE)
		{
			$models = [
				'product_id' => 'TCommerce\Core\Models\Product'
		    ];

			$intersect = array_intersect($keys, ['product_id']);
			if($intersect)
			{
				$this->add(
					$intersect,
					new Integer()
				);
				$this->add(
					$intersect,
					new Exists($models)
				);
			}
		}

		return parent::validate($data, $entity);
	}

}
