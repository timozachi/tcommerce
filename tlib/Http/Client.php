<?php

namespace TLib\Http;

use Exception;
use TLib\Utils\Options;

/**
 * Get the default User-Agent string to useS
 *
 * @todo Under Construction
 *
 * @return string
 */
function default_user_agent()
{
	static $default_agent = null;

	if (! $default_agent)
	{
		$default_agent = 'TLib/Http/Client/' . Client::VERSION;
		if(function_exists('curl_version'))
		{
			$version = \curl_version();
			$default_agent .= ' curl/' . $version['version'];
		}
		$default_agent .= ' PHP/' . PHP_VERSION;
	}

	return $default_agent;
}

/**
 * TLib\Http\Client
 * 
 * @todo Under Construction
 *
 * A class to make external requests
 */
class Client
{

	const VERSION = '1.0';

	/** @var Options */
	protected $_options;
	
	public function __construct(array $options = null)
	{
		if(!extension_loaded('curl'))
		{
			throw new \Exception('curl extension is required to use this class');
		}
		
		$this->_options = new Options($this->_getDefaultOptions($options));
	}
	
	/**
	 * Get's or Set's the options
	 *
	 * @param array $options
	 *
	 * @return array|$this
	 */
	public function options(array $options = null)
	{
		if(is_null($options)) return $this->_options->get();
		
		$this->_options->set($options);
		
		return $this;
	}
	
	protected function _getDefaultOptions(array $options)
	{
		$defaults = [
			'base_uri'        => '',
			'allow_redirects' => true,
			'http_errors'     => true,
			'decode_content'  => true,
			'verify'          => true,
		    'timeout'         => 10
		];
		
		$defaults = array_merge($defaults, $options);
		if(!isset($defaults['headers']))
		{
			$defaults['headers'] = ['User-Agent' => default_user_agent()];
		}
		else
		{
			foreach(array_keys($defaults['headers']) as $name)
			{
				if(strtolower($name) === 'user-agent') return $defaults;
			}
			$defaults['User-Agent'] = default_user_agent();
		}

		return $defaults;
	}
	
}
