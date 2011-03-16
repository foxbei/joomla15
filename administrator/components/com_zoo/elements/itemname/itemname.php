<?php
/**
* @package   ZOO Component
* @file      itemname.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementItemName
		The item name element class
*/
class ElementItemName extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return true;
	}
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return null;
	}
		
	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		if (!empty($this->_item)) {
			$params = new YArray($params);
			if ($params->get('link_to_item', false) && $this->_item->getState()) {
				$url = RouteHelper::getItemRoute($this->_item);
				
				return '<a title="'.$this->_item->name.'" href="' . JRoute::_($url). '">' . $this->_item->name . '</a>';
			} else {
				return $this->_item->name;
			}
		}		
	}
	
}