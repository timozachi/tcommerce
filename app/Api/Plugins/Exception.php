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
