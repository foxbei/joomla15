<?php
/**
* @package   ZOO Component
* @file      element.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Element
		The Element abstract class
*/
abstract class Element {

    /*
       Variable: $identifier
         Element identifier.
    */
	public $identifier;
	
    /*
       Variable: $ordering
         Element ordering.
    */
	public $ordering = 0;
	
    /*
       Variable: $_type
         Elements related type object.
    */
	protected $_type;

    /*
       Variable: $_item
         Elements related item object.
    */
	protected $_item;

    /*
       Variable: $_callbacks
         Element callbacks.
    */
	protected $_callbacks = array();

    /*
       Variable: $_config
         Config parameter object.
    */
	protected $_config;
	
	/*
       Variable: $_metaxml
         Element meta xml.
    */
	protected $_metaxml;

	/*
       Variable: $_data
         Element data.
    */
	protected $_data;
	
	/*
       Variable: $_path
         Element file path.
    */
	protected $_path;	
		
	/*
	   Function: Constructor
	*/
	public function __construct() {
		$this->_config = new YParameter();
		
		// initialize data
		$this->_data = ElementData::newInstance($this);
	}	
	
	/*
		Function: getElementType
			Gets the elements type.

		Returns:
			string - the elements type
	*/
	public function getElementType() {
		return strtolower(str_replace('Element', '', get_class($this)));
	}
	
	/*
		Function: getElementData
			Gets the elements data.

		Returns:
			ElementData - the elements data
	*/	
	public function getElementData() {
		return $this->_data;
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
		$this->_data = ElementData::newInstance($this);

		if (!empty($xml) && ($xml = YXML::loadString($xml))) {
			foreach ($xml->children() as $xml_element) {
				if ((string) $xml_element->attributes()->identifier == $this->identifier) {
					$this->_data->decodeXML($xml_element);
					break;
				}
			}
		}

        return $this;
	}

	/*
    	Function: unsetData
    	  Unsets element data

	   Returns:
	      Void
 	*/
	public function unsetData() {
		if (isset($this->_data)) {
			$this->_data->unsetData();
		}
		return $this;
	}
	
	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/	
	public function bindData($data = array()) {
		$this->_data = ElementData::newInstance($this);
		foreach ($data as $key => $value) {
			$this->_data->set($key, $value);
		}
	}

	/*
	   Function: toXML
	       Get elements XML representation.

	   Returns:
	       string - XML representation
	*/
	public function toXML() {
		return $this->_data->encodeData()->asXML(true);
	}
	
	/*
		Function: getLayout
			Get element layout path and use override if exists.
		
		Returns:
			String - Layout path
	*/
	public function getLayout($layout = null) {

		// init vars
		$type = $this->getElementType();
		$path = ZOO_ADMIN_PATH."/elements/{$type}/tmpl";

		// set default
		if ($layout == null) {
			$layout = "{$type}.php";
		}

		// find layout
		if (JPath::find($path, $layout)) {
			return $path."/".$layout;
		}

		return null;
	}

	/*
		Function: getSearchData
			Get elements search data.
					
		Returns:
			String - Search data
	*/
	public function getSearchData() {
		return null;	
	}

	/*
		Function: getOrdering
			Get order number.
		
		Returns:
			Int - order number
	*/
	public function getOrdering() {
		return $this->ordering;
	}

	/*
		Function: getItem
			Get related item object.
		
		Returns:
			Item - item object
	*/
	public function getItem() {
		return $this->_item;
	}

	/*
		Function: getType
			Get related type object.

		Returns:
			Type - type object
	*/
	public function getType() {
		return $this->_type;
	}

	/*
		Function: getGroup
			Get element group.

		Returns:
			string - group
	*/
	public function getGroup() {
		$metadata = $this->getMetadata();
		return $metadata['group'];
	}	
	
	/*
		Function: setOrdering
			Set order number.

		Parameters:
			$ordering - order number

		Returns:
			Void
	*/
	public function setOrdering($ordering) {
		$this->ordering = $ordering;
	}
	
	/*
		Function: setItem
			Set related item object.

		Parameters:
			$item - item object

		Returns:
			Void
	*/
	public function setItem($item) {
		$this->_item = $item;
	}

	/*
		Function: setType
			Set related type object.

		Parameters:
			$type - type object

		Returns:
			Void
	*/
	public function setType($type) {
		$this->_type = $type;
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
		
		// render layout
		if ($layout = $this->getLayout()) {
			return self::renderLayout($layout, array('value' => $this->_data->get('value')));
		}
		
		return $this->_data->get('value');		
	}

	/*
		Function: renderLayout
			Renders the element using template layout file.

	   Parameters:
            $__layout - layouts template file
	        $__args - layouts template file args

		Returns:
			String - html
	*/
	protected function renderLayout($__layout, $__args = array()) {
				
		// init vars
		if (is_array($__args)) {
			foreach ($__args as $__var => $__value) {
				$$__var = $__value;
			}
		}
	
		// render layout
		$__html = '';
		ob_start();
		include($__layout);
		$__html = ob_get_contents();
		ob_end_clean();
		
		return $__html;
	}

	/*
	   Function: edit
	       Renders the edit form field.
		   Must be overloaded by the child class.

	   Returns:
	       String - html
	*/
	abstract public function edit();

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {}

	/*
		Function: registerCallback
			Register a callback function.

		Returns:
			Void
	*/
	public function registerCallback($method) {
		if (!in_array(strtolower($method), $this->_callbacks)) {
			$this->_callbacks[] = strtolower($method);
		}
	}

