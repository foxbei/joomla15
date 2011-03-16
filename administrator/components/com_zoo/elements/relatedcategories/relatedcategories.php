<?php
/**
* @package   ZOO Component
* @file      relatedcategories.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementRelatedCategories
       The category element class
*/
class ElementRelatedCategories extends Element implements iSubmittable {
	
	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/	
	public function hasValue($params = array()) {
		$category_ids = $this->_data->get('category', array());
		$categories = YTable::getInstance('category')->getById($category_ids, true);
		return !empty($categories);
	}	
	
	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
	
		$category_ids = $this->_data->get('category', array());

		$category_links = array();
		$categories = YTable::getInstance('category')->getById($category_ids, true);
		foreach ($categories as $category) {
			$category_links[] = '<a href="'.RouteHelper::getCategoryRoute($category).'">'.$category->name.'</a>';
		}
		
		return ElementHelper::applySeparators($params['separated_by'], $category_links);
			
	}

	/*
	   Function: _edit
	       Renders the edit form field.
		   Must be overloaded by the child class.

	   Returns:
	       String - html
	*/	
	public function edit(){
		//init vars
		$multiselect = $this->_config->get('multiselect', array());

        $options = array();
        if (!$multiselect) {
            $options[] = JHTML::_('select.option', '', '-' . JText::_('Select Category') . '-');
        }

		$attribs = ($multiselect) ? 'size="5" multiple="multiple"' : '';

		return JHTML::_('zoo.categorylist', Zoo::getApplication(), $options, 'elements[' . $this->identifier . '][category][]', $attribs, 'value', 'text', $this->_data->get('category', array()));
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
        $options = array('required' => $params->get('required'));
		$messages = array('required' => 'Please choose a related category.');
        $validator = new YValidatorForeach(new YValidatorString($options, $messages), $options, $messages);
        $clean = $validator->clean($value->get('category'));

        $categories = array_keys($this->_item->getApplication()->getCategories());
        foreach ($clean as $category) {
            if (!empty($category) && !in_array($category, $categories)) {
                throw new YValidatorException('Please choose a correct category.');
            }
        }

		return array('category' => $clean);
	}

}

class ElementRelatedCategoriesData extends ElementData{
	
	public function encodeData() {		
		$xml = YXMLElement::create($this->_element->getElementType())->addAttribute('identifier', $this->_element->identifier);
		foreach($this->_params->get('category', array()) as $category) {
			$xml->addChild('category')->setData($category);
		}

		return $xml;			
	}

	public function decodeXML(YXMLElement $element_xml) {
		$data = array();
		if (isset($element_xml->category)) {
			$categories = array();
			foreach ($element_xml->category as $category) {
				$categories[] = (string) $category;
			}
			$this->set('category', $categories);
		}
		return $data;
	}	
	
}