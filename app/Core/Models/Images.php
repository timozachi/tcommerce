<?php

namespace TCommerce\Core\Models;

class Images extends Model
{

	public static $dates = [self::UPDATED_AT];

	public static $hidden = ['product_id', 'variant_id', 'deleted'];

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
	 * @var integer
	 * @Column(type="integer", length=10, nullable=false)
	 */
	public $variant_id;

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

}
