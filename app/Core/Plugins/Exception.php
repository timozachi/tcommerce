<?php

namespace TCommerce\Core\Plugins;

use Exception as PHPException;
use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\User\Plugin;
use TCommerce\Core\Exceptions\HttpException;

class Exception extends Plugin
{

    /**
     * A list of the exception types that should not be reported (logged)
     *
     * @var array
     */
    protected $_dontReport = [
        DispatcherException::class => [
            Dispatcher::EXCEPTION_HANDLER_NOT_FOUND, Dispatcher::EXCEPTION_ACTION_NOT_FOUND
        ],
        HttpException::class => null
    ];

    /**
     * Report or log an exception.
     *
     * @param PHPException $exception
     * @return void
     * @throws PHPException
     */
    public function report(PHPException $exception)
    {
        if (! $this->shouldReport($exception)) {
            return;
        }

        try {
            $logger = $this->log;
        } catch (PHPException $exception) {
            throw $exception;
        }

        $message = $exception->getMessage() . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
        error_log($message);
        $logger->error($message);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param PHPException $exception
     * @return bool
     */
    public function shouldReport(PHPException $exception)
    {
        foreach ($this->_dontReport as $type=>$codes)
        {
            if (
                $exception instanceof $type && (
                    is_null($codes) || in_array($exception->getCode(), $codes, true)
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
	 * Triggered before the dispatcher throws any exception
	 *
	 * @param Event $event
	 * @param MvcDispatcher $dispatcher
	 * @param PHPException $exception
	 *
	 * @return boolean
     * @throws \Exception
	 */
	public function beforeException(Event $event, MvcDispatcher $dispatcher, PHPException $exception)
	{
	    $this->report($exception);

        return $this->render($dispatcher, $exception);
	}

	/**
     * We will only render the exception if environment is prod,
     * otherwise let the debugger render the exception
     *
     * @param MvcDispatcher $dispatcher
     * @param PHPException $exception
     *
     * @return bool
     * @throws PHPException
     */
	protected function render(MvcDispatcher $dispatcher, PHPException $exception)
    {
        if (
            $this->config->env === 'prod' &&
            $obj = $this->getForwardObj($exception)
        ) {
            $dispatcher->forward($obj);

            return false;
        }

        return true;
    }

    /**
     * @param PHPException $exception
     *
     * @return array|null
     */
	protected function getForwardObj(PHPException $exception)
    {
        if (
            $exception instanceof DispatcherException &&
            in_array(
                $exception->getCode(),
                [Dispatcher::EXCEPTION_HANDLER_NOT_FOUND, Dispatcher::EXCEPTION_ACTION_NOT_FOUND],
                true
            )
        ) {
            return [
                'controller' => 'errors',
                'action'     => 'show404'
            ];
        }
        elseif ($exception instanceof HttpException)
        {
            return [
                'controller' => 'errors',
                'action' => 'httpException',
                'params' => [$exception]
            ];
        }

        return [
            'controller' => 'errors',
            'action'     => 'show500'
        ];
    }

}
