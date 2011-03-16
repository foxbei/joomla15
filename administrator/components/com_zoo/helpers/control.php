<?php
/**
* @package   ZOO Component
* @file      control.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JHTMLControl {

	/*
    	Function: selectdirectory
    		Returns directory select html string.
 	*/
	function selectdirectory($directory, $filter, $name, $value = null, $attribs = null) {

		// get directories
		$options = array(JHTML::_('select.option',  '', '- '.JText::_('Select Directory').' -'));
		$dirs    = YFile::readDirectory($directory, '', $filter);

		natsort($dirs);
		
		foreach ($dirs as $dir) {
			$options[] = JHTML::_('select.option', $dir, $dir);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $value);
	}

	/*
    	Function: selectfile
    		Returns file select html string.
 	*/
	function selectfile($directory, $filter, $name, $value = null, $attribs = null) {

		// get files
		$options = array(JHTML::_('select.option',  '', '- '.JText::_('Select File').' -'));
		$files   = YFile::readDirectoryFiles($directory, '', $filter);

		natsort($files);
		
		foreach ($files as $file) {
			$options[] = JHTML::_('select.option', $file, $file);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $value);
	}

	/*
    	Function: label
    		Returns label html string.
 	*/
	function label($name, $for = null, $attribs = null) {

		$html  = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		 }

		$for = ($for != '') ? 'for="'.$for.'"' : ''; 

		$html .= "\n\t<label $for $attribs />$name</label>\n";

		return $html;
	}
	
	/*
    	Function: textarea
    		Returns form textarea html string.
 	*/
	function textarea($name, $value = null, $attribs = null) {

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		 }

		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		 
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return "\n\t<textarea name=\"$name\" $attribs />".$value."</textarea>\n";

	}

 	/*
    	Function: text
    		Returns form text input html string.
 	*/
	function text($name, $value = null, $attribs = null) {
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		return JHTML::_('control.input', 'text', $name, $value, $attribs);
	}

 	/*
    	Function: hidden
    		Returns form hidden input html string.
 	*/
	function hidden($name, $value = null, $attribs = null) {
		return JHTML::_('control.input', 'hidden', $name, $value, $attribs);
	}

 	/*
    	Function: input
    		Returns form input html string.
 	*/
	function input($type, $name, $value = null, $attribs = null) {

		$html = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		 }

		$html .= "\n\t<input type=\"$type\" name=\"$name\" value=\"$value\" $attribs />\n";

		return $html;
	}

}