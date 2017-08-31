<?php

namespace TCommerce\Api\Plugins;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Crypt;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;
use TCommerce\Api\Exceptions\APIException;
use TCommerce\Core\Models\Action;
use TCommerce\Core\Models\ApiRole;
use TCommerce\Core\Models\Role as RoleModel;
use TCommerce\Core\Models\RoleAction;

class Auth extends Plugin
{

	/** @var \stdClass */
	public static $jwt = null;
	/** @var int $apiId  */
	public static $apiId = null;
	/** @var int */
	public static $userId = null;

	/**
	 * Returns an existing or new access control list
	 *
	 * @returns AclList
	 */
	protected function _getAcl()
	{
		/**
		 * @todo Mudar lógica do ACL para carregar todos os roles e todas as permissões (cache),
		 * e fazer allow e deny de acordo com a necessidade
		 */

		$ids = []; $admin = false;

		$role_model = RoleModel::class;
		/** @var Role[] $roles */
		$roles = RoleModel::query()
			->columns(["$role_model.id, $role_model.admin"])
			->join(ApiRole::class, "ar.role_id = $role_model.id", 'ar')
			->where('ar.api_id = :api_id:', ['api_id' => static::$apiId])
			->execute();

		foreach($roles as $role)
		{
			if($role->admin) $admin = true;
			$ids[] = (int)$role->id;
		}

		$action_model = Action::class;
		$query = Action::query()
			->columns(["$action_model.id", "$action_model.key"]);
		if(!$admin)
		{
			$query->join(RoleAction::class, "ra.action_id = $action_model.id", 'ra')
				->where('ra.role_id IN (' . implode(',', $ids) . ')');
		}
		/** @var Action[] $actions */
		$actions = $query->execute();

		//All controllers/actions that requires authentication but all users have permission to access it
		$allowed_resources = [
			'auth' => 'refresh'
		];
		foreach($actions as $action)
		{
			list($controller, $action) = explode('@', $action->key);
			if(!isset($allowed_resources[$controller])) {
				$allowed_resources[$controller] = [];
			}

			if(strpos($action, '-') !== false) {
				array_merge($allowed_resources[$controller], explode('-', $action));
			} else {
				$allowed_resources[$controller][] = $action;
			}
		}

		$acl = new AclList(); $role = new Role('Tokens');
		$acl->setDefaultAction(Acl::DENY);
		$acl->addRole($role);

		foreach($allowed_resources as $controller=>$actions)
		{
			$acl->addResource(new Resource($controller), $actions);
			$acl->allow($role->getName(), $controller, $actions);
		}

		return $acl;
	}

	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
	{
		$controller = strtolower($dispatcher->getControllerName());
		$action = $dispatcher->getActionName();

		$allowed = [
			'auth' => ['login'],
			'errors' => ['exception', 'show400', 'show401', 'show403', 'show404', 'show500']
		];

		if(isset($allowed[$controller]) && in_array($action, $allowed[$controller], true)) {
			return true;
		}

		$authorization = $this->request->getHeader('Authorization');
		if(strpos($authorization, 'Bearer ') === 0) {
			$authorization = substr($authorization, 7);
		} else {
			$authorization = null;
		}

		$config = $this->config->jwt; $status_code = null; $exception = null;
		if($authorization)
		{
			try
			{
				Auth::$jwt = JWT::decode($authorization, $config->secret, [$config->algo]);

				if(!empty(static::$jwt->refresh) && $controller != 'auth' && $action != 'refresh')
				{
					$exception = new APIException(
						'cannot-use-refresh-token',
						'Cannot Use Refresh Token',
						'The specified token is a refresh token, you cannot use it as a regular token'
					);
					//Forbidden, that token does not have authorization to perform this action
					$status_code = 403;
				} else {
					static::$apiId = (int)static::$jwt->sub;
				}
			} catch(ExpiredException $e) {
				$exception = new APIException(
					'token-expired',
					'Token Expired',
					'The specified token has expired, please generate another one'
				);
			} catch(\Exception $e) {
				$exception = new APIException(
					'invalid-token',
					'Invalid Token',
					'The specified token is invalid, please generate one'
				);
			}

			if(static::$apiId)
			{
				if(!empty(static::$jwt->uid))
				{
					$crypt = new Crypt();
					static::$userId = (int)$crypt->decrypt(
						base64_decode(static::$jwt->uid),
						$this->config->security->encryptKey
					);
				}

				if($this->_getAcl()->isAllowed('Tokens', $controller, $action)) {
					return true;
				}
				/* else {
					//show403 bellow
				}*/
			}
		}
		else
		{
			$exception = new APIException(
				'token-required',
				'Token Required',
				'An authorization token is required'
			);
		}

		if($exception)
		{
			if(!$status_code) $status_code = 401; //Unauthorized

			$exception->setStatusCode($status_code);
			if($status_code == 401) {
				$exception->setHeaders(['WWW-Authenticate' => 'Bearer']);
			}
			$dispatcher->forward([
				'controller' => 'errors',
				'action' => 'exception',
				'params' => [$exception]
			]);
		}
		else
		{
			$dispatcher->forward([
				'controller' => 'errors',
				'action' => 'show403'
			]);
		}

		return false;
	}

	public function afterDispatchLoop(Event $event, Dispatcher $dispatcher)
	{
		$this->response->setHeader('X-Authenticated', static::$apiId ? 1 : 0);
		$this->response->setHeader('X-User-Id', static::$userId ? 1 : 0);
	}

}
