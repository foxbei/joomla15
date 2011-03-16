<?php
/**
* @package   ZOO Slider
* @file      mod_zooslider.php
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
if (!isset($GLOBALS['yoo_sliders'])) {
	$GLOBALS['yoo_sliders'] = 1;
} else {
	$GLOBALS['yoo_sliders']++;
}

$items = ZooModuleHelper::getItems($params);

if ($item_count = count($items)) {
	// init vars
	$theme			 	= $params->get('theme', 'default-h');
	$layout			 	= $params->get('layout', 'default');
	$module_height      = $params->get('module_height', 150);
	$item_size          = $params->get('item_size', 170);
	$item_expanded      = $params->get('item_expanded', 310);
	$module_base        = JURI::base() . 'modules/mod_zooslider/';
	
	// css parameters
	$slider_id          = 'yoo-slider-' . $GLOBALS['yoo_sliders'];
	
	switch ($theme) {
		// vertical
		case "default-v":
		case "photo-v":
			$jslayout                   = 'vertical';
			$item_height              = $item_size;
			$item_height_expanded     = $item_expanded;
			$module_height		      = ($theme ==  "photo-v") ? ($item_height * $item_count) - 3 : ($item_height * $item_count) + 5;
			$module_height		      = ($theme ==  "photo-v") ? $module_height + (10 * ($item_count - 1)) : $module_height; /* only for photo-v styling */
			$css_item_height          = 'height: ' . $item_height . 'px;';
			$css_item_height_expanded = 'height: ' . $item_height_expanded . 'px;';
			$css_module_height        = 'height: ' . $module_height . 'px;';
			break;
	
		// horizontal
		case "default-h":
		case "photo-h":
		default:
			$jslayout                   = 'horizontal';
			$item_width               = $item_size;
			$item_height              = $module_height;
			$slide_height             = $item_height - 5;
			$module_width		      = ($theme ==  "photo-h") ? ($item_width * $item_count) + (10 * ($item_count - 1)) - 3 : 0; /* only for photo-h styling */
			$all_items_width	      = $module_width + 10; /* only for photo-h styling */
			$css_item_width           = 'width: ' . $item_width . 'px;';
			$css_item_height          = 'height: ' . $item_height . 'px;';
			$css_slide_height         = 'height: ' . $slide_height . 'px;';
			$css_module_height        = 'height: ' . $module_height . 'px;';
			$css_module_width         = 'width: ' . $module_width . 'px;'; /* only for photo-h styling */
			$css_all_items_width      = 'width: ' . $all_items_width . 'px;'; /* only for photo-h styling */
			$css_item_expanded        = 'width: ' . $item_expanded . 'px;';
	}
	
	// set renderer
	$renderer = new ItemRenderer();
	$renderer->addPath(array(dirname(__FILE__), ZOO_SITE_PATH));

	include(JModuleHelper::getLayoutPath('mod_zooslider', $theme));

	// js parameters
	$javascript = "new YOOslider('" . $slider_id . "', '#" . $slider_id . " .slide', { layout: '" . $jslayout . "', sizeNormal: " . $item_size . ", sizeFull: " . $item_expanded . " });";
	$javascript = "window.addEvent('domready', function(){ $javascript });";
		
	$document = JFactory::getDocument();
	$document->addStyleSheet($module_base . 'mod_zooslider.css.php');
	$document->addScript($module_base . 'mod_zooslider.js');
	$document->addScriptDeclaration($javascript); 
	
}