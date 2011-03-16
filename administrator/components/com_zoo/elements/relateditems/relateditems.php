<?php
/**
* @package   ZOO Component
* @file      relateditems.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementRelatedItems
		The related items element class
*/
class ElementRelatedItems extends Element implements iSubmittable {

	protected $_related_items;

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/		
	public function hasValue($params = array()) {
		$items = $this->_getRelatedItems();
		return !empty($items);
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
		
		// init vars
		$params   = new YArray($params);
		$items    = array();
		$output   = array();
		$layout_path = $params->get('layout_path');
		$renderer = new ItemRenderer();
		$renderer->addPath($layout_path);
		$layout   = $params->get('layout');

		$items = $this->_getRelatedItems();

		// sort items
		$order = $params->get('order');
		if (in_array($order, array('alpha', 'ralpha'))) {
			usort($items, create_function('$a,$b', 'return strcmp($a->name, $b->name);'));
		} elseif (in_array($order, array('date', 'rdate'))) {
			usort($items, create_function('$a,$b', 'return (strtotime($a->created) == strtotime($b->created)) ? 0 : (strtotime($a->created) < strtotime($b->created)) ? -1 : 1;'));
		} elseif (in_array($order, array('hits', 'rhits'))) {
			usort($items, create_function('$a,$b', 'return ($a->hits == $b->hits) ? 0 : ($a->hits < $b->hits) ? -1 : 1;'));
		} elseif ($order == 'random') {
			shuffle($items);
		} else {
			
		}
		
		if (in_array($order, array('ralpha', 'rdate', 'rhits'))) {
			$items = array_reverse($items);
		}

		// create output
		foreach($items as $item) {
			$path   = 'item';
			$prefix = 'item.';
			$type   = $item->getType()->id;
			if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type)) {
				$path   .= DIRECTORY_SEPARATOR.$type;
				$prefix .= $type.'.';
			}

			if (in_array($layout, $renderer->getLayouts($path))) {
				$output[] = $renderer->render($prefix.$layout, array('item' => $item));
			} elseif ($params->get('link_to_item', false) && $item->getState()) {
				$url	  = RouteHelper::getItemRoute($item);
				$output[] = '<a href="'.JRoute::_($url).'" title="'.$item->name.'">'.$item->name.'</a>';
			} else {
				$output[] = $item->name;
			}
		}
		
		return ElementHelper::applySeparators($params->get('separated_by'), $output);
	}
	
	protected function _getRelatedItems() {

		if ($this->_related_items == null) {

			// init vars
			$table     = YTable::getInstance('item');

			// get user access id
			$access_id = JFactory::getUser()->get('aid', 0);

			// get items
			$items = $this->_data->get('item', array());

			if (!empty($items)) {
				// get dates
				$db   = YDatabase::getInstance();
				$date = JFactory::getDate();
				$now  = $db->Quote($date->toMySQL());
				$null = $db->Quote($db->getNullDate());
				$conditions = $table->getKeyName().' IN ('.implode(', ', $items).')'
							.' AND state = 1'
							.' AND access <= ' . $access_id
							.' AND (publish_up = '.$null.' OR publish_up <= '.$now.')'
							.' AND (publish_down = '.$null.' OR publish_down >= '.$now.')';
				$order = 'FIELD('.$table->getKeyName().','.implode(', ', $items).')';
				$this->_related_items = $table->all(compact('conditions', 'order'));

			}
		}
		
		return $this->_related_items;
	}
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// filter state and access
		$data = array();
		$table = YTable::getInstance('item');
		foreach ($this->_data->get('item', array()) as $id) {
			if ($id && ($item = $table->get($id)) && $item->isPublished() && $item->canAccess()) {
				$data[$id] = $item;
			}
		}		
		
		// build element id
        $id = 'a' . str_replace('-','_',$this->identifier);

		// filter types
		$type_filter = '';
		$selectable_types = $this->_config->get('selectable_types', array());
		foreach ($selectable_types as $selectable_type) {
			$type_filter .= '&type_filter[]=' . $selectable_type;
		}

		// filter items
		$item_filter = '';
		if ($this->_item) {
			$item_filter = '&item_filter='.$this->_item->id;
		}

		// init vars
		$link = 'index.php?option=com_zoo&controller=item&task=element&tmpl=component&func=selectRelateditem&object='.$id.$type_filter.$item_filter;

		if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'id' => $id,
                    'data' => $data,
					'link' => $link
                )
            );
        }

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

		// load assets
		JHTML::_('behavior.modal', 'a.modal');
		JHTML::script('relateditems.js', 'administrator/components/com_zoo/elements/relateditems/');

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

        $options     = array('required' => $params->get('required'));
		$messages    = array('required' => 'Please select at least one related item.');

        $validator = new YValidatorForeach(null, $options, $messages);
        $clean = $validator->clean($value->get('item'));

		$table = YTable::getInstance('item');
		$selectable_types = $this->_config->get('selectable_types', array());
        if (!empty($selectable_types)) {
			foreach ($clean as $item) {
				if (!empty($item) && !in_array($table->get($item)->type, $this->_config->get('selectable_types', array()))) {
					throw new YValidatorException('Please choose a correct related item.');
				}
			}
		}

		return array('item' => $clean);
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		JHTML::_('behavior.modal', 'a.modal');
		JHTML::script('relateditems.js', 'administrator/components/com_zoo/elements/relateditems/');
	}
	
	/*
	   Function: loadConfig
	       Converts the XML to a data array and calls the bind method.

	   Parameters:
	      XML - The XML for this Element
	*/
	public function loadConfig($xml) {

		parent::loadConfig($xml);
		
		if (isset($xml->selectable_type)) {
			$types = array();
			
			foreach ($xml->selectable_type as $selectable_type) {
				$types[] = (string) $selectable_type->attributes()->value;
			}
			
			$this->_config->set('selectable_types', $types);
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
	public function getConfigXML($ignore = array()) {

		$xml = parent::getConfigXML(array('selectable_types'));
		
		foreach ($this->_config->get('selectable_types', array()) as $selectable_type) {		
			if ($selectable_type['value'] != '') {
				$xml->addChild('selectable_type')->addAttribute('value', $selectable_type);	
			}
		}
		
		return $xml;
	}
	
}

class ElementRelatedItemsData extends ElementData{

	public function encodeData() {		
		$xml = YXMLElement::create($this->_element->getElementType())->addAttribute('identifier', $this->_element->identifier);
		foreach($this->_params->get('item', array()) as $item) {
			$xml->addChild('item', $item, null, true);
		}
		return $xml;			
	}
		
	public function decodeXML(YXMLElement $element_xml) {
		$data = array();
		if (isset($element_xml->item)) {
			$items = array();
			foreach ($element_xml->item as $related_item) {			
				$items[] = (string) $related_item;
			}
			$this->_params->set('item', $items);
		}
	}		
	
}