<?php
/**
* @package   ZOO Component
* @file      itemprint.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementItemPrint
		The item print element class
*/
class ElementItemPrint extends Element {

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

		$params = new YArray($params);

		// include assets css
		JHTML::stylesheet('itemprint.css', 'administrator/components/com_zoo/elements/itemprint/assets/css/');

		if (YRequest::getBool('print', 0)) {

			return '<a class="element-print-button" onclick="window.print();return false;" href="#"></a>';

		} else {

			JHTML::_('behavior.modal', 'a.modal');
			$text  = $params->get('showicon') ? '' : JText::_('Print');
			$class = $params->get('showicon') ? 'modal element-print-button' : 'modal';
			return '<a href="'.JRoute::_(RouteHelper::getItemRoute($this->_item).'&amp;tmpl=component&amp;print=1').'" title="'.JText::_('Print').'" rel="{handler: \'iframe\', size: {x: 850, y: 500}}" class="'.$class.'">'.$text.'</a>';
			
		}
	}

}