<?php

namespace TCommerce\Api\Plugins;

use TLib\Http\Input;
use Phalcon\Config;
use Phalcon\Events\Event;
use Phalcon\Http\Response\Headers;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class Log extends Plugin
{

	public function afterDispatchLoop(Event $event, Dispatcher $dispatcher)
	{
		/** @var Config $config */
		$config = $this->di->getConfig();

		$logger = new Logger(
			$config->api->logFile,
			['mode' => 'a']
		);

		$request_headers = '';
		foreach($this->request->getHeaders() as $header_name=>$header_value)
		{
			$request_headers .= "$header_name: $header_value\n";
		}
		$request_headers .= "\n";

		$response_headers = '';
		foreach($this->response->getHeaders()->toArray() as $header_name=>$header_value)
		{
			$response_headers .= "$header_name: $header_value\n";
		}
		$response_headers .= "\n";

		$logger->begin();
		$logger->info(
			str_repeat('-', 50) . "\n\n" .
			"URL: " . $this->request->getMethod() . ' ' . $this->request->getURI() . "\n\n" .
			"REQUEST: \n" . $request_headers . Input::raw() . "\n\n" .
			"RESPONSE: \n" . $response_headers . $this->response->getContent() . "\n"
		);
		$logger->commit();
	}

}
