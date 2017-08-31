<?php

namespace TCommerce\Api\Validations;

use TLib\Validations\Validators\CNPJ;
use TLib\Validations\Validators\CPF;
use TLib\Validations\Validators\NotEmpty;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;
use TCommerce\Core\Models\User;

class UserValidation extends Validation
{

	protected static $_createWhiteList = [
		'name', 'email', 'pj', 'cpf', 'rg', 'cnpj', 'birth_date'
	];

	protected static $_updateWhiteList = [
		'name', 'email', 'pj', 'cpf', 'rg', 'cnpj', 'birth_date'
	];

	public function validate($data = null, $entity = null)
	{
		if($this->mode == Validation::CREATE)
		{
			$this->add(
				['name', 'email', 'password'],
				new PresenceOf(['cancelOnFail' => true])
			);
		}

		if(array_key_exists('pj', $data))
		{
			$this->add(
				'pj',
				new InclusionIn(['domain' => [0, 1]])
			);
		}

		if(array_key_exists('name', $data))
		{
			$this->add(
				'name',
				new NotEmpty()
			);
		}

		if(array_key_exists('email', $data))
		{
			$this->add(
				'email',
				new Email(['cancelOnFail' => true])
			);
			$this->add(
				'email',
				new Uniqueness(['model' => new User()])
			);
		}

		if(array_key_exists('password', $data))
		{
			$this->add(
				'password',
				new StringLength(['min' => 8])
			);
		}

		if(array_key_exists('cpf', $data))
		{
			$this->add(
				'cpf',
				new CPF()
			);
		}

		if(array_key_exists('rg', $data))
		{
			$this->add(
				'rg',
				new Regex(
					[
						'message' => 'RG entered is not valid',
						'pattern' => '/^(\\d+\\.)+\\d*-?\\d+$/'
					]
				)
			);
		}

		if(array_key_exists('cnpj', $data))
		{
			$this->add(
				'cnpj',
				new CNPJ()
			);
		}

		if(array_key_exists('birth_date', $data))
		{
			$this->add(
				'birth_date',
				new Date(['format' => 'd/m/Y'])
			);
		}

		return parent::validate($data, $entity);
	}

}
