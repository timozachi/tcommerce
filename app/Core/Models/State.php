<?php

namespace TCommerce\Core\Models;

class State extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=false)
     */
    public $country_id;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=false)
     */
    public $abbreviation;

    /**
     *
     * @var string
     * @Column(type="string", length=127, nullable=false)
     */
    public $name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('states');
        $this->belongsTo('country_id', 'TCommerce\Core\Models\Country', 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'states';
    }

}
