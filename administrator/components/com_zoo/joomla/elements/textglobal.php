<?php
/**
* @package   ZOO Component
* @file      textglobal.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementTextGlobal extends JElement {

	var	$_name = 'TextGlobal';

	protected static $_count = 1;	
	
	function fetchElement($name, $value, &$node, $control_name) {

		// load script
		JHTML::script('textglobal.js', 'administrator/components/com_zoo/joomla/elements/');

		// init vars
		$id     = 'text-global'.self::$_count++;		
		$size   = ($node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '');
		$class  = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"');
        $value  = htmlspecialchars_decode($value, ENT_QUOTES);
		$global = $this->_parent->getValue($name) === null;

		// create html
		$html[] = '<div class="global text-global">';
		$html[] = '<input id="'.$id.'" type="checkbox" name="_global" value="'.$control_name.'['.$name.']'.'"'.($global ? ' checked="checked"' : '').' />';
		$html[] = '<label for="'.$id.'"> '.JText::_('Global').'</label>';
		$html[] = '<div class="input">';
		$html[] = '<input type="text" name="'.($global ? '' : $control_name.'['.$name.']').'" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}

}