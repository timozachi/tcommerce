<?php

namespace TCommerce\Core\Controllers;

use Phalcon\Http\Response;
use Phalcon\Logger\Adapter\File;
use Phalcon\Mvc\Controller as PhalconController;

/**
 * @property File $log
 */
class Controller extends PhalconController
{

	/**
	 * @param mixed $data [optional]
	 * @param int $statusCode [optional]
	 * @param array $headers [optional] Extra headers to be sent
	 *
	 * @return Response a response containing json data encoded
	 */
	public function createJsonResponse($data = null, $statusCode = 200, $headers = [])
	{
		$response = new Response();

		$response->setJsonContent($data);
		$response->setStatusCode($statusCode);
		$response->setContentType('application/json');
		$response->setContentLength(strlen($response->getContent()));

		foreach($headers as $name=>$header)
		{
			$response->setHeader($name, $header);
		}

		$this->di->setShared('response', $response);

		return $response;
	}

	/**
	 * @param string $template The template name
	 * @param array $vars [optional] Response status code
	 * @param int $status [optional] Response status code
	 * @param array $headers [optional] Extra meta data about the response
	 *
	 * @return void
	 */
	public function view($template = null, $vars = [], $status = 200, $headers = [])
	{
		if(!empty($template)) $this->view->pick($template);
		if(!empty($vars)) $this->view->setVars($vars);

		$this->response->setStatusCode($status);
		foreach($headers as $header_key=>$header_val)
		{
			$this->response->setHeader($header_key, $header_val);
		}
	}

}
