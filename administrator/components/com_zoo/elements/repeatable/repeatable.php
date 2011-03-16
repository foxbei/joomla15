<?php
/**
* @package   ZOO Component
* @file      repeatable.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementRepeatable
       The repeatable element class
*/
abstract class ElementRepeatable extends Element implements Iterator {

	/*
       Variable: $_data_array
         Element data array.
    */
	protected $_data_array = array();
	
	/*
	   Function: Constructor
	*/
	public function __construct() {
		parent::__construct();

		// set callbacks
		$this->registerCallback('_edit');
		
		// initialize data
		$this->_data = $this->_data_array[0] = ElementData::newInstance($this);

	}		
	
	/*
		Function: setData
			Set data through xml string.

		Parameters:
			$xml - string

		Returns:
			Void
	*/	
	public function setData($xml) {
		$this->_data_array = array();

		if (!empty($xml)) {
			$xml = YXML::loadString($xml);
			foreach ($xml->children() as $xml_element) {
				if ((string) $xml_element->attributes()->identifier == $this->identifier) {
					$data = ElementData::newInstance($this);
					$data->decodeXML($xml_element);
					$this->_data_array[] = $data;
				}
			}
		}
		
		if (empty($this->_data_array)) {
			$this->_data_array[0] = ElementData::newInstance($this);
		}

		$this->_data = $this->_data_array[0];
	}

	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$array - Data array

		Returns:
			Void
	*/
	public function bindData($array = array()) {
		$this->_data_array = array();

		foreach ($array as $instance_data) {
			$data = ElementData::newInstance($this);
			foreach ($instance_data as $key => $value) {
				$data->set($key, $value);
			}
			$this->_data_array[] = $data;
		}
		
		if (empty($this->_data_array)) {
			$this->_data_array[0] = ElementData::newInstance($this);
		}

		$this->_data = $this->_data_array[0];	
	}

	/*
	   Function: toXML
	       Get elements XML representation.

	   Returns:
	       string - XML representation
	*/
	public function toXML() {
		$xml = '';
		foreach ($this->_data_array as $data) {
			$xml .= $data->encodeData()->asXml(true);
		}
		return $xml;
	}	
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->_renderRepeatable('_edit');
	}
	
	/*
	   Function: _edit
	       Renders the repeatable edit form field.
		   Must be overloaded by the child class.

	   Returns:
	       String - html
	*/	
	abstract protected function _edit();
	
	/*
		Function: getSearchData
			Get elements search data.
					
		Returns:
			String - Search data
	*/
	public function getSearchData() {
		$result = array();
		foreach($this as $self) {
			$result[] = $self->_getSearchData();
		}
		
		return (empty($result) ? null : implode("\n", $result));	
	}

	/*
		Function: _getSearchData
			Get repeatable elements search data.
					
		Returns:
			String - Search data
	*/	
	protected function _getSearchData() {
		return null;		
	}

	
	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		foreach($this as $self) {
			if ($self->_hasValue($params)) {
				return true;
			}
		}
		return false;
	}
	
	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/	
	protected function _hasValue($params = array()) {
		$value = $this->_data->get('value');
		return !empty($value);
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
		
		$result = array();
		foreach ($this as $self) {
			$result[] = $this->_render($params);
		}
		
		return ElementHelper::applySeparators($params['separated_by'], $result);
	}

	/*
		Function: render
			Renders the repeatable element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	protected function _render($params = array()) {
		
		// render layout
		if ($layout = $this->getLayout()) {
			return self::renderLayout($layout, array('value' => $this->_data->get('value')));
		}
		
		return $this->_data->get('value');		
	}	
	
	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		if ($this->_config->get('repeatable')) {
			JHTML::script('repeatable.js', 'administrator/components/com_zoo/elements/repeatable/');
		}
		return $this;
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
        $this->loadAssets();
        return $this->_renderRepeatable('_renderSubmission', $params);
	}

	protected function _renderSubmission($params = array()) {}

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
		$result = array();
		foreach($value as $single_value) {

            $single_value = $single_value ? new YArray($single_value) : new YArray();

			try {
				$result[] = $this->_validateSubmission($single_value, $params);
			} catch (YValidatorException $e) {
				if ($e->getCode() != YValidator::ERROR_CODE_REQUIRED) {
					throw $e;
				}
			}
		}
		if ($params->get('required') && !count($result)) {
			throw $e;
		}
		return $result;
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
		$validator = new YValidatorString(array('required' => $params->get('required')));
		$clean     = $validator->clean($value->get('value'));
		return array('value' => $clean);
	}

    protected function _renderRepeatable($function, $params = array()) {

		if ($this->_config->get('repeatable')) {

			// create repeat-elements
			$html = array();
			$html[] = '<div id="'.$this->identifier.'" class="repeat-elements">';
			$html[] = '<ul class="repeatable-list">';

			foreach($this as $self) {
				$html[] = '<li class="repeatable-element">';
				$html[] = $self->$function($params);
				$html[] = '</li>';
			}

			$this->rewind();
			$html[] = '<li class="repeatable-element hidden">';
			$html[] = preg_replace('/(elements\[\S+])\[(\d+)\]/', '$1[-1]', $this->$function($params));
			$html[] = '</li>';

			$html[] = '</ul>';
			$html[] = '<p class="add"><a href="javascript:void(0);">'.JText::sprintf('Add another %s', JString::ucfirst($this->getElementType())).'</a></p>';
			$html[] = '</div>';

			// create js

			$javascript  = "var rep = new Zoo.ElementRepeatable({ element : '".$this->identifier."', msgDeleteElement : '".JText::_('Delete Element')."', msgSortElement : '".JText::_('Sort Element')."' });";
			$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

			return implode("\n", $html).$javascript;

		} else {

			return $this->$function($params);
            
		}
    }

	public function isSortable() {
		return $this->_is_sortable;
	}	
	
	public function current() {
		return $this;
	}

	public function next() {
		$this->_data = next($this->_data_array);
		
		return $this->_data ? $this : false;
	}

	public function key() {
		return key($this->_data_array);
	}

	public function valid() {
		return $this->_data !== false;
	}

	public function rewind() {
		if (isset($this->_data_array[0])) {
			$this->_data = $this->_data_array[0];
		}
		reset($this->_data_array);
	}
	
	public function index() {
		return $this->key();
	}
		
}

// Declare the interface 'iRepeatSubmittable'
interface iRepeatSubmittable extends iSubmittable {

	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
    public function _renderSubmission($params = array());

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
    public function _validateSubmission($value, $params);
}