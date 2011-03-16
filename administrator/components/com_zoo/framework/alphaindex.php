<?php
/**
* @package   ZOO Component
* @file      alphaindex.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YAlphaindex
		The YAlphaindex Class. Provides Alphaindex functionality.
*/
class YAlphaindex {

	protected $_index		= array();
	protected $_objects		= array();
	protected $_other		= '#';

	/*
    	Function: constructor
 		
		Parameters:
	      $path - Path to xml alphaindex definition.
	
	   Returns:
	      YAlphaindex
 	*/
	public function __construct($path) {
		if ($xml = YXML::loadFile($path)) {

			// add other character
			if ($xml->attributes()->other) {
				$this->_other = (string) $xml->attributes()->other;
			}

			// add characters
			foreach ($xml->children() as $option) {
				if (!in_array((string) $option, $this->_index)) {
					$key = $option->attributes()->value ? (string) $option->attributes()->value : (string) $option;
					$this->_index[$key] = (string) $option;
				}
			}
		}
	}

	/*
    	Function: getIndex
 			Retrieve character index.

		Parameter:
			$other - Include other character in index

	   Returns:
	      Array
 	*/
	public function getIndex($other = false) {

		$index = $this->_index;

		$key   = $other ? false : array_search($this->_other, $index);
		
		if ($key !== false) {
			unset($index[$key]);
		}

		return $index;
	}

	/*
    	Function: getOther
 			Retrieve character for items which are not indexed, usually #.

	   Returns:
	      String
 	*/
	public function getOther() {
		return $this->_other;
	}

	/*
    	Function: getChar
 			Retrieve alpha char from value.

		Parameter:
			$value - Index character value
	 *
	   Returns:
	      String
 	*/
	public function getChar($value) {
		return isset($this->_index[$value]) ? $this->_index[$value] : '';
	}

	/*
    	Function: getObjects
 			Retrieve objects which match a character in index.

		Parameter:
			$char - Index character
			$class_name - Retrieve only objects of a certain class

	   Returns:
	      Array
 	*/
	public function getObjects($char, $class_name = null) {

		$key = array_search($char, $this->_index);
		
		if ($key !== false && isset($this->_objects[$key])) {
			
			if ($class_name !== null) {
				return array_filter($this->_objects[$key], create_function('$object', 'return $object instanceof '.$class_name.';'));
			}
			
			return $this->_objects[$key];
		}
		
		return array();
	}

	/*
    	Function: addObjects
 			Add objects to index.

		Parameter:
			$objects - Object array
			$property - Object property to use in for indexing

	   Returns:
	      Void
 	*/
	public function addObjects($objects, $property) {

		$index = $this->getIndex();

		foreach ($objects as $object) {
			if (isset($object->$property)) {
				
				$char = JString::strtolower(JString::substr($object->$property, 0, 1));
				$key  = array_search($char, $index);

				if ($key !== false) {
					$this->_objects[$key][] = $object;
				} else {
					$this->_objects[array_search($this->getOther(), $this->_index)][] = $object;
				}
			}
		}

		return $this;
	}

	/*
    	Function: link
 			Get link with added get parameter.

	   Returns:
			String - Link url
 	*/
	public function link($url, $vars) {
		
		if (!is_array($vars)) {
			$vars = array($vars);
		}
		
		return $url.(strpos($url, '?') === false ? '?' : '&').implode('&', $vars);
	}

	/*
    	Function: render
 			Render the alphaindex.

	   Returns:
			String - Alphaindex html
 	*/
    public function render($url = 'index.php') {

		$html = '';

		// check if index is empty
		if (empty($this->_index)) {
			return $html;
		}

		// create html
		foreach ($this->_index as $key => $char) {
			if (isset($this->_objects[$key]) && count($this->_objects[$key])) {
				$html .= '<a href="'.JRoute::_($this->link($url, 'alpha_char='.$key)).'" title="'.$char.'">'.$char.'</a>';
			} else {
				$html .= '<span title="'.$char.'">'.$char.'</span>';
			}
		}

        return $html;
    }

}