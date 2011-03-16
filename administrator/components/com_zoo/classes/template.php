<?php
/**
* @package   ZOO Component
* @file      template.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Template
		Template related attributes and functions.
*/
class Template {

    /*
		Variable: name
			Template unique name.
    */
	public $name;

    /*
		Variable: path
			Template path.
    */
	public $path;

    /*
		Variable: metaxml_file
			Template meta xml filename.
    */
	public $metaxml_file;

    /*
		Variable: _metaxml
			Template meta data YXMLElement.
    */
	public $_metaxml;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      $name - Template name
 	*/
	public function __construct($name, $path) {

		// set vars
		$this->name = $name;
		$this->path = $path;
		$this->metaxml_file = "template.xml";
	}

	/*
		Function: getPath
			Get template path.

		Returns:
			String - Template path
	*/
	public function getPath() {
		return $this->path;
	}

	/*
		Function: getURI
			Get template uri for CSS/JS loading.

		Returns:
			String - Template uri
	*/
	public function getURI() {
		return trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $this->path)), '/');
	}

	/*
		Function: getParamsForm
			Gets template parameter form.

		Returns:
			YParameterForm
	*/
	public function getParamsForm($global = false) {

		$xmlpath = $this->path.'/'.$this->metaxml_file;

		// get parameter xml file
		if (JFile::exists($xmlpath)) {
			
			// set xml file
			$xml = $xmlpath;
			
			// parse xml and add global
			if ($global) {
				$xml = YXML::loadFile($xmlpath);
				foreach ($xml->params as $param) {
					foreach ($param->children() as $element) {
						$type = (string) $element->attributes()->type;
						
						if (in_array($type, array('list', 'radio', 'text'))) {
							$element->attributes()->type = $type.'global';
						}
					}
				}				

				$xml = $xml->asXML(true);
			}

			// get form
			$form = new YParameterFormDefault($xml);
			$form->addElementPath(ZOO_ADMIN_PATH.'/joomla/elements');

			return $form;
		}
		
		return null;
	}
	
	/*
		Function: getMetaData
			Get template xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData() {

		$data = array();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}
		
		if ($xml->getName() != 'template') {
			return false;
		}
		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		return $data;
	}
	
	/*
		Function: getMetaXML
			Get template xml meta file.

		Returns:
			Object - YXMLElement
	*/
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = YXML::loadFile($this->getMetaXMLFile());
		}

		return $this->_metaxml;
	}

	/*
		Function: getMetaXMLFile
			Get template path to xml meta file.

		Returns:
			String
	*/
	public function getMetaXMLFile() {
		return $this->getPath() . '/' . $this->metaxml_file;
	}

}

/*
	Class: TemplateException
*/
class TemplateException extends YException {}