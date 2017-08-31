<?php

namespace TCommerce\Core\Plugins;

use Exception as PHPException;
use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\User\Plugin;
use TCommerce\Core\Http\Exceptions\HttpException;

class Exception extends Plugin
{

	/**
	 * Triggered before the dispatcher throws any exception
	 *
	 * @param Event $event
	 * @param MvcDispatcher $dispatcher
	 * @param PHPException $exception
	 *
	 * @return boolean
	 */
	public function beforeException(Event $event, MvcDispatcher $dispatcher, PHPException $exception)
	{
		error_log($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());

		if(
			$exception instanceof DispatcherException &&
			in_array($exception->getCode(), [Dispatcher::EXCEPTION_HANDLER_NOT_FOUND, Dispatcher::EXCEPTION_ACTION_NOT_FOUND], true)
		) {
			$obj = [
				'controller' => 'errors',
				'action'     => 'show404'
			];
		}
		elseif($exception instanceof HttpException)
		{
			$obj = [
				'controller' => 'errors',
				'action' => 'httpException',
				'params' => [$exception]
			];
		}
		else
		{
			$obj = [
				'controller' => 'errors',
				'action'     => 'show500'
			];
		}

		$dispatcher->forward($obj);

		return false;
	}

}
