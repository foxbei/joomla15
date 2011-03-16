<?php
/**
* @package   ZOO Component
* @file      options.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementOptions extends JElement {

	function fetchElement($name, $value, $node, $control_name) {

		// get element from parent parameter form
		$element = $this->_parent->element;
		$config  = $element->getConfig();
		
		// init vars
		$id         = str_replace(array('[', ']'), '_', $control_name).'options';		
		$i          = 0;

		// create options
		$options  = '<div id="'.$id.'" class="options">';
		$options .= '<ul>';
		foreach ($config->get('options', array()) as $opt) {
			$options .= '<li>'.$element->editOption($control_name, $i++, $opt['name'], $opt['value']).'</li>';
		}
		$options .= '<li class="hidden" >'.$element->editOption($control_name, '0', '', '').'</li>';		
		$options .= '</ul>';
		$options .= '<div class="add">'.JText::_('Add Option').'</div>';
		$options .= '</div>';
				
		// create js
		$javascript  = "jQuery('#$id').ElementSelect({variable: '$control_name' });";
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";

		return $options.$javascript;
	}

}