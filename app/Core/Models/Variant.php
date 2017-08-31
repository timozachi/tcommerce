<?php

namespace TCommerce\Core\Models;

class Variant extends Model
{
	
	public static $dates = [self::CREATED_AT, self::UPDATED_AT];

	public static $hidden = ['product_id', 'deleted'];

	public static $softDeletes = true;

	/**
	 *
	 * @var integer
	 * @Primary
	 * @Identity
	 * @Column(type="integer", length=10, nullable=false)
	 */
	public $id;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=10, nullable=false)
	 */
	public $product_id;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=true)
	 */
	public $sku;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=false)
	 */
	public $name;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=false)
	 */
	public $image_thumb;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=false)
	 */
	public $image_small;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=false)
	 */
	public $image_large;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=255, nullable=false)
	 */
	public $image_original;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=8, nullable=false)
	 */
	public $stock;

	/**
	 *
	 * @var double
	 * @Column(type="double", length=10, nullable=false)
	 */
	public $cost_price;

	/**
	 *
	 * @var double
	 * @Column(type="double", length=10, nullable=false)
	 */
	public $price;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=9, nullable=false)
	 */
	public $weight;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=9, nullable=false)
	 */
	public $width;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=9, nullable=false)
	 */
	public $height;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=9, nullable=false)
	 */
	public $depth;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=1, nullable=false)
	 */
	public $active;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	public $created_at;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	public $updated_at;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=1, nullable=false)
	 */
	public $deleted;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		$this->setSource('variants');
		$this->belongsTo('product_id', 'TCommerce\Core\Models\Product', 'id', ['alias' => 'Product', 'reusable' => true]);
	}

	/**
	 * Returns table name mapped in the model.
	 *
	 * @return string
	 */
	public function getSource()
	{
		return 'variants';
	}
	
}
