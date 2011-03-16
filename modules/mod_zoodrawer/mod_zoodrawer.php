<?php
/**
* @package   ZOO Drawer
* @file      mod_zoodrawer.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// count instances
if (!isset($GLOBALS['yoo_drawers'])) {
	$GLOBALS['yoo_drawers'] = 1;
} else {
	$GLOBALS['yoo_drawers']++;
}

$items = ZooModuleHelper::getItems($params);

// load template
if ($item_count = count($items)) {

	// init vars
	$theme			 = $params->get('theme', 'default-v');
	$layout			 = $params->get('layout', 'default');
	$module_height   = $params->get('module_height', '150');
	$item_size       = $params->get('item_size', '220');
	$item_minimized  = $params->get('item_minimized', '90');
	$title           = $params->get('title', 'Title');
	$module_base     = JURI::base() . 'modules/mod_zoodrawer/';
	
	// css parameters
	$drawer_id       = 'yoo-drawer-' . $GLOBALS['yoo_drawers'];
	
	switch ($theme) {
		// horizontal
		case "photo-h":
			$jslayout              = 'horizontal';
			$item_width          = $item_size;
			$item_height         = $module_height;
			$module_width        = $item_size + ($item_count-1) * $item_minimized;
			$css_item_width      = 'width: ' . $item_width . 'px;';
			$css_item_height     = 'height: ' . $item_height . 'px;';
			$css_module_width    = 'width: ' . $module_width . 'px;';
			$css_module_height   = 'height: ' . $module_height . 'px;';
			// js parameters
			$item_shift          = $item_size - $item_minimized + 10;
			break;
	
		// vertical
		case "photo-v":
			$jslayout              = 'vertical';
			$item_height         = $item_size;
			$module_height       = $item_size + ($item_count-1) * $item_minimized;
			$css_item_height     = 'height: ' . $item_height . 'px;';
			$css_module_height   = 'height: ' . $module_height . 'px;';
			// js parameters
			$item_shift          = $item_size - $item_minimized + 10;
			break;
			
		case "default-v":
		default:
			$jslayout              = 'vertical';
			$item_height         = $item_size - 10;
			$module_height       = $item_size + ($item_count-1) * $item_minimized;
			$css_item_height     = 'height: ' . $item_height . 'px;';
			$css_module_height   = 'height: ' . $module_height . 'px;';
			// js parameters
			$item_shift          = $item_size - $item_minimized - 10;
	}
	
	// set renderer
	$renderer = new ItemRenderer();
	$renderer->addPath(array(dirname(__FILE__), ZOO_SITE_PATH));

	include(JModuleHelper::getLayoutPath('mod_zoodrawer', $theme));
	
	$javascript = "new YOOdrawer('" . $drawer_id . "', '#" . $drawer_id . " .item', { layout: '" . $jslayout . "', shiftSize: " . $item_shift . " });";
	$javascript = "window.addEvent('domready', function(){ $javascript });";
	
	$document = JFactory::getDocument();
	$document->addStyleSheet($module_base . 'mod_zoodrawer.css.php');
	$document->addScript($module_base . 'mod_zoodrawer.js');
	$document->addScriptDeclaration($javascript); 
}