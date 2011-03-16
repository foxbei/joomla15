<?php
/**
* @package   ZOO Component
* @file      date.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementRepeatable', ZOO_ADMIN_PATH.'/elements/repeatable/repeatable.php');

/*
   Class: ElementDate
   The date element class
*/
class ElementDate extends ElementRepeatable implements iRepeatSubmittable {

	const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';

	/*
		Function: render
			Renders the repeatable element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	protected function _render($params = array()) {	
		return JHTML::_('date', $this->_data->get('value', ''), (($params['date_format'] == 'custom') ? $params['custom_format'] : $params['date_format']));
	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/		
	protected function _edit(){
		$value = $this->_data->get('value', '');
		if (!empty($value)) {
			$tzoffset = JFactory::getConfig()->getValue('config.offset');
			$date     = JFactory::getDate($value, -$tzoffset);
			$value	  = $date->toMySQL();
		}
		$html = JHTML::_('element.calendar', $value, 'elements[' . $this->identifier . ']['.$this->index().'][value]', 'elements[' . $this->identifier . ']['.$this->index().']value', self::EDIT_DATE_FORMAT, array('class' => 'calendar-element'));

		JHTML::script('date.js', 'administrator/components/com_zoo/elements/date/');

		return $html;
	}
	
	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function _renderSubmission($params = array()) {
		return JHTML::_('element.calendar', $this->_data->get('value', ''), 'elements[' . $this->identifier . ']['.$this->index().'][value]', 'elements[' . $this->identifier . ']['.$this->index().']value', self::EDIT_DATE_FORMAT, array('class' => 'calendar-element'));
	}

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function _validateSubmission($value, $params) {

        $validator = new YValidatorDate(array('required' => $params->get('required')), array('required' => 'Please choose a date.'));
		$validator->addOption('date_format', self::EDIT_DATE_FORMAT);
        $clean = $validator->clean($value->get('value'));

		return array('value' => $clean);
	}

}

class ElementDateData extends ElementData{

	public function encodeData() {
		$xml = YXMLElement::create($this->_element->getElementType())->addAttribute('identifier', $this->_element->identifier);
		$value = $this->_params->get('value', '');
		if (!empty($value)) {
			$tzoffset = JFactory::getConfig()->getValue('config.offset');
			$date     = JFactory::getDate($value, $tzoffset);
			$value	  = $date->toMySQL();
		}
		
		$xml->addChild('value', $value, null, true);
		
		return $xml;
	}

}