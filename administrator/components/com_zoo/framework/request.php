<?php
/**
* @package   ZOO Component
* @file      request.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YRequest
		This class serves to provide a common interface to access request variables.
*/
class YRequest extends JRequest {

	public static function getArray($name, $default = array(), $type = 'array', $hash = 'default', $mask = 0) {
 
		// explode name
		$parts = explode('.', $name);

		if (count($parts) > 1) {
			$name = $parts[0];
			$key  = $parts[1];
		}

		$array = self::getVar($name, $default, $hash, 'array', $mask);

		if (isset($key)) {

			if (is_array($array) && array_key_exists($key, $array)) {
				$value = $array[$key];
				settype($value, $type);
				return $value;
			}
			
			return $default;
		}

		if ($type == 'string') {
			JArrayHelper::toString($array);
		} else if ($type == 'int') {
			JArrayHelper::toInteger($array);
		}

		return $array;
	}

}