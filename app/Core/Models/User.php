<?php

namespace TCommerce\Core\Models;

class User extends Model
{

	public static $softDeletes = true;

	public static $hidden = [
		'password', 'deleted'
	];

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
     * @Column(type="integer", length=1, nullable=false)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=127, nullable=false)
     */
    public $email;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=127, nullable=false)
	 */
	public $password;

	/**
	 *
	 * @var integer
	 * @Column(type="integer", length=1, nullable=false)
	 */
	public $pj;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=11, nullable=true)
	 */
	public $cpf;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=15, nullable=true)
	 */
	public $rg;

	/**
	 *
	 * @var string
	 * @Column(type="string", length=14, nullable=true)
	 */
	public $cnpj;

	/**
	 *
	 * @var string
	 * @Column(type="string", nullable=true)
	 */
	public $birth_date;

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
	    parent::initialize();

       /**
        * @todo Fazer relations aqui
        */
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

	public static function parse(array &$data, $removeHidden = true)
	{
		parent::parse($data, $removeHidden);
		if(!isset($data[0]))
		{
			if(isset($data['pj'])) $data['pj'] = (bool)(int)$data['pj'];
		}
	}

}
