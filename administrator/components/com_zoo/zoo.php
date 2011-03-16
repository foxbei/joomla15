<?php
/**
* @package   ZOO Component
* @file      zoo.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(dirname(__FILE__).'/config.php');

// add css, js
JHTML::script('jquery-ui.custom.min.js', ZOO_ADMIN_URI.'libraries/jquery/');
JHTML::stylesheet('jquery-ui.custom.css', ZOO_ADMIN_URI.'libraries/jquery/');
JHTML::script('accordionmenu.js', ZOO_ADMIN_URI.'assets/js/');
JHTML::script('placeholder.js', ZOO_ADMIN_URI.'assets/js/');
JHTML::script('default.js', ZOO_ADMIN_URI.'assets/js/');
JHTML::stylesheet('ui.css', ZOO_ADMIN_URI.'assets/css/');
JHTMLBehavior::modal();

// init vars
$controller = YRequest::getWord('controller');
$task       = YRequest::getWord('task');
$group      = YRequest::getString('group');

// change application
if ($id = YRequest::getInt('changeapp')) {
	JFactory::getApplication()->setUserState('com_zooapplication', $id);
}

// load application
$application = Zoo::getApplication();

// set default controller
if (!$controller) {
	$controller = $application ? 'item' : 'new';
	YRequest::setVar('controller', $controller);
}

// set toolbar button include path
$toolbar = JToolBar::getInstance('toolbar');
$toolbar->addButtonPath(ZOO_ADMIN_PATH.'/joomla/button');

// build menu
$menu = YMenu::getInstance('nav');

// add "app" menu items
foreach (YTable::getInstance('application')->all(array('order' => 'name')) as $app) {
	$app->addMenuItems($menu);
}

// add "new" and "manager" menu item
$new = new YMenuItem('new', '<span class="icon"> </span>', 'index.php?option=com_zoo&controller=new', array('class' => 'new'));
$manager = new YMenuItem('manager', '<span class="icon"> </span>', 'index.php?option=com_zoo&controller=manager', array('class' => 'config'));
$menu->addChild($new);
$menu->addChild($manager);

if ($controller == 'new' && $task == 'add' && $group) {

	// get application meta
	$app = new Application();
	$app->setGroup($group);
	$meta = $app->getMetaData();

	// add info item
	$new->addChild(new YMenuItem('new', $meta['name']));
}

if ($controller == 'manager' && $group) {
		
	// get application meta
	$app = new Application();
	$app->setGroup($group);
	$meta = $app->getMetaData();

	// add info item
	$info = new YMenuItem('manager-types', $meta['name'], 'index.php?option=com_zoo&controller=manager&task=types&group='.$group);
	$info->addChild(new YMenuItem('manager-types', 'Types', 'index.php?option=com_zoo&controller=manager&task=types&group='.$group));
	$info->addChild(new YMenuItem('manager-info', 'Info', 'index.php?option=com_zoo&controller=manager&task=info&group='.$group));
	$manager->addChild($info);
}

try {

	if ($application) {

		// dispatch current application
		$application->dispatch();

	} else {

		// load controller
		require_once(ZOO_ADMIN_PATH."/controllers/$controller.php");

		// perform the request task
		$class      = $controller.'Controller';
		$controller = new $class();
		$controller->execute($task);
		$controller->redirect();
	}

} catch (YException $e) {
	JError::raiseError(500, $e);
}