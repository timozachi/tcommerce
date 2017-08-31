<?php

namespace TCommerce\Core\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\User\Plugin;

class ForceSsl extends Plugin
{

	public function beforeDispatchLoop(Event $event, MvcDispatcher $dispatcher)
	{
		if(
			$this->config->application->forceSSL &&
			$this->request->getHeader('HTTP_X_FORWARDED_PROTO') !== 'https' &&
			$this->request->getScheme() !== 'https'
		) {
            $this->response->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

            return false;
		}

		return true;
	}

}
