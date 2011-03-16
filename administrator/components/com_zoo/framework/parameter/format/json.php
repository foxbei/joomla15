<?php
/**
* @package   ZOO Component
* @file      json.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YParameterFormatJSON
		Parameter JSON Format Class.
*/
class YParameterFormatJSON extends YParameterFormat {

	/*
		Function: stringToObject
			Converts an JSON formatted string into an object

		Parameters:
			data - Raw parameter string
	*/			
	function stringToObject($data) {
		$data = JString::trim($data);

		if ((JString::substr($data, 0, 1) != '{') && (JString::substr($data, -1, 1) != '}')) {
			$object = JRegistryFormat::getInstance('INI')->stringToObject($data);
		} else {
			$object = json_decode($data);
		}

		return $object;
	}

	/*
		Function: stringToObject
			Converts an object into a JSON formatted string

		Parameters:
			object - Data object
	*/		
	function objectToString($object) {
		return json_encode($object);
	}

}