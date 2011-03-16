<?php
/**
* @package   ZOO Component
* @file      zooapplication.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementZooApplication extends JElement {

	var	$_name = 'ZooApplication';

	function fetchElement($name, $value, &$node, $control_name) {

		JHTML::_('behavior.modal', 'a.modal');
		JHTML::stylesheet('zooapplication.css', ZOO_ADMIN_URI.'/joomla/elements/');
		JHTML::script('zooapplication.js', ZOO_ADMIN_URI.'/joomla/elements/');
	
		// init vars
		$params = $this->_parent;
		$table  = YTable::getInstance('application');

		// set modes
		$modes = array();

		if ($node->attributes('categories')) {
			$modes[] = JHTML::_('select.option', 'categories', JText::_('Categories'));
		}

		if ($node->attributes('types')) {
			$modes[] = JHTML::_('select.option', 'types', JText::_('Types'));
		}

		if ($node->attributes('items')) {
			$modes[] = JHTML::_('select.option', 'item', JText::_('Item'));
		}
		
		// create application/category select
		$cats    = array();
		$types   = array();
		$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Application').' -'));

		foreach ($table->all(array('order' => 'name')) as $app) {

			// application option
			$options[] = JHTML::_('select.option', $app->id, $app->name);

			// create category select
			if ($node->attributes('categories')) {
				$attribs = 'class="category app-'.$app->id.($value != $app->id ? ' hidden' : null).'" role="'.$control_name.'[category]"';
				$opts    = $node->attributes('frontpage') ? array(JHTML::_('select.option', '', '&#8226;	'.JText::_('Frontpage'))) : array();
				$cats[]  = JHTML::_('zoo.categorylist', $app, $opts, ($value == $app->id ? $control_name.'[category]' : null), $attribs, 'value', 'text', $params->get('category'));
			}

			// create types select
			if ($node->attributes('types')) {
				$opts = array();

				foreach ($app->getTypes() as $type) {
					$opts[] = JHTML::_('select.option', $type->id, $type->name);				
				}

				$attribs = 'class="type app-'.$app->id.($value != $app->id ? ' hidden' : null).'" role="'.$control_name.'[type]"';
				$types[] = JHTML::_('select.genericlist', $opts, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'));
			}
		}

		// create html
		$html[] = '<div id="'.$name.'" class="zoo-application">';
		$html[] = JHTML::_('select.genericlist', $options, $control_name.'['.$name.']', 'class="application"', 'value', 'text', $value);

		// create mode select
		if (count($modes) > 1) {
			$html[] = JHTML::_('select.genericlist', $modes, $control_name.'[mode]', 'class="mode"', 'value', 'text', $params->get('mode'));
		}

		// create categories html
		if (!empty($cats)) {
			$html[] = '<div class="categories">'.implode("\n", $cats).'</div>';
		}

		// create types html
		if (!empty($types)) {
			$html[] = '<div class="types">'.implode("\n", $types).'</div>';
		}
				
		// create items html
		$link = '';
		if ($node->attributes('items')) {
			
			$document 	= JFactory::getDocument();
			$field_name	= $control_name.'[item_id]';
			$item_name  = JText::_('Select Item');
			
			if ($item_id = $params->get('item_id')) {
				$item = YTable::getInstance('item')->get($item_id);
				$item_name = $item->name;
			}
			
			$link = 'index.php?option=com_zoo&controller=item&task=element&tmpl=component&func=selectZooItem&object='.$name;
		
			$html[] = '<div class="item">';
			$html[] = '<input type="text" id="'.$name.'_name" value="'.htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />';
			$html[] = '<a class="modal" title="'.JText::_('Select Item').'"  href="#" rel="{handler: \'iframe\', size: {x: 850, y: 500}}">'.JText::_('Select').'</a>';
			$html[] = '<input type="hidden" id="'.$name.'_id" name="'.$field_name.'" value="'.(int)$item_id.'" />';
			$html[] = '</div>';
			
		}
		
		$html[] = '</div>';
				
		$javascript  = 'jQuery("#'.$name.'").ZooApplication({ url: "'.JRoute::_($link, false).'", msgSelectItem: "'.JText::_('Select Item').'" });';
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";
		
		return implode("\n", $html).$javascript;
	}

}