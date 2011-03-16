<?php
/**
* @package   ZOO Carousel
* @file      mod_zoocarousel.php
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
if (!isset($GLOBALS['yoo_carousels'])) {
	$GLOBALS['yoo_carousels'] = 1;
} else {
	$GLOBALS['yoo_carousels']++;
}

$items = ZooModuleHelper::getItems($params);

if ($item_count = count($items)) {

	// init vars
	$theme			 	 = $params->get('theme', 'default');
	$layout				 = $params->get('layout', 'default');
	$module_width        = $params->get('module_width', '400');
	$module_height       = $params->get('module_height', '200');
	$tab_width           = $params->get('tab_width', '200');
	$slide_interval      = $params->get('slide_interval', '3000');
	$transition_effect   = $params->get('transition_effect', 'scroll');
	$transition_duration = $params->get('transition_duration', '700');
	$control_panel       = $params->get('control_panel', 'top');
	$rotate_action       = $params->get('rotate_action', 'click');
	$rotate_duration     = $params->get('rotate_duration', '100');
	$rotate_effect       = $params->get('rotate_effect', 'scroll');
	$buttons             = $params->get('buttons', '1');
	$autoplay            = $params->get('autoplay', 'on');
	$module_base         = JURI::base() . 'modules/mod_zoocarousel/';
	
	// css parameters
	$carousel_id           = 'yoo-carousel-' . $GLOBALS['yoo_carousels'];
	
	switch ($theme) {
		case "basic":
			$tab_height    = 30;
			$control_panel = ($control_panel == "top" || $control_panel == "bottom") ? $control_panel : "top";
	   		break;
		case "button":
			$tab_height    = 35;
			$control_panel = "top";
	   		break;
		case "plain":
			$tab_height    = 30;
			$control_panel = "top";
			$buttons       = 0;
	   		break;
		case "slideshow":
			$tab_height    = 0;
			$control_panel = "none";
	   		break;
		case "list":
			$control_panel = "left";
	   		break;
		case "basiclist":
			$control_panel = "left";
	   		break;
		case "plainlist":
			$module_height = 40 * $item_count;
			$control_panel = (!($control_panel ==  "left" || $control_panel == "right") ) ? "left" : $control_panel;
	   		break;
		default:
			$tab_height    = 40;
			$control_panel = "top";
	}
	
	if ($theme == "list" || $theme == "basiclist" || $theme == "plainlist") {
		$panel_width             = $module_width - $tab_width;
		$panel_width             = ($theme ==  "basiclist") ? $panel_width - 4 : $panel_width; /* only for basiclist styling */
		$panel_height            = ($theme ==  "list") ? $module_height - 40 : $module_height - 4;
		$css_tab_width           = 'width: ' . $tab_width . 'px;';
		$css_module_width        = 'width: ' . $module_width . 'px;';
		$css_module_height       = 'height: ' . $module_height . 'px;';
		$css_panel_width         = 'width: ' . $panel_width . 'px;';
		$css_panel_height        = 'height: ' . $panel_height . 'px;';
		$css_total_panel_width   = 'width: ' . ($panel_width * $item_count + 3) . 'px;';
	} else {
		$button_width            = ($theme ==  "default") ? 50 : 0; /* only for default styling */
		$panel_width             = ($buttons) ? $module_width - (2 * $button_width) : $module_width;
		$panel_width             = ($theme ==  "basic") ? $panel_width - 4 : $panel_width; /* only for basic styling */
		$panel_height            = ($control_panel != "none") ? $module_height - $tab_height : $module_height;
		$panel_height            = ($theme ==  "basic") ? $panel_height - 2 : $panel_height; /* only for basic styling */
		$panel_height            = ($theme ==  "button") ? $panel_height - 7 : $panel_height; /* only for button styling */
		$panel_width             = ($theme ==  "button") ? $panel_width - 10 : $panel_width; /* only for button styling */	
		$css_module_width        = 'width: ' . $module_width . 'px;';
		$css_module_height       = 'height: ' . $module_height . 'px;';
		$css_total_module_width  = 'width: ' . ($module_width * $item_count + 3) . 'px;';
		$css_panel_width         = 'width: ' . $panel_width . 'px;';
		$css_panel_height        = 'height: ' . $panel_height . 'px;';
		$css_total_panel_width   = 'width: ' . ($panel_width * $item_count + 3) . 'px;';
	}
	
	if ($transition_effect == 'crossfade' || $rotate_effect == 'crossfade') {
		$css_slide_position = ' position: absolute;';
		$css_total_panel_width = $css_panel_width;
	} else {
		$css_slide_position = '';
	}
	
	// set renderer
	$renderer = new ItemRenderer();
	$renderer->addPath(array(dirname(__FILE__), ZOO_SITE_PATH));

	include(JModuleHelper::getLayoutPath('mod_zoocarousel', $theme));

	// js parameters
	$javascript = "new YOOcarousel('" . $carousel_id . "', { transitionEffect: '" . $transition_effect . "', transitionDuration: " . $transition_duration . ", rotateAction: '" . $rotate_action . "', rotateActionDuration: " . $rotate_duration . ", rotateActionEffect: '" . $rotate_effect . "', slideInterval: " . $slide_interval . ", autoplay: '" . $autoplay . "' });";
	$javascript = "window.addEvent('domready', function(){ $javascript });";	
	
	$document = JFactory::getDocument();
	$document->addStyleSheet($module_base . 'mod_zoocarousel.css.php');
	$document->addScript($module_base . 'mod_zoocarousel.js');
	$document->addScriptDeclaration($javascript); 
}
