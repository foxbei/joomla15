<?php
/**
* @package   ZOO Component
* @file      addthis.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementAddthis
       The Addthis element class (http://www.addthis.com)
*/
class ElementAddthis extends Element implements iSubmittable {
	
	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		
		// init vars
		$account = $this->_config->get('account');

		// render html
		if ($account && $this->_data->get('value')) {
			$html[] = "<a class=\"addthis_button\" href=\"http://www.addthis.com/bookmark.php?v=250&amp;username=$account\">";
			$html[] = "<img src=\"http://s7.addthis.com/static/btn/v2/lg-share-en.gif\" width=\"125\" height=\"16\" alt=\"Bookmark and Share\" style=\"border:0\"/>";
			$html[] = "</a>";
			$html[] = "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#username=$account\"></script>";
			return implode("\n", $html);
		}

		return '';
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$default = $this->_config->get('default');
		
		// set default, if item is new
		if ($default != '' && $this->_item != null && $this->_item->id == 0) {
			$this->_data->set('value', 1);
		}

		return JHTML::_('select.booleanlist', 'elements[' . $this->identifier . '][value]', '', $this->_data->get('value'));
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		return array('value' => $value->get('value'));
	}

}