	/*
		Function: callback
			Execute elements callback function.

		Returns:
			Mixed
	*/
	public function callback($method, $args = array()) {

		// try to call a elements class method
		if (in_array(strtolower($method), $this->_callbacks) && method_exists($this, $method)) {
			// call method
			$res = call_user_func_array(array($this, $method), $args);
			// output if method returns a string
			if (is_string($res)) {
				echo $res;
			}
		}
	}

	/*
		Function: getConfig
			Retrieve element configuration.

		Returns:
			YParameter
	*/
	public function getConfig() {
		return $this->_config;
	}

	/*
		Function: bindConfig
			Binds the data array.

		Parameters:
			$data - data array
	        $ignore - An array or space separated list of fields not to bind

		Returns:
			Element
	*/
	public function bindConfig($data, $ignore = array()) {

		$this->_config = new YParameter();
		$this->_config->set('identifier', $this->identifier);
		
		// convert space separated list
		if (!is_array($ignore)) {
			$ignore = explode(' ', $ignore);
		}

		// bind data array
		if (is_array($data)) {
			foreach ($data as $name => $value) {
				if (!in_array($name, $ignore)) {
					$this->_config->set($name, $value);
				}
			}
		}

		return $this;
	}

	/*
   		Function: loadConfig
       		Load xml element configuration.

		Parameters:
			$xml - XML for this element

		Returns:
			Element
	*/	
	public function loadConfig(YXMLElement $xml) {

		// bind xml data
		foreach ($xml->attributes() as $name => $value) {
			$this->_config->set($name, (string) $value);
		}
		
		// set identifier
		$this->identifier = $this->_config->get('identifier');
		
		return $this;
	}

	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {

		$xml = $this->getPath().'/'.$this->getElementType().'.xml';

		// get parameter xml file
		if (JFile::exists($xml)) {

			// get form
			$form = new YParameterFormDefault($xml);
			$form->addElementPath(ZOO_ADMIN_PATH.'/joomla/elements');
			$form->setValues($this->_config);
			$form->element = $this; // add reference to element

			return $form;
		}
		
		return null;
	}
	
	/*
		Function: getConfigXML
			Get element configuration as xml formatted string.

		Returns:
			String
	*/
	public function getConfigXML($ignore = array()) {
		
		$xml = YXMLElement::create('param')
			->addAttribute('type', $this->getElementType())
			->addAttribute('identifier', $this->_config->get('identifier'))
			->addAttribute('name', $this->_config->get('name'));
		
		if ($xmlfile = $this->getMetaXML()) {
			$params = $xmlfile->xpath('params/param');
			if ($params) {
				foreach ($params as $param) {
					$name  = (string) $param->attributes()->name;
					$value = $this->_config->get($name);
					if (isset($value) && !in_array($name, $ignore)) {
						$xml->addAttribute($name, $value);
					}
				}
			}
		}
		
		return $xml;
	}
	
	/*
		Function: loadConfigAssets
			Load elements css/js config assets.

		Returns:
			Element
	*/
	public function loadConfigAssets() {
		return $this;
	}	
	
	/*
		Function: getMetaData
			Get elements xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData() {

		$data = array();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}

		$data['type'] 		  = $xml->attributes()->type ? (string) $xml->attributes()->type : 'Unknown';
		$data['group'] 		  = $xml->attributes()->group ? (string) $xml->attributes()->group : 'Unknown';
		$data['hidden'] 	  = $xml->attributes()->hidden ? (string) $xml->attributes()->hidden : 'false';
        $data['trusted'] 	  = $xml->attributes()->trusted ? (string) $xml->attributes()->trusted : 'false';
		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl'] 	  = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;

		return $data;
	}
	
	/*
		Function: getMetaXML
			Get elements xml meta file.

		Returns:
			Object - YXMLElement
	*/
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = YXML::loadFile($this->getPath().'/'.$this->getElementType().'.xml');
		}
		
		return $this->_metaxml;
	}	
	
	/*
		Function: getPath
			Get path to element's base directory.

		Returns:
			String - Path
	*/
	public function getPath() {
		if (empty($this->_path)) {
			$rc = new ReflectionClass(get_class($this));
			$this->_path = dirname($rc->getFileName());
		}
		return $this->_path;
	}

}

class ElementData {
	
	protected $_params;
	protected $_element;
	
	public function __construct($element) {
		$this->_element = $element;
		$this->_params = new YParameter();
	}
	
	public static function newInstance($element) {
		
		$current_class = get_class($element);
		do {
			$class_name = $current_class.'Data';
			if (class_exists($class_name)) {
				return new $class_name($element);
			}
		} while ($current_class = get_parent_class($current_class));

	}
	
	public function getParams() {
		return $this->_params;
	}

	public function set($name, $value) {
		$this->_params->set($name, $value);
	}

	public function get($name, $default = null) {
		return $this->_params->get($name, $default);
	}

	public function encodeData() {
		$xml = YXMLElement::create($this->_element->getElementType())->addAttribute('identifier', $this->_element->identifier);
		foreach ($this->_params->toArray() as $key => $value) {
			$xml->addChild($key, $value, null, true);
		}

		return $xml;
	}

	public function decodeXML(YXMLElement $element_xml) {
		foreach ($element_xml->children() as $child) {
			$this->set($child->getName(), (string) $child);
		}
		return $this;
	}	

	public function unsetData() {
		foreach($this->_params as $key => $data) {
			unset($this->_params[$key]);
		}
		$this->_element = null;
	}
	
}

// Declare the interface 'iSubmittable'
interface iSubmittable {

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
    public function renderSubmission($params = array());

    /*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
    public function validateSubmission($value, $params);
}

interface iSubmissionUpload {

    /*
		Function: doUpload
			Does the actual upload during submission

		Returns:
			void
	*/
    public function doUpload();
}

/*
	Class: ElementException
*/
class ElementException extends YException {}