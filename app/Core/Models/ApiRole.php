<?php

namespace TCommerce\Core\Models;

class ApiRole extends Model
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
     * @Column(type="integer", length=5, nullable=false)
     */
    public $role_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('apis_roles');
        $this->belongsTo('api_id', 'TCommerce\Core\Models\Api', 'id', ['alias' => 'Api']);
        $this->belongsTo('role_id', 'TCommerce\Core\Models\Role', 'id', ['alias' => 'Role']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'apis_roles';
    }

}
