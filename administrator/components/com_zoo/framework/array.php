<?php
/**
* @package   ZOO Component
* @file      array.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YArray
		Array Object Class.
*/
class YArray extends ArrayObject {

	/*
		Function: get
			Get a value from array

		Parameters:
			$key - Array key
			$default - Default value, return if key was not found

		Returns:
			Mixed
	*/	
	public function get($key, $default = null) {
		
		if ($this->offsetExists($key)) {
			return $this->offsetGet($key);
		}
		
		return $default;
	}

	/*
		Function: set
			Set a array value

		Parameters:
			$key - Array key
			$value - Value

		Returns:
			YArray
	*/	
	public function set($key, $value) {
		$this->offsetSet($key, $value);
		return $this;
	}

	/*
		Function: find
			Find a key also in nested arrays/objects 

		Parameters:
			$key - Search key (e.g config.database.myvalue)
			$default - Default value, return if key was not found
			$separator - Separator for array/object search key

		Returns:
			Mixed
	*/	
	public function find($key, $default = null, $separator = '.') {

		$key   = (string) $key;
		$value = $this->get($key);

		// check if key exists in array
		if ($value !== null) {
			return $value;
		}

		// explode search key and init search data
		$parts = explode($separator, $key);
		$data  =& $this;
				
		foreach ($parts as $part) {
			
			// handle ArrayObject
			if ($data instanceof ArrayObject) {

				if (!$data->offsetExists($part)) {
					return $default;
				}

				$data =& $data->offsetGet($part);
				continue;
			}
			
			// handle object
			if (is_object($data)) {

				if (!isset($data->$part)) {
					return $default;
				}
				
				$data =& $data->$part;
				continue;
			}
			
			// handle array
			if (!isset($data[$part])) {
				return $default;
			}
			
			$data =& $data[$part];
		}
		
		// return existing value
		return $data;
	}

	/*
		Function: searchRecursive
			Find a value also in nested arrays/objects

		Parameters:
			$needle - the value to search for

		Returns:
			Mixed
	*/
	public function searchRecursive($needle) {
		$aIt = new RecursiveArrayIterator($this);
		$it	 = new RecursiveIteratorIterator($aIt);

		while ($it->valid()) {
			if ($it->current() == $needle) {
				return $aIt->key();
			}

			$it->next();
		}

		return false;
	}

		/*
		Function: flattenRecursive
			Return flattened array copy. Keys are NOT preserved.

		Returns:
			array
	*/
	public function flattenRecursive() {
		$flat = array();
		foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($this), RecursiveIteratorIterator::SELF_FIRST) as $key => $value) {
			if (!is_array($value)) {
				$flat[] = $value;
			}
		}

		return $flat;
	}

}