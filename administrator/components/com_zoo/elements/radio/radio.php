<?php
/**
* @package   ZOO Component
* @file      radio.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementOption', ZOO_ADMIN_PATH.'/elements/option/option.php');

/*
	Class: ElementRadio
		The radio element class
*/
class ElementRadio extends ElementOption implements iSubmittable {
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){
		
		// init vars
		$options_from_config = $this->_config->get('options', array());
		$default 			 = $this->_config->get('default');
		
		if (count($options_from_config)) {
						
			// set default, if item is new
			if ($default != '' && $this->_item != null && $this->_item->id == 0) {
				$this->_data->set('option', array($default));
			}
			
			$options = array();
			foreach ($options_from_config as $option) {
				$options[] = JHTML::_('select.option', $option['value'], $option['name']);
			}
	
			$option = $this->_data->get('option', array());

			return JHTML::_('select.radiolist', $options, 'elements[' . $this->identifier . '][option][]', null, 'value', 'text', (isset($option[0]) ? $option[0] : null));
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