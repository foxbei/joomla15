<?php
/**
* @package   ZOO Component
* @file      exception.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YException
*/
class YException extends Exception {
	
	public function __toString() {
		// Use JText to translate exception message
		return JText::_($this->getMessage()); 
	}
	
}