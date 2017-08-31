<?php

namespace TCommerce\Core\Models;

class Store extends Model
{

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
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=511, nullable=false)
     */
    public $corporate_name;

    /**
     *
     * @var string
     * @Column(type="string", length=2047, nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=14, nullable=false)
     */
    public $cnpj;

    /**
     *
     * @var string
     * @Column(type="string", length=63, nullable=false)
     */
    public $logo;

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
        $this->setSource('stores');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'stores';
    }

}
