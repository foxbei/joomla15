<?php
/**
* @package   ZOO Component
* @file      object.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YObject
		Base object class.
*/
class YObject {

	/*
    	Function: bind
    	  Binds a named array/hash to this object.
		
		Parameters:
	      from   - An associative array or object.
	      ignore - An array or space separated list of fields not to bind.
	
	   Returns:
	      Boolean
 	*/
	public function bind($from, $ignore = array()) {

		$fromArray	= is_array($from);
		$fromObject	= is_object($from);

		if (!$fromArray && !$fromObject) {
			throw new YObjectException(get_class($this).'::bind failed. Invalid from argument');
		}
		
		if (!is_array($ignore)) {
			$ignore = explode(' ', $ignore);
		}
		
		foreach (get_object_vars($this) as $k => $v) {
			
			// ignore protected attributes
			if ('_' == substr($k, 0, 1)) {
				continue;
			}
			
			// internal attributes of an object are ignored
			if (!in_array($k, $ignore)) {
				if ($fromArray && isset($from[$k])) {
					$this->$k = $from[$k];
				} else if ($fromObject && isset($from->$k)) {
					$this->$k = $from->$k;
				}
			}
		}
		
		return true;
	}

}

/*
	Class: YObjectException
*/
class YObjectException extends YException {}