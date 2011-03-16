<?php
/**
* @package   ZOO Component
* @file      zoolayout.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementZooLayout extends JElement {
	
	public	$_name = 'ZooLayout';
	protected	$_renderer;

	function fetchElement($name, $value, &$node, $control_name) {

		// init vars
		$class      = $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"';
		$constraint = $node->attributes('constraint');
		
		// get renderer
		$this->renderer = new ItemRenderer();
		$this->renderer->addPath($this->_parent->layout_path);
		
		// if selectable types isn't specified, get all types
		if (empty($this->_parent->selectable_types)) {
			$this->_parent->selectable_types = array('');
			foreach (JFolder::folders($this->_parent->layout_path.'/'.$this->renderer->getFolder().'/item') as $folder) {
				$this->_parent->selectable_types[] = $folder;	
			}
		}

		// get layouts
		$layouts = array();		
		foreach ($this->_parent->selectable_types as $type) {
			$layouts = array_merge($layouts, $this->_getLayouts($type, $constraint));
		}		
		
		// create layout options
		$options = array(JHTML::_('select.option', '', JText::_('Item Name')));
		foreach ($layouts as $layout => $layout_name) {
			$text	   = $layout_name;
			$val	   = $layout;
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		return JHTML::_('select.genericlist', $options, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
	}
	
	protected function _getLayouts($type = null, $constraint = null) {
		$path   = 'item';
		$prefix = 'item.';
		if (!empty($type) && $this->renderer->pathExists($path.DIRECTORY_SEPARATOR.$type)) {
			$path   .= DIRECTORY_SEPARATOR.$type;
			$prefix .= $type.'.';
		}
		
		$layouts = array();
		foreach ($this->renderer->getLayouts($path) as $layout) {

			$metadata = $this->renderer->getLayoutMetaData($prefix.$layout);

			if (empty($constraint) || $metadata->get('type') == $constraint) {
				$layouts[$layout] = $metadata->get('name');
			}
		}
		return $layouts;
	}
	
}