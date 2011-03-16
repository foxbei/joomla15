<?php
/**
* @package   ZOO Category
* @file      mod_zoocategory.php
* @version   2.1.0
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

// load helper
require_once(dirname(__FILE__).DS.'helper.php');

$app = YTable::getInstance('application')->get($params->get('application', 0));

// is application ?
if (empty($app)) {
	return null;
}	

// set one or multiple categories
$categories = array();
$all_categories = $app->getCategoryTree(true);
if (isset($all_categories[$params->get('category', 0)])) {
	$categories = $all_categories[$params->get('category', 0)]->getChildren();
}

if (count($categories)) {

	include(JModuleHelper::getLayoutPath('mod_zoocategory', $params->get('theme', 'default')));
	
}