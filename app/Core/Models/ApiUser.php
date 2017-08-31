<?php

namespace TCommerce\Core\Models;

class ApiUser extends Model
{

	/**
	 *
	 * @var integer
	 * @Primary
	 * @Column(type="integer", length=5, nullable=false)
	 */
	public $api_id;

	/**
	 *
	 * @var integer
	 * @Primary
	 * @Column(type="integer", length=10, nullable=false)
	 */
	public $user_id;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		parent::initialize();

		$this->belongsTo('api_id', 'TCommerce\Core\Models\Api', 'id', ['alias' => 'Api']);
		$this->belongsTo('user_id', 'TCommerce\Core\Models\User', 'id', ['alias' => 'User']);
	}

	/**
	 * Returns table name mapped in the model.
	 *
	 * @return string
	 */
	public function getSource()
	{
		return 'apis_users';
	}

}
