<?php
/**
* @package   ZOO Component
* @file      checkbox.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementOption', ZOO_ADMIN_PATH.'/elements/option/option.php');

/*
	Class: ElementCheckbox
		The checkbox element class
*/
class ElementCheckbox extends ElementOption implements iSubmittable {

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){

		// init vars
		$options_from_config 	= $this->_config->get('options', array());
		$default				= $this->_config->get('default');
		
		if (count($options_from_config)) {
		
			// set default, if item is new
			if ($default != '' && $this->_item != null && $this->_item->id == 0) {
				$this->_data->set('value', $default);
			}
			
			$selected_options  = $this->_data->get('option', array());
	
			$i       = 0;
			$html    = array();
			foreach ($options_from_config as $option) {
				$name = 'elements[' . $this->identifier . '][option][]';
				$checked = in_array($option['value'], $selected_options) ? ' checked="checked"' : null;
				$html[]  = '<input id="'.$name.$i.'" type="checkbox" name="elements[' . $this->identifier . '][option][]" value="'.$option['value'].'"'.$checked.'><label for="'.$name.$i++.'">'.$option['name'].'</label>';
			}
			// workaround: if nothing is selected, the element is still being transfered 
			$html[] = '<input type="hidden" name="elements[' . $this->identifier . '][check]" value="1">';
	
			return implode("\n", $html);
		}
		
		return JText::_("There are no options to choose from.");
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

}