<?php
/**
* @package   ZOO Component
* @file      element.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: JHTMLElement
   	  A class that contains element html functions
*/
class JHTMLElement {

 	/*
    	Function: editRow
    		Returns edit row html string.
 	*/
	public function editRow($name, $value) {

		$html  = "\t<tr>\n";
		$html .= "\t\t<td style=\"color:#666666;\">$name</td>\n";
		$html .= "\t\t<td>$value</td>\n";
		$html .= "\t</tr>\n";

		return $html;
	}
	
	public function countrySelectList($countries, $name, $selected, $multiselect) {

        $options = array ();
        if (!$multiselect) {
            $options[] = JHTML::_('select.option', '', '-' . JText::_('Select Country') . '-');
        }

        foreach ($countries as $key => $country) {
                $val   = $key;
                $text  = $country;
                $options[] = JHTML::_('select.option', $val, JText::_($text));
        }
        
        $attribs = $multiselect ? 'size="'.max(min(count($options), 10), 3).'" multiple="multiple"' : '';
        
        return JHTML::_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);	
	}

	public function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
	{
		JHTML::_('behavior.calendar'); //load the calendar behavior

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString( $attribs );
		}
	
		$html =  '<input style="width: 110px" type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'
				. '<img style="vertical-align:middle; margin:-2px 0 0 5px;" class="calendar" src="'.JURI::root(true).'/templates/system/images/calendar.png" alt="calendar" id="'.$id.'_img" />';

		$javascript = 'Calendar.setup({
				        inputField     :    "'.$id.'",     	// id of the input field
				        ifFormat       :    "'.$format.'",  // format of the input field
				        button         :    "'.$id.'_img",  // trigger for the calendar (button ID)
				        align          :    "Tl",           // alignment (defaults to "Bl")
				        singleClick    :    true,
				        showsTime	   :	true
	    })';
		
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";
		return $html.$javascript;
	}

}

/*
	Class: ElementHelper
		A class that contains element helper functions
*/
class ElementHelper {

	/*
	   Function: getAll
	      Returns a XML representation of the element array.

	   Parameters:
	      $paths - additional paths, to look for elements	

	   Returns:
	      XML representation of element array
	*/
	public static function getAll($paths = array()){

		settype($paths, 'array');		
		$paths[] = ZOO_ADMIN_PATH.'/elements';
		
		$elements = array();
		foreach ($paths as $path) {
			if (JFolder::exists($path)) {
				foreach (JFolder::folders($path, '.', false, true) as $folder) {
					$type = basename($folder);
					if (is_file($folder.DIRECTORY_SEPARATOR.$type.'.php')) {
						if ($element = self::loadElement($type, $paths)) {
							$metadata = $element->getMetaData();
							if ($metadata && $metadata['hidden'] != 'true') {
								$elements[] = $element;
							}
						}					
					}
				}
			}
		}
		
		return $elements;
	}

	/*
 		Function: saveElements
 	      Method to save a types elements.

	   Parameters:
	      $post - post data
	      $type - current type data with xml

	   Returns:
	      Boolean. True on success
	*/
	public static function saveElements($post, Type $type){

		// init vars
		$elements = array();
		
		// update old elements
		foreach ($type->getElements() as $identifier => $element) {
			if (isset($post['elements'][$identifier])) {
				$data = $post['elements'][$identifier];

				// bind data
				$element->bindConfig($data);

				// add to element array
				$elements[$data['ordering']] = $element;
			}
		}

		// add new elements
		if (isset($post['new_elements'])) {
			foreach ($post['new_elements'] as $data) {
				$element = ElementHelper::loadElement($data['type'], $type->getApplication()->getPath().'/elements');
				$element->setType($type);

				// set identifier (UUID)
				$data['identifier'] = YUtility::generateUUID();

				// bind data
				$element->bindConfig($data);

				// add to element array
				$elements[$data['ordering']] = $element;
			}
		}

		// sort elements
		ksort($elements);

		$type->setXML(self::toXML($elements));
		$type->clearElements();

		return true;
	}

