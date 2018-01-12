<?php

namespace TCommerce\Api\Plugins;

use Exception as PHPException;
use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\User\Plugin;
use TCommerce\Api\Exceptions\APIException;
use TCommerce\Core\Plugins\Exception as CoreExceptionPlugin;

class Exception extends CoreExceptionPlugin
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

		if ($exception instanceof APIException)
		{
			$obj = [
				'controller' => 'errors',
				'action' => 'exception',
				'params' => [$exception]
			];
		}
		elseif (
			$exception instanceof DispatcherException &&
			in_array(
			    $exception->getCode(),
                [Dispatcher::EXCEPTION_HANDLER_NOT_FOUND, Dispatcher::EXCEPTION_ACTION_NOT_FOUND],
                true)
		) {
			$obj = [
				'controller' => 'errors',
				'action'     => 'show404'
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

    /**
     * @param PHPException $exception
     *
     * @return array
     */
    protected function getForwardObj(PHPException $exception)
    {
        $obj = parent::getForwardObj($exception);

        if ($this->config->env !== 'prod' && empty($obj['params']))
        {
            $obj['params'] = [
                null,
                'exception' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                ]
            ];
        }

        return $obj;
    }

}
