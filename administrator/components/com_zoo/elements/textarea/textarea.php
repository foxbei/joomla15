<?php
/**
* @package   ZOO Component
* @file      textarea.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementRepeatable', ZOO_ADMIN_PATH.'/elements/repeatable/repeatable.php');

/*
   Class: ElementTextarea
   The textarea element class
*/
class ElementTextarea extends ElementRepeatable implements iSubmittable {

	const ROWS = 20;
	const COLS = 60;
	const MAX_HIDDEN_EDITORS = 5;
	
	/*
		Function: bindData
			Set data through data array.

		Parameters:
			$data - array

		Returns:
			Void
	*/
	public function bindData($array = array()) {
		$this->_data_array = array();

		// set raw input for textarea
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		foreach ($array as $index => $instance_data) {
			if (isset($post['elements'][$this->identifier][$index]['value'])) {
				$array[$index]['value'] = $post['elements'][$this->identifier][$index]['value'];
			}
		}

		// set data
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
		Function: _getSearchData
			Get repeatable elements search data.
					
		Returns:
			String - Search data
	*/	
	protected function _getSearchData() {

		// clean html tags
		$filter	= new JFilterInput();
		$value  = $filter->clean($this->_data->get('value', ''));
		
		return (empty($value) ? null : $value);
	}
	
	/*
		Function: hasValue
			Override. Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {

		$display = isset($params['display']) ? $params['display'] : 'all';

		switch ($display) {
			case 'all':
				foreach ($this as $self) {
					if ($self->_hasValue()) {
						return true;
					}
				}
				break;
			case 'first':
				$this->rewind();
				return $this->_hasValue();
				break;
			case 'all_without_first':
				$this->rewind();
				while ($this->next()) {
					if ($this->_hasValue()) {
						return true;
					}	
				}
				break;
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

		$jplugins = $this->_config->get('jplugins');
		$display = isset($params['display']) ? $params['display'] : 'all';

		$result = array();
		switch ($display) {
			case 'all':
				foreach ($this as $self) {
					if (($text = $this->_data->get('value', '')) && !empty($text)) {
						$result[] = $text;
					}
				}
				break;
			case 'first':
				$this->rewind();
				if (($text = $this->_data->get('value', '')) && !empty($text)) {
					$result[] = $text;
				}
				break;
			case 'all_without_first':
				$this->rewind();
				while ($this->next()) {
					if (($text = $this->_data->get('value', '')) && !empty($text)) {
						$result[] = $text;
					}
				}				
				break;						
		}

		// trigger joomla content plugins
		if ($jplugins) {
			for ($i = 0; $i < count($result); $i++) {
				$result[$i] = ZooHelper::triggerContentPlugins($result[$i]);
			}
		}
		
		return ElementHelper::applySeparators($params['separated_by'], $result);	
	}	

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {

		parent::loadAssets();
		if ($this->_config->get('repeatable')) {
			JHTML::script('textarea.js', 'administrator/components/com_zoo/elements/textarea/');
		}
		return $this;
	}	
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->_renderRepeatable(array('trusted_mode' => true));
	}

    protected function _edit() {}
		
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
        return $this->_renderRepeatable($params);
	}

    protected function _renderRepeatable($params = array()) {
        
        $params = new YArray($params);
        $trusted_mode = $params->get('trusted_mode', false);

		if ($repeatable = $this->_config->get('repeatable')) {
            
			// create repeat-elements
			$html = array();
			$html[] = '<div id="'.$this->identifier.'" class="repeat-elements">';
			$html[] = '<ul class="repeatable-list">';

			foreach ($this as $self) {
				$html[] = '<li class="repeatable-element">';
				$html[] = $self->_addEditor($this->index(), $this->_data->get('value'), $trusted_mode);
				$html[] = '</li>';
			}

			for ($index = count($this->_data_array); $index < count($this->_data_array) + self::MAX_HIDDEN_EDITORS; $index++) {
				$html[] = '<li class="repeatable-element hidden">';
				$html[] = $this->_addEditor($index, '', $trusted_mode);
				$html[] = '</li>';
			}

			$html[] = '</ul>';
			$html[] = '<p class="add"><a href="javascript:void(0);">'.JText::sprintf('Add another %s', JString::ucfirst($this->getElementType())).'</a></p>';
			$html[] = '</div>';

			// create js
			$javascript  = "jQuery('#$this->identifier').ElementRepeatableTextarea({ msgDeleteElement : '".JText::_('Delete Element')."' });";
			$javascript  = "<script type=\"text/javascript\">\n//<!--\n$javascript\n// -->\n</script>\n";

			return implode("\n", $html).$javascript;

		} else {
            return $this->_addEditor(0, $this->_data->get('value'), $trusted_mode);
		}
    }

	protected function _addEditor($index, $value = '', $trusted_mode = true) {

		// init vars
		$default = $this->_config->get('default');

		// set default, if item is new
		if ($default != '' && $this->_item != null && $this->_item->id == 0 && $index == 0 && empty($value)) {
			$value = $default;
		}

		$html 	= array();
		$html[] = '<div class="repeatable-content">';
		if ($trusted_mode) {
            $html[] = JFactory::getEditor()->display('elements[' . $this->identifier . '][' . $index . '][value]', htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ), null, null, self::COLS, self::ROWS, array('pagebreak', 'readmore'));
        } else {
			$html[] = JHTML::_('control.textarea', 'elements[' . $this->identifier . '][' . $index . '][value]', $value, 'cols='.self::COLS.' rows='.self::ROWS);
		}		$html[] = '</div>';
		$html[] = '<span class="delete" title="'.JText::_('Delete Element').'"></span>';
		return implode("\n", $html);
	}

}