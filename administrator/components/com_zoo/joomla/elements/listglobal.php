<?php
/**
* @package   ZOO Component
* @file      listglobal.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementListGlobal extends JElement {

	public $_name = 'ListGlobal';

	protected static $_count = 1;

	function fetchElement($name, $value, &$node, $control_name) {

		// load script
		JHTML::script('listglobal.js', 'administrator/components/com_zoo/joomla/elements/');

		// init vars
		$id      = 'list-global-'.self::$_count++;
		$class   = $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"';
		$global  = $this->_parent->getValue($name) === null;
		$options = array();

		foreach ($node->children() as $option) {
			$val	   = $option->attributes('value');
			$text	   = $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		// create html
		$html[] = '<div class="global list-global">';
		$html[] = '<input id="'.$id.'" type="checkbox" name="_global" value="'.$control_name.'['.$name.']'.'"'.($global ? ' checked="checked"' : '').' />';
		$html[] = '<label for="'.$id.'"> '.JText::_('Global').'</label>';
		$html[] = '<div class="input">';
		$html[] = JHTML::_('select.genericlist',  $options, ($global ? '' : $control_name.'['.$name.']'), $class, 'value', 'text', $value, $control_name.$name);
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}

}