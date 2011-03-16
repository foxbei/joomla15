<?php
/**
* @package   ZOO Component
* @file      format.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YParameterFormat
		Parameter Format Class.
*/
class YParameterFormat extends JObject {
	
	/*
		Function: getInstance
			Returns a reference to a Format object, only creating it if it doesn't already exist.

		Parameters:
			format - Parameter format
	*/		
	function getInstance($format) {
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$format = strtolower($format);
		
		if (empty($instances[$format])) {
			$class = 'YParameterFormat'.$format;

			if (!class_exists($class)) {
				$path = dirname(__FILE__).'/format/'.$format.'.php';

				if (file_exists($path)) {
					require_once($path);
				} else {
					JError::raiseError(500, JText::_('Unable to load format class'));
				}
			}

			$instances[$format] = new $class();
		}

		return $instances[$format];
	}

	/*
		Function: stringToObject
			Converts an formatted string into an object

		Parameters:
			data - Raw parameter string
	*/		
	function stringToObject($data) {
		JError::raiseError(500, JText::_('Unable to call abstract method'));
	}

	/*
		Function: stringToObject
			Converts an object into a formatted string

		Parameters:
			object - Data object
	*/		
	function objectToString(&$object) {
		JError::raiseError(500, JText::_('Unable to call abstract method'));
	}

}