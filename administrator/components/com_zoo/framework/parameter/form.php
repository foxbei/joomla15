<?php
/**
* @package   ZOO Component
* @file      form.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YParameterForm
		Render parameter XML as HTML form.
*/
abstract class YParameterForm {

	/*
		Variable: _values
			Array of values.
    */
	protected $_values = array();

	/*
		Variable: _elements
			Elements.
    */
	protected $_elements = array();

	/*
		Variable: _element_path
			Directories, where element types can be stored.
    */
	protected $_element_path = array();

	/*
		Function: __construct
			Constructor
	*/	
	public function __construct() {
		
		// set default element paths
		$this->addElementPath(JPATH_LIBRARIES.'/joomla/html/parameter/element');
		$this->addElementPath(dirname(__FILE__).'/element');
	}

	/*
		Function: getValue
			Retrieve a form value

		Return:
			Mixed
	*/		
	public function getValue($name, $default = null) {
		
		if (array_key_exists($name, $this->_values)) {
			return $this->_values[$name];
		}
		
		return $default;
	}

	/*
		Function: setValue
			Set a form value

		Return:
			YParameterForm
	*/		
	public function setValue($name, $value) {
		$this->_values[$name] = $value;
		return $this;
	}

	/*
		Function: getValues
			Retrieve form values

		Return:
			Array
	*/		
	public function getValues() {
		return $this->_values;
	}

	/*
		Function: setValues
			Set form values

		Parameters:
			values - Parameter, Array, Object

		Return:
			YParameterForm
	*/		
	public function setValues($values) {
		
		if ($values instanceof YParameter) {
			$this->_values = $values->toArray();
		} else if (is_array($values)) {
			$this->_values = $values;
		} else if (is_object($values)) {
			$this->_values = get_object_vars($values);
		}
		
		return $this;
	}

	/*
		Function: loadElement
			Loads a element type

		Parameters:
			type - Element type
	*/
	public function loadElement($type, $new = false) {
		$signature = md5($type);

		if ((isset($this->_elements[$signature]) && !is_a($this->_elements[$signature], '__PHP_Incomplete_Class'))  && $new === false) {
			return $this->_elements[$signature];
		}

		$elementClass = 'JElement'.$type;

		if(!class_exists($elementClass)) {
			if (isset($this->_element_path)) {
				$dirs = $this->_element_path;
			} else {
				$dirs = array();
			}

			$file = JFilterInput::clean(str_replace('_', DS, $type).'.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file)) {
				include_once $elementFile;
			} else {
				return false;
			}
		}

		if (!class_exists($elementClass)) {
			return false;
		}

		$this->_elements[$signature] = new $elementClass($this);

		return $this->_elements[$signature];
	}

	/*
		Function: addElementPath
			Add a directory to search for element types

		Parameters:
			path - Element path (string or array)
	*/		
	public function addElementPath($path) {

		// just force path to array
		settype($path, 'array');

		// loop through the path directories
		foreach ((array) $path as $dir) {
			// no surrounding spaces allowed!
			$dir = trim($dir);

			// add trailing separators as needed
			if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
				// directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// add to the top of the search dirs
			array_unshift($this->_element_path, $dir);
		}
	}

	/*
		Function: render
			Render parameter html
	*/		
	abstract public function render();

}