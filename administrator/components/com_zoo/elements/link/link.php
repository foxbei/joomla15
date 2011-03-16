<?php
/**
* @package   ZOO Component
* @file      link.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register yoo gallery class
JLoader::register('ElementRepeatable', ZOO_ADMIN_PATH.'/elements/repeatable/repeatable.php');

/*
   Class: ElementLink
       The link element class
*/
class ElementLink extends ElementRepeatable implements iRepeatSubmittable {

	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/	
	protected function _hasValue($params) {
		$link = $this->_data->get('value', '');
		return !empty($link);		
	}

	
	/*
		Function: getText
			Gets the link text.

		Returns:
			String - text
	*/
	public function getText() {
		$text = $this->_data->get('text', '');
		return empty($text) ? $this->_data->get('value', '') : $text;
	}
	
	/*
		Function: getTitle
			Gets the link title.

		Returns:
			String - title
	*/
	public function getTitle() {
		$title = $this->_data->get('custom_title', '');
		return empty($title) ? $this->getText() : $title;
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

		$target = ($this->_data->get('target', '')) ? 'target="_blank"' : '';
		$rel	= $this->_data->get('rel', '');
		$rel	= !empty($rel) ? 'rel="' . $rel .'"' : '';
					
		return '<a href="'.JRoute::_($this->_data->get('value', '')).'" title="'.$this->getTitle().'" '.$target.' '. $rel .'>'.$this->getText().'</a>';

	}

	/*
	   Function: _edit
	       Renders the repeatable edit form field.

	   Returns:
	       String - html
	*/		
	protected function _edit(){
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'index' => $this->index(),
                    'text' => $this->_data->get('text'),
                    'link' => $this->_data->get('value'),
                    'target' => $this->_data->get('target'),
                    'title' => $this->_data->get('custom_title'),
					'rel' => $this->_data->get('rel')
                )
            );
        }
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

        // get params
        $params       = new YArray($params);
        $trusted_mode = $params->get('trusted_mode');

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'index' => $this->index(),
                    'text' => $this->_data->get('text'),
                    'link' => $this->_data->get('value'),
                    'target' => $this->_data->get('target'),
                    'title' => $this->_data->get('custom_title'),
					'rel' => $this->_data->get('rel'),
					'trusted_mode' => $trusted_mode
                )
            );
        }
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
        $values       = $value;

        $validator    = new YValidatorString(array('required' => false));
        $text         = $validator->clean($values->get('text'));
        $target       = $validator->clean($values->get('target'));
        $custom_title = $validator->clean($values->get('custom_title'));
        $rel          = $validator->clean($values->get('rel'));
        
        $validator    = new YValidatorUrl(array('required' => $params->get('required')), array('required' => 'Please enter an URL.'));
        $value        = $validator->clean($values->get('value'));

		return compact('value', 'text', 'target', 'custom_title', 'rel');
    }

}