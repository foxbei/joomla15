<?php
/**
* @package   ZOO Comment
* @file      mod_zoocomment.php
* @version   2.3.1
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

// init vars
$app = YTable::getInstance('application')->get($params->get('application', 0));

// is application ?
if (empty($app)) {
	return null;
}

// set one or multiple categories
$category = $params->get('category', 0);
if ($params->get('subcategories')) {
	$categories = $app->getCategoryTree(true);
	if (isset($categories[$category])) {
		$category = array_merge(array($category), array_keys($categories[$category]->getChildren(true)));
	}
}

// get latest comments
$comments = modZooCommentHelper::getLatestComments($app, $category, $params->get('count', 10));

// load template

if (!empty($comments)) {

	include(JModuleHelper::getLayoutPath('mod_zoocomment', $params->get('theme', 'list-v')));

}