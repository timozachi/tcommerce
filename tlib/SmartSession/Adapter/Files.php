<?php

namespace TLib\SmartSession\Adapter;

use Phalcon\Session\Adapter\Files as PhalconFiles;

/**
 * Class Files
 *
 * This class is a session files adapter for Phalcon, the main difference is that
 * it only starts when it's actually used
 *
 * @package TLib\SmartSession\Adapter
 */
class Files extends PhalconFiles
{

	protected $_smartStarted = false;

	public function __construct($options = null)
	{
		parent::__construct($options);
	}

	/**
	 * Starts the session (if headers are already sent the session will not be started)
	 */
	public function start()
	{
		$this->_smartStarted = true;
	}

	public function get($index, $defaultValue = null, $remove = false)
	{
		if(!static::_start(false)) {
			return $defaultValue;
		}

		return parent::get($index, $defaultValue, $remove);
	}

	public function set($index, $value)
	{
		static::_start(true);

		parent::set($index, $value);
	}

	public function has($index)
	{
		if(!static::_start(false)) {
			return false;
		}

		return parent::has($index);
	}

	public function remove($index)
	{
		if(!static::_start(false)) {
			return false;
		}

		parent::remove($index);
	}

	public function destroy($removeData = false)
	{
		if(!static::_start(false)) {
			return false;
		}

		parent::destroy($removeData);
	}

	protected function _start($force = false)
	{
		if(!$this->_smartStarted) {
			return false;
		} else if($this->_started) {
			return true;
		} else if($force || !empty($_COOKIE[$this->getName()])) {
			parent::start();

			return true;
		}

		return false;
	}

}
