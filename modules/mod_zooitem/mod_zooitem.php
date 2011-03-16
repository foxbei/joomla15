<?php
/**
* @package   ZOO Item
* @file      mod_zooitem.php
* @version   2.3.2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

if ($app = YTable::getInstance('application')->get($params->get('application', 0))) {

	$categories = $app->getCategoryTree(true);

	$items = ZooModuleHelper::getItems($params);

	// load template
	if (!empty($items)) {

		// set renderer
		$renderer = new ItemRenderer();
		$renderer->addPath(array(dirname(__FILE__), ZOO_SITE_PATH));

		$layout = $params->get('layout', 'default');

		include(JModuleHelper::getLayoutPath('mod_zooitem', $params->get('theme', 'list-v')));
	}
}