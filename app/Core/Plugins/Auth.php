<?php

namespace TCommerce\Core\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

/**
 * Class Auth
 *
 * Class to intermediate routes that require login
 *
 * @property \TCommerce\Core\Security\Auth $auth
 */
class Auth extends Plugin
{

	protected $_loginUri;

	protected $_unprotectedResources;

	public function __construct($loginUri, array $unprotectedResources = null)
	{
		$this->setLoginUri($loginUri);

		$ur = ['index' => ['login', 'logout']];
		if(!is_null($unprotectedResources)) {
			$ur = array_merge($ur, $unprotectedResources);
		}
		$this->setUnprotectedResources($ur);
	}

	/**
	 * Gets the login URI
	 *
	 * @return array
	 */
	public function getLoginUri()
	{
		return $this->_loginUri;
	}
	/**
	 * Sets the login URI
	 *
	 * @param string $loginUri
	 *
	 * @return $this
	 */
	public function setLoginUri($loginUri)
	{
		$this->_loginUri = $loginUri;

		return $this;
	}

	/**
	 * Gets the resources that don't need login
	 *
	 * @return array
	 */
	public function getUnprotectedResources()
	{
		return $this->_unprotectedResources;
	}
	/**
	 * Sets the resources that don't need login
	 *
	 * @param array $unprotectedResources
	 *
	 * @return $this
	 */
	public function setUnprotectedResources(array $unprotectedResources)
	{
		$this->_unprotectedResources = $unprotectedResources;

		return $this;
	}

	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
	{
		$controller = strtolower($dispatcher->getControllerName());
		$action = $dispatcher->getActionName();

		if(
			(!isset($this->_unprotectedResources[$controller]) || !in_array($action, $this->_unprotectedResources[$controller], true)) &&
			!$this->auth->check()
		) {
			$query = [];
			if(($pos = strpos($this->_loginUri, '?')) !== false)
			{
				$login_uri = substr($this->_loginUri, 0, $pos);
				parse_str(substr($this->_loginUri, $pos + 1), $query);
			} else {
				$login_uri = $this->_loginUri;
			}
			$query['redirect'] = $this->request->getURI();

			/**
			 * @todo Set correct status code for login redirect
			 */
			$this->response->redirect($login_uri . '?' . http_build_query($query));
			return false;
		}

		return true;
	}

}