	/*
		Function: createElementsFromXML
			Creates the elements described by the XML.

		Parameters:
			$xml_string - String
			$type - Type

		Returns:
			Array - Array of element objects
	*/
	public static function createElementsFromXML($xml_string, Type $type){
		
		$i       = 0;
		$results = array();
		$xml 	 = YXML::loadString($xml_string);

		$params = $xml->xpath('params/param');
		if ($params) {
			foreach ($params as $param)  {
				if ($element = self::_getElementFromXMLNode($param, $type)) {
					$results[$element->identifier] = $element;
					$results[$element->identifier]->setOrdering($i++);
				}
			}
		}
		return $results;
	}

	/*
		Function: _getElementFromXMLNode
			Creates an Element and binds data from XMLNode

		Parameters:
			$xml  - YXMLElement describes element
			$type - Type

		Returns:
			Object - element object
	*/
	protected static function _getElementFromXMLNode(YXMLElement $xml, Type $type) {

		// load element
		$element_type = (string) $xml->attributes()->type;
		$element 	  = self::loadElement($element_type, $type->getApplication()->getPath().'/elements');

		// bind element data or set undefined
		if ($element !== false) {
			$element->loadConfig($xml);
			return $element;
		}

		return null;
	}

	/*
		Function: loadElement
			Creates element of $type

		Parameters:
			$type - Type of the element subclass to create
	      	$paths - additional paths, to look for elements			

		Returns:
			Object - element object
	*/
	public static function loadElement($type, $paths = array()) {
		$false   = false;
		
		settype($paths, 'array');
		foreach ($paths as $key => $path) {
			$paths[$key] = $path.'/'.$type;
		}
		if (!in_array(ZOO_ADMIN_PATH.'/elements/'.$type, $paths)) {
			$paths[] = ZOO_ADMIN_PATH.'/elements/'.$type;
		}

		// register element class
		JLoader::register('Element', ZOO_ADMIN_PATH.'/elements/element/element.php');

		// load element class
		$elementClass = 'Element'.$type;
		if (!class_exists($elementClass)) {
			
			$file = JFilterInput::clean(str_replace('_', DS, strtolower(substr($type, 0, 1)) . substr($type, 1)).'.php', 'path');

			if ($elementFile = JPath::find($paths, $file)) {
				require_once $elementFile;
			} else {
				return $false;
			}
		}

		if (!class_exists($elementClass)) {
			return $false;
		}
		
		$testClass = new ReflectionClass($elementClass);
		
		if ($testClass->isAbstract()) {
			return false;
		}

		$element = new $elementClass();

		return $element;
	}

	/*
		Function: toXML
			Returns a XML representation of the element array.

		Parameters:
			$elements - ElementArray

		Returns:
			String - XML representation of element array
	*/
	public static function toXML($elements){

		$type   = YXMLElement::create('type')->addAttribute('version', '1.0.0');
		$params = YXMLElement::create('params');
		
		foreach ($elements as $element) {
			$params->appendChild($element->getConfigXML());
		}
						
		return $type->appendChild($params)->asXML(true, true);
	}

	/*
		Function: applySeparators
			Separates the passed element values with a separator

		Parameters:
			$separated_by - Separator
			$values - Element values

		Returns:
			String
	*/	
	public static function applySeparators($separated_by, $values) {

		if (!is_array($values)) {
			$values = array($values);
		}
		
		$separator = '';
		$tag = '';
		$enclosing_tag = '';
		if ($separated_by) {
			if (preg_match('/separator=\[(.*)\]/U', $separated_by, $result)) {
				$separator = $result[1];
			}
					
			if (preg_match('/tag=\[(.*)\]/U', $separated_by, $result)) {
				$tag = $result[1];
			}
			
			if (preg_match('/enclosing_tag=\[(.*)\]/U', $separated_by, $result)) {
				$enclosing_tag = $result[1];
			}			
		}
		
		if (empty($separator) && empty($tag) && empty($enclosing_tag)) {
			$separator = ', ';
		}
		
		if (!empty($tag)) {
			foreach ($values as $key => $value) {
				$values[$key] = sprintf($tag, $values[$key]);
			}
		}
		
		$value = implode($separator, $values);

		if (!empty($enclosing_tag)) {
			$value = sprintf($enclosing_tag, $value);
		}
		
		return $value;
	}	

}