<?php

namespace TCommerce\Core\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Validation;

class Product extends Model
{

	public static $dates = ['available_on', self::CREATED_AT, self::UPDATED_AT];

	public static $hidden = ['deleted'];

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
    public $store_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    public $shipping_category_id;

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
    public $permalink;

    /**
     *
     * @var string
     * @Column(type="string", length=4095, nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=511, nullable=false)
     */
    public $meta_keywords;

    /**
     *
     * @var string
     * @Column(type="string", length=511, nullable=false)
     */
    public $meta_description;

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
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $available_on;

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
        $this->setSource('products');
		$this->belongsTo('store_id', 'TCommerce\Core\Models\Store', 'id', ['alias' => 'Store', 'reusable' => true]);
		$this->belongsTo('shipping_category_id', 'TCommerce\Core\Models\ShippingCategory', 'id', ['alias' => 'ShippingCategory', 'reusable' => true]);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'products';
    }

	public static function parse(array &$data, $removeHidden = true)
	{
		parent::parse($data, $removeHidden);
		if(!isset($data[0]))
		{
			//Do custom parse here
		}
	}

}
