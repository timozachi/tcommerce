<?php

namespace TCommerce\Core\Models;

use Phalcon\Mvc\Model as PhalconModel;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Model extends PhalconModel
{

	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'created_at';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'updated_at';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected static $_primaryKey = 'id';

	/**
	 * Fields that are considered dates (to be formatted)
	 *
	 * @var array
	 */
	public static $dates = [self::CREATED_AT, self::UPDATED_AT];

	/**
	 * The attributes that should be hidden in the output JSON.
	 *
	 * @var array
	 */
	public static $hidden = [];

	/**
	 * Indicates if the model should use soft deletes.
	 *
	 * @var bool
	 */
	public static $softDeletes = false;

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * The name of the "deleted" column.
	 *
	 * @var string
	 */
	const DELETED = 'deleted';

	/**
	 * Checks if a model exists by it's primary key or a specific field
	 *
	 * @param mixed $value
	 * @param string $field
	 *
	 * @return bool
	 */
	public static function exists($value, $field = null)
	{
		if(is_null($field)) $field = static::$_primaryKey;
		$exists = static::findFirst(
			[
				'columns' => ["[$field]"],
				'conditions' => "[{$field}] = :value:",
				'bind' => ['value' => $value]
			]
		);

		return $exists ? true : false;
	}

	public static function findBy($col, $value, $first = false, $parameters = [])
	{
		$parameters = array_merge([
			'conditions' => "[{$col}] = :value:",
			'bind' => ['value' => $value]
		], $parameters);

		if($first) return static::findFirst($parameters);
		return static::find($parameters);
	}

	public static function find($parameters = null)
	{
		return parent::find(static::_softDeleteParameters($parameters));
	}

	public static function findFirst($parameters = null)
	{
		return parent::findFirst(static::_softDeleteParameters($parameters));
	}

	protected static function _softDeleteParameters($parameters = null)
	{
		if(static::$softDeletes)
		{
			$model = static::class;

			if(is_null($parameters)) $parameters = [];
			/*elseif(!is_array($parameters))
			{
				$pk = static::$_primaryKey;
				$val = $parameters;
				$parameters = [
					'conditions' => "[$model].[$pk] = :value"
				];
				$parameters['bind'] = ['value' => $val];
			}*/

			if(is_array($parameters))
			{
				$and = ' AND ';
				if(isset($parameters[0])) $key = 0;
				elseif(isset($parameters['conditions'])) $key = 'conditions';
				else
				{
					$and = '';
					$key = 'conditions';
					$parameters['conditions'] = '';
				}

				$parameters[$key] .= "{$and}[$model].[" . static::DELETED . "] = 0";
			}
		}

		return $parameters;
	}

	/**
	 * Called before a model initializes
	 */
	public function initialize()
	{
		$this->keepSnapshots(true);
		
		if(static::$softDeletes)
		{
			$this->addBehavior(
				new SoftDelete(
					[
						'field' => static::DELETED,
						'value' => 1
					]
				)
			);
		}
	}

	/**
	 * Called before a model is inserted in the database
	 */
	public function beforeCreate()
	{
		if($this->timestamps)
		{
			$date = gmdate('Y-m-d H:i:s');
			if(property_exists($this, static::CREATED_AT)) {
				$this->{static::CREATED_AT} = $date;
			}
			if(property_exists($this, static::UPDATED_AT)) {
				$this->{static::UPDATED_AT} = $date;
			}

		}
	}

	/**
	 * Called before a model is updated in the database
	 */
	public function beforeUpdate()
	{
		if($this->timestamps)
		{
			if(property_exists($this, static::UPDATED_AT)) {
				$this->{static::UPDATED_AT} = gmdate('Y-m-d H:i:s');
			}
		}
	}

	/**
	 * Loop through each row and parse, also remove private fields optionally
	 *
	 * @param array $data
	 * @param bool $removeHidden [optional]
	 */
	public static function parse(array &$data, $removeHidden = true)
	{
		if(isset($data[0]))
		{
			foreach($data as &$row)
			{
				static::parse($row, $removeHidden);
			}
		}
		elseif(!empty($data))
		{
			foreach(static::$dates as $date_field)
			{
				if(isset($data[$date_field]) && !empty($data[$date_field]))
				{
					$data[$date_field] = str_replace(' ', 'T', $data[$date_field]) . 'Z';
				}
			}

			if($removeHidden)
			{
				foreach(static::$hidden as $pf)
				{
					unset($data[$pf]);
				}
			}
		}
	}

}
