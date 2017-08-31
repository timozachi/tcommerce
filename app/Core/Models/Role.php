<?php

namespace TCommerce\Core\Models;

class Role extends Model
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
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $admin;

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
        $this->setSource("roles");
		$this->hasManyToMany('id', 'TCommerce\Core\Models\ApiRole', 'role_id', 'api_id', 'TCommerce\Core\Models\Api', 'id', ['alias' => 'Apis']);
		$this->hasManyToMany('id', 'TCommerce\Core\Models\RoleAction', 'role_id', 'action_id', 'TCommerce\Core\Models\Action', 'id', ['alias' => 'Actions']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'roles';
    }

}
