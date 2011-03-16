<?php
/**
* @package   ZOO Component
* @file      zoolist.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementZooList extends JElement {
	
	var	$_name = 'ZooList';

	function fetchElement($name, $value, &$node, $control_name)	{
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		$options = array ();
		foreach ($node->children() as $option) {
			$text	= $option->attributes('name');
			$val	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
			
			if ($value == $option->attributes('name')) {
				$value = $option->data();
			}			
		}

		return JHTML::_('select.genericlist', $options, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
	}
}
