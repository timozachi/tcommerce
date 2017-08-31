<?php

namespace TCommerce\Core\Http;

use TLib\Utils\Options;
use TLib\Utils\Stringify;

class API
{

	const REQUEST_TYPE_JSON = 'application/json';
	const REQUEST_TYPE_URLENCODED = 'application/x-www-form-urlencoded';

	/** @var Options */
	protected $_options;

	protected $_requireAuthorization = true;

	protected $_statusCode = null;
	protected $_responseHeaders = null;
	protected $_responseJSON = null;

	public function __construct(array $options = null)
	{
		$this->_setDefaultOptions($options);
	}

	/**
	 * Gets current options
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options->get();
	}
	/**
	 * Set current options
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions(array $options)
	{
		 $this->_options->set($options);

		 return $this;
	}

	public function getStatusCode()
	{
		return $this->_statusCode;
	}

	public function isError()
	{
		return floor($this->_statusCode/100) != 2;
	}

	public function getResponseHeaders()
	{
		return $this->_responseHeaders;
	}

	public function getResponse($data = false)
	{
		return $data ? $this->_responseJSON['data'] : $this->_responseJSON;
	}

	public function getError()
	{
		return $this->_responseJSON['error'];
	}

	public function getErrors()
	{
		return $this->_responseJSON['errors'];
	}

	public function getAuthorization($options = null)
	{
		if(!is_null($options)) $o = $this->_options->getMerged($options);
		else $o = $this->_options->get();

		if(!$o['tokenFile']) {
			throw new \Exception('Token file not specified');
		}

		$json = null;
		if(file_exists($o['tokenFile'])) {
			$json = json_decode(file_get_contents($o['tokenFile']), true);
		}

		//10 segundos de margem de erro
		if(!$json || (time() + 10) > $json['expires'])
		{
			if(!$o['id'] || !$o['secret']) {
				throw new \Exception('Options ID and Secret are required');
			}

			$this->_requireAuthorization = false;
			$info = $this->post(
				'/auth/login',
				[
					'id' => $o['id'],
					'secret' => $o['secret']
				],
				$options
			);
			$this->_requireAuthorization = true;

			if($info['is_error']) {
				throw new \Exception('Invalid id and secret credentials for API');
			}

			$json = $info['data']['authorization'];
			file_put_contents($o['tokenFile'], json_encode($json));
		}

		return $json['token'];
	}

	public function call($method, $url, $data = null, $options = null)
	{
		if(!is_null($options)) $o = $this->_options->getMerged($options);
		else $o = $this->_options->get();

		$full_url = $o['baseUri'] . $url;

		$headers = [
			'Accept' => 'application/json'
		];
		if($this->_requireAuthorization) {
			$headers['Authorization'] = 'Bearer ' . (!empty($o['authorization']) ? $o['authorization'] :  $this->getAuthorization($options));
		}

		$input = '';
		if(!empty($data))
		{
			if($o['requestType'] == static::REQUEST_TYPE_JSON)
			{
				$ctype = 'application/json';
				$input = json_encode($data);
			}
			else
			{
				$ctype = 'application/x-www-form-urlencoded';
				$input = http_build_query($data);
			}

			$headers['Content-Type'] = $ctype;
			$headers['Content-Length'] = strlen($input);
		}

		$init_time = microtime(true);

		$final_headers = [];
		foreach($headers as $hname=>$hvalue)
		{
			$final_headers[] = "{$hname}: {$hvalue}";
		}

		$ch = curl_init($full_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //Custom method
		curl_setopt($ch, CURLOPT_HEADER, true); //Include header in the output
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Return the response
		curl_setopt($ch, CURLOPT_TIMEOUT, $o['timeout']); //Timeout without response
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //Follow redirects
		curl_setopt($ch, CURLOPT_MAXREDIRS, 4); //Max 4 redirects
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); //We want the request header string
		curl_setopt($ch, CURLOPT_HTTPHEADER, $final_headers); //Custom headers
		curl_setopt($ch, CURLOPT_POSTFIELDS, $input); //Post data

		if(!$o['verifyPeer']) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$this->_responseHeaders = $this->_responseJSON = null;
		$response = curl_exec($ch);
		if($response === false) {
			throw new \Exception('Curl error (' . curl_errno($ch) . '): ' . curl_error($ch));
		}
		$header_length = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$status_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$request_header = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		curl_close($ch);

		$request_body = $input;
		$response_header = substr($response, 0, $header_length);
		$response_body = substr($response, $header_length);
		$response_json = json_decode($response_body, true);
		if($response_json === false) {
			throw new \Exception('Invalid JSON response returned from the API');
		}

		$time = microtime(true) - $init_time;

		$response_headers_array = [];
		foreach(explode("\r\n", $response_header) as $header_string)
		{
			if(empty($header_string) || strpos($header_string,': ') === false) { continue; }
			list($name, $val) = explode(': ', $header_string);
			$response_headers_array[$name] = $val;
		}

		$this->_statusCode = $status_code;
		$this->_responseHeaders = $response_headers_array;
		$this->_responseJSON = $response_json;

		$log_files = [];
		if($o['logCalls'])
		{
			if(!$o['logFile']) {
				throw new \Exception('Log file must be specified in order to log calls');
			}

			$log_files[] = $this->_options->get('logFile');
		}
		if($o['logLongCalls'])
		{
			if($o['longCallsTimeout'] <= 0) {
				throw new \Exception('Long calls timeout must be bigger than 0');
			}
			if(!$o['longCallsLogFile']) {
				throw new \Exception('Long calls log file must be specified to log long calls');
			}

			if($o['longCallsTimeout'] <= $time) {
				$log_files[] = $o['longCallsLogFile'];
			}
		}

		if(!empty($log_files))
		{
			$log_str = str_repeat('-', 50) . date('d/m/Y H:i:s') . str_repeat('-', 50) . "\n\n";
			$log_str .= sprintf("TIME TAKEN: %d milliseconds\n", round($time * 1000, 2));
			$log_str .= sprintf("REQUEST_URI: %s %s\n", $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
			$log_str .= "URL: {$method} {$full_url}\n";
			$log_str .= "REQUEST:\n" .  $request_header . $request_body . "\n\n";
			$log_str .= "RESPONSE:\n" . $response . "\n\n";

			if($o['logTrace'])
			{
				$stringify = new Stringify();
				$log_str .= "TRACE:\n" . $stringify->backtrace() . "\n";
			}

			foreach($log_files as $lf)
			{
				$fh = fopen($lf, 'a');
				fwrite($fh, $log_str);
				fclose($fh);
			}
		}

		$return = [
			'status_code' => $status_code,
			'is_error' => floor($status_code/100) != 2,
			'headers' => $response_headers_array,
			'response' => $response_json
		];
		if($o['dataAsObject'])
		{
			$return['data'] = isset($response_json->data) ? $response_json->data : null;
			$return['error'] = isset($response_json->error) ? $response_json->error : null;
			$return['errors']= isset($response_json->errors) ? $response_json->errors : null;
		}
		else
		{
			$return['data'] = isset($response_json['data']) ? $response_json['data'] : null;
			$return['error'] = isset($response_json['error']) ? $response_json['error'] : null;
			$return['errors'] = isset($response_json['errors']) ? $response_json['errors'] : null;
		}

		return $return;
	}

	public function get($url, $options = null)
	{
		return $this->call('GET', $url, null, $options);
	}

	public function post($url, $data = null, $options = null)
	{
		return $this->call('POST', $url, $data, $options);
	}

	public function put($url, $data = null, $options = null)
	{
		return $this->call('PUT', $url, $data, $options);
	}

	public function delete($url, $data = null, $options = null)
	{
		return $this->call('DELETE', $url, $data, $options);
	}

	protected function _setDefaultOptions(array $optionsData = null)
	{
		$defaults = [
			'baseUri'             => isset($_SERVER['HTTP_HOST']) ?
				'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . isset($_SERVER['HTTP_HOST']) . '/' :
				'http://localhost/',
			'timeout'             => 10,
			'id'                  => null,
			'secret'              => null,
			'tokenFile'           => null,
			'authorization'       => null,
			'verifyPeer'          => true,
			'logCalls'            => false,
			'logTrace'            => false,
			'logFile'             => null,
			'logLongCalls'        => false,
			'longCallsTimeout'    => 3,
			'longCallsLogFile'    => null,
			'requestType'         => static::REQUEST_TYPE_JSON,
			'dataAsObject'        => true
		];

		$options = new Options($defaults);
		if(!is_null($optionsData)) $options->set($optionsData);

		$options->setValidation(
			'baseUri',
			function ($value) { return filter_var($value, FILTER_VALIDATE_URL); }
		);
		$options->setValidation('timeout', 'is_int');
		$options->setValidation('longCallsTimeout', 'is_int');
		$options->setValidation(
			'requestType',
			function ($value)
			{
				return in_array($value, [static::REQUEST_TYPE_JSON, static::REQUEST_TYPE_URLENCODED]);
			}
		);

		$this->_options = $options;
	}

}
