<?php

namespace TCommerce\Core\Models;

class Api extends Model
{

	public static $softDeletes = true;

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=5, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=44, nullable=false)
     */
    public $token;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $name;

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
	 * @var integer
	 * @Column(type="integer", length=1, nullable=false)
	 */
	public $deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		parent::initialize();

		$this->setSource('apis');
		$this->hasManyToMany('id', 'TCommerce\Core\Models\ApiRole', 'api_id', 'role_id', 'TCommerce\Core\Models\Role', 'id', ['alias' => 'Roles']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'apis';
    }

}
