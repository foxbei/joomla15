<?php
/**
* @package   ZOO Component
* @file      type.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: TypeHelper
   The Helper Class for item
*/
class TypeHelper {
	
	/*
		Function: setUniqueIndentifier
			Sets a unique type identifier

		Parameters:
			$type - Type object

		Returns:
			Type
	*/	
	public static function setUniqueIndentifier($type) {	
		if ($type->id != $type->identifier) {
			// check identifier
			$tmp_identifier = $type->identifier;
			$i = 2;
			while (file_exists($type->getXMLFile($tmp_identifier))) {
				$tmp_identifier = $type->identifier . '-' . $i;
			}
			$type->identifier = $tmp_identifier;
		}
		return $type;
	}
}