<?php
/**
* @package   ZOO Component
* @file      flickr.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementFlickr
       The Flickr element class (http://www.flickr.com)
*/
class ElementFlickr extends Element implements iSubmittable {

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render() {

		// init vars
		$height   = $this->_config->get('height');
		$width 	  = $this->_config->get('width');		
		$tags     = $this->_data->get('value');
		$flickrid = $this->_data->get('flickrid', '');		

		// render html
		if ($width && $height && ($tags || $flickrid)) {

			$vars = array();
			
			if ($flickrid) {
				$vars[] = 'user_id='.$flickrid;
			}
			
			if ($tags) {
				$vars[] = 'tags='.$tags;
			}
		
			$html  = '<iframe src="http://www.flickr.com/slideShow/index.gne?'.implode('&amp;', $vars). '"';
			$html .= ' align="middle" frameborder="0" height="'. $height .'"';
			$html .= ' scrolling="no" width="'. $width .'"';
			$html .= '></iframe>'	;
			
			return $html;
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'tags' => $this->_data->get('value'),
                    'flickrid' => $this->_data->get('flickrid')
                )
            );
        }

        return null;
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

        $values = $value;

		$validator = new YValidatorString(array('required' => false));

		try {
			$value     = $validator->clean($values->get('value'));
		} catch (YValidatorException $e) {
			$value = $validator->getEmptyValue();
		}
		try {
			$flickrid  = $validator->clean($values->get('flickrid'));
		} catch (YValidatorException $e) {
			$flickrid = $validator->getEmptyValue();
		}

		if ($params->get('required') && empty($value) && empty($flickrid)) {
			throw new YValidatorException('Please provide Tags or a valid Flickr id.');
		}

		return compact('value', 'flickrid');
	}

}