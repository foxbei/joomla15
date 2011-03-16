<?php
/**
* @package   ZOO Accordion
* @file      mod_zooaccordion.php
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
if (!isset($GLOBALS['yoo_accordions'])) {
	$GLOBALS['yoo_accordions'] = 1;
} else {
	$GLOBALS['yoo_accordions']++;
}

$items = ZooModuleHelper::getItems($params);

if ($item_count = count($items)) {

	// init vars
	$theme			 = $params->get('theme', 'default');
	$layout			 = $params->get('layout', 'default');
	$open            = $params->get('open', 'first');
	$multiple_open   = $params->get('multiple_open', 0) ? 'true' : 'false';
	$module_base     = JURI::base() . 'modules/mod_zooaccordion/';
	
	// css parameters
	$accordion_id    = 'yoo-accordion-' . $GLOBALS['yoo_accordions'];	
	
	// set renderer
	$renderer = new ItemRenderer();
	$renderer->addPath(array(dirname(__FILE__), ZOO_SITE_PATH));

	include(JModuleHelper::getLayoutPath('mod_zooaccordion', $theme));

	// js parameters
	$javascript = "new YOOaccordion($$('#$accordion_id .toggler'), $$('#$accordion_id .content'), { open: '$open', allowMultipleOpen: $multiple_open });";
	$javascript = "window.addEvent('domready', function(){ $javascript });";
	
	$document = JFactory::getDocument();
	$document->addStyleSheet($module_base . 'mod_zooaccordion.css.php');
	$document->addScript($module_base . 'mod_zooaccordion.js');
	$document->addScriptDeclaration($javascript); 

}