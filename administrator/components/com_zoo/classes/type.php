<?php
/**
* @package   ZOO Component
* @file      type.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Type
		Type related attributes and functions.
*/
class Type {

    /*
       Variable: id
         Type id.
    */
	public $id;

    /*
       Variable: identifier
         Type unique identifier.
    */
	public $identifier;

    /*
       Variable: name
         Type name.
    */
	public $name;

    /*
       Variable: _application
         Related application.
    */
	protected $_application;

    /*
       Variable: _basepath
         Type base path.
    */
	protected $_basepath;

    /*
       Variable: _elements
         Element objects.
    */
	protected $_elements;

    /*
       Variable: _core_elements
         Core element objects.
    */
	protected $_core_elements;

    /*
       Variable: _xml
         Type elements xml.
    */
	protected $_xml;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      id - Type id
	      basepath - Type base path

		Returns:
		  Type
 	*/
	public function __construct($id, $basepath) {
		
		// init vars
		$this->id = $id;
		$this->identifier = $id;
		$this->_basepath = $basepath;

		if ($data = $this->getXML()) {
			$this->name = (string) YXML::loadString($data)->attributes()->name;		
		}
	}

	/*
		Function: getApplication
			Retrieve related application object.

		Returns:
			Application
	*/
	public function getApplication() {
		return $this->_application;
	}

	/*
		Function: setApplication
			Set related application object.

		Returns:
			Type
	*/
	public function setApplication($application) {
		$this->_application = $application;
		return $this;
	}

	/*
    	Function: getElement
    	  Get element object by name.

	   	  Returns:
	        Object
 	*/
	public function getElement($identifier, $item = null) {

		$elements = empty($item) ? $this->getElements() : $item->getElements();
		
		if (isset($elements[$identifier])) {
			return $elements[$identifier];
		}
		
		return null;
	}

	/*
    	Function: getElements
    	  Get all element objects from the parsed type elements xml.

	   	  Returns:
	        Array - Array of element objects
 	*/
	public function getElements($item = null) {
		$elements = array();
				
		// init elements from type xml
		if (empty($this->_elements)) {
			$this->_elements = ElementHelper::createElementsFromXML($this->getXML(), $this);
		}

		// set type and item object
		foreach ($this->_elements as $identifier => $element) {
			$elements[$identifier] = clone($this->_elements[$identifier]);
			$elements[$identifier]->setType($this);
			$elements[$identifier]->setItem($item);
		}

		return $elements;
	}
	
	/*
    	Function: getSubmittableElements
    	  Get all submittable element objects from the parsed type elements xml.

	   	  Returns:
	        Array - Array of submittable element objects
 	*/
	public function getSubmittableElements($item = null) {
		return	array_filter($this->getElements($item), create_function('$element', 'return $element instanceof iSubmittable;'));
	}

	/*
    	Function: getCoreElements
    	  Get all core element objects.

	   	  Returns:
	        Array - Array of element objects
 	*/
	public function getCoreElements($item = null) {

		$elements = array();

		// init elements from type xml
		if (empty($this->_core_elements)) {
			$corexml  = JFile::read(ZOO_ADMIN_PATH.'/elements/core.xml');
			$this->_core_elements = ElementHelper::createElementsFromXML($corexml, $this);
		}

		// set type and item object
		foreach ($this->_core_elements as $identifier => $element) {
			$elements[$identifier] = clone($this->_core_elements[$identifier]);
			$elements[$identifier]->setType($this);
			if ($item != null) {
				$elements[$identifier]->setItem($item);
			}
		}

		return $elements;

	}

	/*
    	Function: clearElements
    	  Clear loaded elements object.

	   	  Returns:
	        Type
 	*/
	public function clearElements() {
		
		$this->_elements = null;

		return $this;
	}

	/*
		Function: getXMLFile
			Retrieve xml config file.

		Returns:
			String
	*/
	public function getXMLFile($id = null) {

		$id = ($id !== null) ? $id : $this->id;

		if ($this->_basepath && $id) {
			return $this->_basepath.'/'.$id.'.xml';
		}

		return null;
	}

	/*
		Function: getXML
			Retrieve xml and read config file content.

		Returns:
			String
	*/
	public function getXML() {
		
		$file = $this->getXMLFile();

		if (empty($this->_xml) && file_exists($file)) {
			$this->_xml = JFile::read($file);
		}

		return $this->_xml;
	}

	/*
		Function: setXML
			Set xml and write config file content.

		Returns:
			Type
	*/
	public function setXML($xml) {
		$this->_xml = $xml;
		return $this;
	}

	/*
		Function: bind
			Bind data array to type.

		Returns:
			Type
	*/
	public function bind($data) {
		
		if (isset($data['identifier'])) {

			// check identifier
			if ($data['identifier'] == '' || $data['identifier'] != YString::sluggify($data['identifier'])) {
				throw new TypeException('Invalid identifier');
			}

			$this->identifier = $data['identifier'];
		}
		
		if (isset($data['name'])) {

			// check name
			if ($data['name'] == '') {
				throw new TypeException('Invalid name');
			}

			$this->name = $data['name'];
		}

		return $this;
	}

	/*
		Function: save
			Save type data.

		Returns:
			Type
	*/
	public function save() {

		$old_identifier = $this->id;
		$rename = false;
		
		if (empty($this->id)) {

			// check identifier
			if (file_exists($this->getXMLFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

			// set xml
			$this->setXML(YXMLElement::create('type')
				->addAttribute('version', '1.0.0')
				->asXML(true, true)
			);

		} else if ($old_identifier != $this->identifier) {
			
			// check identifier
			if (file_exists($this->getXMLFile($this->identifier))) {
				throw new TypeException('Identifier already exists');
			}

			// rename xml file
			if (!JFile::move($this->getXMLFile(), $this->getXMLFile($this->identifier))) {
				throw new TypeException('Renaming xml file failed');
			}			

			$rename = true;
				
		}

		// update id
		$this->id = $this->identifier;			
		
		// set data
		$this->setXML(YXML::loadString($this->getXML())
			->addAttribute('name', $this->name)
			->asXML(true, true)
		);

		// save xml file
		if ($file = $this->getXMLFile()) {
			if (!JFile::write($file, $this->getXML())) {
				throw new TypeException('Writing type xml file failed');
			}
		}
		
		// rename related items
		if ($rename) {
			$table = YTable::getInstance('item');
			
			// get database
			$db = $table->getDBO();
			
			// update childrens parent category
			$query = "UPDATE ".$table->getTableName()
			    	." SET type=".$db->quote($this->identifier)
				    ." WHERE type=".$db->quote($old_identifier);
			$db->query($query);
		}
						
		return $this;
	}
	
	/* 
		Function: delete
			Delete type data.

		Returns:
			Type
	*/
	public function delete() {

		// check if type has items
		if (YTable::getInstance('item')->getTypeItemCount($this)) {
			throw new TypeException('Cannot delete type, please delete the items related first');
		}

		// delete xml file
		if (!JFile::delete($this->getXMLFile())) {
			throw new TypeException('Deleting xml file failed');
		}
				
		return $this;
	}

}

/*
	Class: TypeException
*/
class TypeException extends YException {}