<?php

namespace TCommerce\Core\Security;

use TLib\Http\Input;
use Phalcon\Di\Injectable;
use TCommerce\Core\Http\API;

class Auth extends Injectable
{

	/** @var API */
	protected $_api;

	/** @var string */
	protected $_sessionPrefix;

	/** @var array */
	protected $_user;

	public function __construct($sessionPrefix = null)
	{
		if(is_null($sessionPrefix)) $sessionPrefix = str_replace('\\', '-', __CLASS__) . '-';
		$this->setSessionPrefix($sessionPrefix);
	}

	/**
	 * gets the session prefix to be used
	 *
	 * @return string
	 */
	public function getSessionPrefix()
	{
		return $this->_sessionPrefix;
	}
	/**
	 * Sets the session prefix to be used
	 *
	 * @param string $sessionPrefix
	 *
	 * @return $this
	 */
	public function setSessionPrefix($sessionPrefix = null)
	{
		$this->_sessionPrefix = $sessionPrefix;

		return $this;
	}

	/**
	 * @return API
	 */
	public function getAPI()
	{
		if(is_null($this->_api))
		{
			$config = $this->config->api->toArray();
			$config['dataAsObject'] = false;
			$this->_api = new API($config);
		}

		return $this->_api;
	}

	public function logout()
	{
		$this->session->remove($this->_sessionPrefix . 'user-id');
		$this->session->remove($this->_sessionPrefix . 'user-authorization');
		$this->session->remove($this->_sessionPrefix . 'user-cache-expires');
		$this->_user = null;
	}

	public function getAuthorization()
	{
		if($authorization = $this->session->get($this->_sessionPrefix . 'user-authorization'))
		{
			if((time() + 10) > $authorization['expires'])
			{
				if((time() + 10) <= $authorization['refresh_expires'])
				{
					$info = $this->getAPI()->post(
						'auth/refresh',
						null,
						['authorization' => $authorization['refresh_token']]
					);

					if(!$info['is_error'])
					{
						$data = $info['data']['authorization'];
						$authorization['token'] = $data['token'];
						$authorization['expires'] = $data['expires'];
						$this->session->set($this->_sessionPrefix . 'user-authorization', $authorization);

						return $authorization['token'];
					}
				}
			}
			else
			{
				return $authorization['token'];
			}
		}

		$this->session->remove($this->_sessionPrefix . 'user-id');
		$this->session->remove($this->_sessionPrefix . 'user-authorization');
		$this->session->remove($this->_sessionPrefix . 'user-cache-expires');

		return null;
	}

	/**
	 * Attempts a login at the API
	 *
	 * @param array|null $input
	 * @param array|null $errors
	 *
	 * @return bool
	 */
	public function attempt($input = null, &$errors = null)
	{
		if(is_null($input)) $input = Input::post();

		$info = $this->getAPI()->post(
			'/users/login',
			$input
		);

		if(!$info['is_error'])
		{
			$data = $info['data'];
			$this->session->set($this->_sessionPrefix . 'user-id', $data['user']['id']);
			$this->session->set($this->_sessionPrefix . 'user-authorization', $data['authorization']);

			return true;
		}

		$errors = $info['errors'];

		return false;
	}

	public function check()
	{
		if($this->getAuthorization()) {
			return true;
		}

		return false;
	}

	public function getId()
	{
		if($this->check()) {
			return $this->session->get($this->_sessionPrefix . 'user-id');
		}

		return false;
	}

	public function getUser()
	{
		if($this->check())
		{
			if(
				is_null($this->session->get($this->_sessionPrefix . 'user-cache-expires')) ||
				time() > $this->session->get($this->_sessionPrefix . 'user-cache-expires')
			) {
				$info = $this->getAPI()->get('/users/me', ['authorization' => $this->getAuthorization()]);
				if($info['is_error']) {
					throw new \Exception('An unexpected error occurred while trying to get user info');
				}

				$this->_user = $info['data']['user'];
			}

			return $this->_user;
		}

		return null;
	}

	public function getUserInfo($key, $default = null)
	{
		if(($user = $this->getUser()) && array_key_exists($key, $user)) {
			return $user[$key];
		}

		return $default;
	}

	public function refreshUser()
	{
		$this->session->remove($this->_sessionPrefix . 'user-cache-expires');
	}

	protected function _setData($data)
	{
		$this->session->set($this->_sessionPrefix . 'user-id', $data['user']['id']);
		$this->session->set($this->_sessionPrefix . 'user', $data['user']);
		//User cache expires in 5 minutes
		$this->session->set($this->_sessionPrefix . 'user-cache-expires', strtotime('+5 min'));
	}

}
