<?php

namespace TLib\Utils;

/**
 * TLib\Utils\Stringify
 *
 * A simple class to stringify stuff to a text readable format
 */
class Stringify
{
	
	/** @var Options */
	protected $_options;
	
	public function __construct(array $options = null)
	{
		$this->_options = new Options([
			'charset' => 'UTF-8',
			'max_array_elements' => 3,
			'max_string_length' => 128,
			'max_func_args' => 10
		]);
		$this->options($options);
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

	/**
	 * Receives a variable and returns a string representation of the variable
	 *
	 * @param $var
	 *
	 * @return string
	 */
	public function variable($var)
	{
		$var_str = '';

		if(is_null($var)) $var_str = 'null';
		elseif(is_int($var) || is_float($var)) $var_str = (string)$var;
		elseif(is_object($var)) $var_str = 'Object: ' . get_class($var);
		elseif(is_array($var))
		{
			$comma = ''; $assoc = !isset($var[0]); $i = 0;
			$mae = $this->_options->get('max_array_elements');
			foreach($var as $key=>$val)
			{
				if($i >= $mae)
				{
					$var_str .= $comma . '...';
					break;
				}

				$var_str .= $comma . ($assoc ? "'{$key}' => " : '') . $this->variable($val);
				$comma = ', ';
				$i++;
			}
			$var_str = '[' . $var_str . ']';
		}
		else
		{
			$options = $this->_options->get();
			
			$var = (string)$var;
			$var_str = mb_substr($var, 0, $options['max_string_length'], $options['charset']);
			if(mb_strlen($var, $options['charset']) > $options['max_string_length']) $var_str .= '...';
			$var_str =  "'" . $var_str . "'";
		}

		return $var_str;
	}

	/**
	 * Get's current backtrace and transforms it to a readable string
	 *
	 * @param array $backtrace [optional]
	 * @param int $skipFirst [optional] Skip the first x entries
	 * @param array $steps [optional]
	 *
	 * @return string
	 */
	public function backtrace(array $backtrace = null, $skipFirst = 0, &$steps = [])
	{
		$trace_str = '';
		if(is_null($backtrace))
		{
			$backtrace = debug_backtrace();
			$skipFirst++;
		}
		array_splice($backtrace, 0, $skipFirst + 1);

		$i = 0;
		foreach($backtrace as $arr)
		{
			$s_line = '';
			if(isset($arr['class'])) $s_line .= $arr['class'] . '.';
			$args = [];

			if(!empty($arr['args']))
			{
				$mfa = $this->_options->get('max_func_args');
				foreach($arr['args'] as $j=>$val)
				{
					if($j < $mfa) $args[] = $this->variable($val);
					else
					{
						$args[] = '...';
						break;
					}
				}
			}

			$s_line .= $arr['function'] . '(' . implode(', ', $args) . ')';
			$line = (isset($arr['line']) ? $arr['line'] : 'unknown');
			$file = (isset($arr['file']) ? $arr['file'] : 'unknown');
			$s_line .= sprintf(' - line %d, file: %s', $line, $file);
			$steps[] = $s_line;

			$trace_str .= '#' . $i . ' ' . $s_line . "\n";

			$i++;
		}

		return $trace_str;
	}

}
