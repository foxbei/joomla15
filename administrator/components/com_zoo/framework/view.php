<?php
/**
* @package   ZOO Component
* @file      view.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YView
		The View Class. Extends Joomla's JView functionality.
*/
class YView extends JView {

	/*
		Function: setLayout
			Override to retun this, to allow method chaining.

		Parameters:
			$layout - Layout name

		Returns:
			YView
	*/	
	public function setLayout($layout) {
		parent::setLayout($layout);
		return $this;
	}

	/*
		Function: addTemplatePath
			Override to return this, to allow method chaining.

		Parameters:
			$path - Path

		Returns:
			YView
	*/	
	public function addTemplatePath($path) {
		parent::addTemplatePath($path);
		return $this;
	}

	/*
		Function: partial
			Render a partial view template file

		Parameters:
			$name - Partial name
			$args - Array of arguments

		Returns:
			String - The output of the the partial
	*/	
	public function partial($name, $args = array()) {

		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', '_'.$name);
		
		// set template path and add global partials
		$path   = $this->_path['template'];
		$path[] = $this->_basePath.DS.'partials';
		
		// load the partial
		$__file    = $this->_createFileName('template', array('name' => $file));
		$__partial = JPath::find($path, $__file);

		// render the partial
		if ($__partial != false) {

			// import vars and get content
			extract($args);
			ob_start();
			include($__partial);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		return JError::raiseError(500, 'Partial Layout "'.$__file.'" not found. ('.YUtility::debugInfo(debug_backtrace()).')');
	}

}

/*
	Class: YViewException
*/
class YViewException extends YException {}