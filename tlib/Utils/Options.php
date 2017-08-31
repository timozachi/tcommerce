<?php

namespace TLib\Utils;

use InvalidArgumentException;

/**
 * TLib\Utils\Options
 *
 * A simple options container, to validate options and hold default values
 */
class Options
{

	protected $_options = [];
	protected $_validation = [];

	public function __construct(array $defaults)
	{
		$this->_options = $defaults;
	}

	/**
	 * @param string $key
	 * @param Closure|callable $validation
	 */
	public function setValidation($key, $validation = null)
	{
		$this->_validation[$key] = $validation;
	}
	
	/**
	 * Set's one or many options
	 *
	 * @param array $options
	 */
	public function set(array $options)
	{
		$this->_validateSupportedOptions($options);
		
		$this->_options = array_merge($this->_options, $options);
	}
	
	/**
	 * Get's one or all options
	 *
	 * @param string $key [optional]
	 *
	 * @return mixed
	 */
	public function get($key = null)
	{
		if(!is_null($key) && !isset($this->_options[$key]))
		{
			throw new InvalidArgumentException('Invalid key: ' . $key);
		}
		
		return !is_null($key) ? $this->_options[$key] : $this->_options;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function validate($key, $value)
	{
		$valid = true;
		if(isset($this->_validation[$key]))
		{
			$validation = $this->_validation[$key];
			if($validation instanceof \Closure) $valid = $validation($value);
			elseif(is_callable($validation)) $valid = call_user_func($validation, $value);
		}

		if(!$valid)
		{
			throw new InvalidArgumentException('Invalid option value for key: ' . $key);
		}
	}
	
	/**
	 * Get's current options optionally overriden by new options
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function getMerged(array $options = null)
	{
		$this->_validateSupportedOptions($options);
		
		return array_merge($this->_options, $options);
	}
	
	protected function _validateSupportedOptions(array $options)
	{
		if(($diff = array_diff_key($options, $this->_options)))
		{
			throw new InvalidArgumentException(
				'The following options are not supported: ' . implode(', ', array_keys($diff))
			);
		}
	}
	
}
