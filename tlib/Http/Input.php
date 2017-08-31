<?php

namespace TLib\Http;

class Input
{

	protected static $_phpInput = null;
	protected static $_input = null;

	protected static function _getStructuredInput()
	{
		if(is_null(static::$_input))
		{
			$ctype = 'text/plain';
			if(isset($_SERVER['CONTENT_TYPE'])) $ctype = strtolower($_SERVER['CONTENT_TYPE']);

			$pos = strpos($ctype, ';');
			if($pos !== false) $ctype = trim(substr($ctype, 0, $pos));

			$input = null;
			switch($ctype)
			{
				case 'application/x-www-form-urlencoded':
				case 'multipart/form-data':
					$input = $_POST;
					break;
				case 'application/json':
				case 'text/json':
					$input = json_decode(static::raw(), true);
					break;
				default:
					break;
			}

			if(is_null($input)) $input = [];
			static::$_input = $input;
		}

		return static::$_input;
	}

	/**
	 * Get an item from the $_GET data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($key = null, $default = null)
	{
		if(is_null($key)) return $_GET;

		if(!isset($_GET[$key])) return $default;
		return $_GET[$key];
	}

	/**
	 * Get an item from post data (text/json, application/json or application/x-www-form-urlencoded).
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function post($key = null, $default = null)
	{
		$input = static::_getStructuredInput();

		if(is_null($key)) return $input;

		if(!isset($input[$key])) return $default;
		return $input[$key];
	}

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE)
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function all($key = null, $default = null)
	{
		$data = static::post($key, $default);
		if(is_null($data))
		{
			$data = static::get($key, $default);
		}

		return $data;
	}

	/**
	 * Gets raw input as text
	 *
	 * @return string
	 */
	public static function raw()
	{
		if(is_null(static::$_phpInput))
		{
			static::$_phpInput = file_get_contents('php://input');
		}

		return static::$_phpInput;
	}

}
