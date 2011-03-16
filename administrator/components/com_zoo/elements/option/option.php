<?php
/**
* @package   ZOO Component
* @file      option.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementOption
		The option elements base class
*/
abstract class ElementOption extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/	
	public function hasValue($params = array()) {
		foreach ($this->_data->get('option', array()) as $option) {
            if (!empty($option)) {
                return true;
            }
        }
        return false;
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
		$options_from_config = $this->_config->get('options');
		$selected_options  = $this->_data->get('option', array());

		$options = array();
		foreach ($options_from_config as $option) {
			if (in_array($option['value'], $selected_options)) {
				$options[] = $option['name'];
			}
		}

		return ElementHelper::applySeparators($params['separated_by'], $options);

	}	
	
	/*
		Function: getSearchData
			Get elements search data.
					
		Returns:
			String - Search data
	*/
	public function getSearchData() {
		$options = $this->_data->get('option', array());
		$result = array();
		foreach ($this->_config->get('options', array()) as $option) {
			if (in_array($option['value'], $options)) {
				$result[] = $option['name'];
			}
		}
		return (empty($result) ? null : implode("\n", $result));

	}
	
	/*
	   Function: editOption
	      Renders elements options for form input.

	   Parameters:
	      $var - form var name
	      $num - option order number

	   Returns:
		  Array
	*/
	public function editOption($var, $num, $name = null, $value = null){

		// init vars
		$path = ZOO_ADMIN_PATH."/elements/option/tmpl/editoption.php";

		// render option
		$__html = '';
		ob_start();

		include($path);
		$__html = ob_get_contents();
		ob_end_clean();
		
		return $__html;
	}
	
	/*
	   Function: loadConfig
	       Converts the XML to a Dataarray and calls the bind method.

	   Parameters:
	      XML - The XML for this Element
	*/
	public function loadConfig($xml) {

		parent::loadConfig($xml);
				
		if ($xml->option) {
			$options = array();

			foreach ($xml->option as $option) {
				$options[] = array(
					'name'  => (string) $option->attributes()->name,
					'value' => (string) $option->attributes()->value);
			}

			$this->_config->set('options', $options);			
		}
	}
		
	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {
		
		$form = parent::getConfigForm();
		$form->addElementPath(dirname(__FILE__));

		return $form;
	}
			
	/*
	   Function: getConfigXML
   	      Get elements XML.

	   Returns:
	      Object - YXMLElement
	*/
	public function getConfigXML() {

		$xml = parent::getConfigXML(array('options'));
		
		foreach ($this->_config->get('options', array()) as $option) {
			if ($option['name'] != '' || $option['value'] != '') {
				$xml->addChild('option')
					->addAttribute('name', $option['name'])
					->addAttribute('value', $option['value']);
			}
		}
		
		return $xml;
	}

	/*
		Function: loadAssets
			Load elements css/js config assets.

		Returns:
			Void
	*/
	public function loadConfigAssets() {
		JHTML::script('option.js', 'administrator/components/com_zoo/elements/option/');
		JHTML::stylesheet('option.css', 'administrator/components/com_zoo/elements/option/');
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
        $options = array('required' => $params->get('required'));
		$messages = array('required' => 'Please choose an option.');
        $validator = new YValidatorForeach(new YValidatorString($options, $messages), $options, $messages);
        $option = $validator->clean($value->get('option'));

		return compact('option');
	}

}

class ElementOptionData extends ElementData{

	public function encodeData() {		
		$xml = YXMLElement::create($this->_element->getElementType())->addAttribute('identifier', $this->_element->identifier);
		foreach($this->_params->get('option', array()) as $option) {
			$xml->addChild('option', $option, null, true);
		}

		return $xml;			
	}

	public function decodeXML(YXMLElement $element_xml) {
		$data = array();
		if ($element_xml->option) {
			$options = array();
			foreach ($element_xml->option as $option) {
				$options[] = (string) $option;
			}
			$this->set('option', $options);
		}
		return $data;
	}	
	
}