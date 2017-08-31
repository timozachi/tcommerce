<?php

namespace TCommerce\Core\Models;

class Action extends Model
{

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
     * @var integer
     * @Column(type="integer", length=5, nullable=false)
     */
    public $module_id;

    /**
     *
     * @var string
     * @Column(type="string", length=63, nullable=false)
     */
    public $key;

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
    public $route;

    /**
     *
     * @var integer
     * @Column(type="integer", length=6, nullable=false)
     */
    public $order;

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
     * Initialize method for model.
     */
    public function initialize()
    {
		parent::initialize();
		$this->setSource('actions');
        $this->belongsTo('module_id', 'TCommerce\Core\Models\Module', 'id', ['alias' => 'Module']);
		$this->hasManyToMany('id', 'TCommerce\Core\Models\RoleAction', 'action_id', 'role_id', 'TCommerce\Core\Models\Role', 'id', ['alias' => 'Roles']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'actions';
    }

}
