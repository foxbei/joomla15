<?php
/**
* @package   ZOO Component
* @file      file.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementFile
		The file element class
*/
abstract class ElementFile extends Element {

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();
		
		// set defaults
		$params = JComponentHelper::getParams('com_media');
		$this->_config->set('directory', $params->get('file_path'));
	}
	
	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {

		// init vars
		$file 	   = $this->_data->get('file');
		$directory = $this->_config->get('directory');
		$filepath  = rtrim(JPATH_ROOT.DS.$directory, '/').'/'.$file;				
		
		return !empty($file) && is_readable($filepath) && is_file($filepath);
	}
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){
		return JHTML::_('control.selectfile', JPATH_ROOT.DS.$this->_config->get('directory'), false, 'elements[' . $this->identifier . '][file]', $this->_data->get('file'));
	}

}