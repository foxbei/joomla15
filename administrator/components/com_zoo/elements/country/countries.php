<?php
/**
* @package   ZOO Component
* @file      countries.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementCountries extends JElement {

	function fetchElement($name, $value, $node, $control_name) {

		// get element from parent parameter form
		$element = $this->_parent->element;
		$config  = $element->getConfig();
		
		return JHTML::_('element.countryselectlist', ElementCountry::getCountryArray(), $control_name.'[selectable_countries][]', $config->get('selectable_countries', array()), true);
	}
	
}