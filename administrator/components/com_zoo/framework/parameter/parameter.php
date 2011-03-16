<?php
/**
* @package   ZOO Component
* @file      parameter.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YParameter
		Parameter Class.
*/
class YParameter {

    /*
		Variable: _data
			Parameter data.
    */
	protected $_data;
	
	/*
		Function: __construct
			Constructor

		Parameters:
			$data - Array or Object
	*/	
	public function __construct($data = array()) {
		$this->_data = new YArray($data);
	}

	/*
		Function: get
			Get a parameter

		Parameters:
			$name - Name of the parameter
			$default - Default value, return if parameter was not found

		Returns:
			Mixed
	*/	
	public function get($name, $default = null) {
		$name = (string) $name;
		
		if (preg_match('/\.$/', $name)) {

			$values = array();
			
			foreach ($this->_data as $key => $value) {
				if (strpos($key, $name) === 0) {
					$values[substr($key, strlen($name))] = $value;
				}
			}
			
			if (!empty($values)) {
				return $values;
			}
			
		} else if ($this->_data->offsetExists($name)) {
			return $this->_data->offsetGet($name);
		}
		
		return $default;
	}

	/*
		Function: set
			Set a parameter

		Parameters:
			$name - Name of the parameter
			$value - Value of the parameter

		Returns:
			YParameter
	*/	
	public function set($name, $value) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {

			$values = is_object($value) ? get_object_vars($value) : is_array($value) ? $value : array();

			foreach ($values as $key => $val) {
				$this->_data->offsetSet($name.$key, $val);
			}
			
		} else {
			$this->_data->offsetSet($name, $value);
		}
	
		return $this;
	}

	/*
		Function: remove
			Remove a parameter

		Parameters:
			$name - Name of the parameter

		Returns:
			YParameter
	*/	
	public function remove($name) {
		$name = (string) $name;

		if (preg_match('/\.$/', $name)) {
			
			$keys = array();

			foreach ($this->_data as $key => $value) {
				if (strpos($key, $name) === 0) {
					$keys[] = $key;
				}
			}
	
			foreach ($keys as $key) {
				$this->_data->offsetUnset($key);
			}
			
		} else {
			$this->_data->offsetUnset($name);
		}
	
		return $this;
	}

	/*
		Function: clear
			Clear parameter data

		Returns:
			YParameter
	*/	
	public function clear() {

		$this->_data = new YArray();
		
		return $this;
	}

	/*
		Function: toString
			Get parameter as array

		Returns:
			Array
	*/	
	public function toArray() {
		return $this->_data->getArrayCopy();
	}

	/*
		Function: toObject
			Get parameter as object

		Returns:
			stdClass
	*/	
	public function toObject() {
		$object = new stdClass();
		
		foreach ($this->_data as $name => $value) {
			$object->$name = $value;
		}
		
		return $object;
	}		

	/*
		Function: toString
			Get parameter as formatted string

		Returns:
			String
	*/	
	public function toString($format = 'json') {
		return YParameterFormat::getInstance($format)->objectToString($this->_data);
	}

	/*
		Function: loadArray
			Load a associative array of values

		Parameters:
			$array - Array of values

		Returns:
			YParameter
	*/	
	public function loadArray(array $array) {

		foreach ($array as $name => $value) {
			$this->_data->offsetSet($name, $value);
		}

		return $this;
	}
	
	/*
		Function: loadArray
			Load accessible non-static variables of a object

		Parameters:
			$object - Object with values

		Returns:
			YParameter
	*/	
	public function loadObject($object) {

		if (is_object($object)) {
			foreach (get_object_vars($object) as $name => $value) {
				$this->_data->offsetSet($name, $value);
			}
		}

		return $this;
	}

	/*
		Function: loadString
			Load parameter from formatted string

		Returns:
			String
	*/	
	public function loadString($data = null, $format = 'json') {
		$this->_data = new YArray((array) YParameterFormat::getInstance($format)->stringToObject($data));
		return $this;
	}

}