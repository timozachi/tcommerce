<?php

namespace TCommerce\Core\Models;

class RoleAction extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=5, nullable=false)
     */
    public $role_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=5, nullable=false)
     */
    public $action_id;

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
		$this->setSource('roles_actions');
        $this->belongsTo('action_id', 'Action', 'id');
        $this->belongsTo('role_id', 'Role', 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'roles_actions';
    }

}
