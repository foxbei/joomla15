<?php
/**
* @package   ZOO Component
* @file      utility.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YUtility
		The general Utility Class.
*/
class YUtility {

	/*
		Function: generateUUID
			Generates a universally unique identifier (UUID v4) according to RFC 4122
			Version 4 UUIDs are pseudo-random.

		Returns:
			String
	*/	
	public static function generateUUID() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
	}

	/*
		Function: debugInfo
			Retreive debug information from debug trace array.

		Returns:
			String
	*/	
	public static function debugInfo($trace, $index = 0) {
		
		if (isset($trace[$index])) {
			$file = str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $trace[$index]['file']));
			$line = $trace[$index]['line'];
			
			return sprintf('File: %s, Line: %s', $file, $line);
		}

		return null;
	}

}