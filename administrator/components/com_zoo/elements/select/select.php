<?php
/**
* @package   ZOO Component
* @file      select.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementOption', ZOO_ADMIN_PATH.'/elements/option/option.php');

/*
	Class: ElementSelect
		The select element class
*/
class ElementSelect extends ElementOption implements iSubmittable {

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){
		
		// init vars
		$options_from_config = $this->_config->get('options', array());
		$multiple 			 = $this->_config->get('multiple');
		$default			 = $this->_config->get('default');
        $name   			 = $this->_config->get('name');

		if (count($options_from_config)) {
			
			// set default, if item is new
			if ($default != '' && $this->_item != null && $this->_item->id == 0) {
				$this->_data->set('option', $default);
			}
			
			$options = array();
            if (!$multiple) {
                $options[] = JHTML::_('select.option', '', '-' . JText::sprintf('Select %s', $name) . '-');
            }
            foreach ($options_from_config as $option) {
				$options[] = JHTML::_('select.option', $option['value'], $option['name']);
			}

			$style = $multiple ? 'multiple="multiple" size="5"' : '';
			
			$html[] = JHTML::_('select.genericlist', $options, 'elements['.$this->identifier.'][option][]', $style, 'value', 'text', $this->_data->get('option', array()));
			
			// workaround: if nothing is selected, the element is still being transfered 
			$html[] = '<input type="hidden" name="elements[' . $this->identifier . '][select]" value="1">';
			
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