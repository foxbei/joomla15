<?php
/**
* @package   ZOO Component
* @file      itemlink.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementItemLink
		The item link element class
*/
class ElementItemLink extends Element {

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
            if ($this->_item->getState()) {
                $url = RouteHelper::getItemRoute($this->_item);

                return '<a href="' . JRoute::_($url). '">' . JText::_('READ_MORE') . '</a>';

            } else {

                return JText::_('READ_MORE');
                
            }
		}
	}
	
}