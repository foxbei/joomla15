<?php
/**
* @package   ZOO Component
* @file      _menu.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// render menu
$menu = YMenu::getInstance('nav')
	->addFilter(array('ZooMenuFilter', 'activeFilter'))
	->addFilter(array('ZooMenuFilter', 'nameFilter'))
	->applyFilter();

echo '<div id="nav"><div class="bar"></div>'.$menu->render(array('YMenuDecorator', 'index')).'</div>';

/*
	Class: ZooMenuFilter
		Filter for menu class.
*/
class ZooMenuFilter {

	public static function activeFilter(YMenuItem $item) {

		// init vars
		$id         = '';
		$app 		= Zoo::getApplication();
		$controller = YRequest::getWord('controller');
		$task 		= YRequest::getWord('task');
		$classes    = array();

		// application context
		if (!empty($app)) {
			$id = $app->id.'-'.$controller;
		}

		// application configuration
		if ($controller == 'configuration' && $task) {
			if (in_array($task, array('importfrom', 'import', 'importcsv', 'importexport'))) {
				$id .= '-importexport';
			} else {
				$id .= '-'.$task;
			}
		}

		// new application
		if ($controller == 'new') {
			$id = 'new';
		}

		// application manager
		if ($controller == 'manager') {
			$id = 'manager';
			if (in_array($task, array('types', 'addtype', 'edittype', 'editelements', 'assignelements', 'assignsubmission'))) {
				$id .= '-types';
			} elseif ($task) {
				$id .= '-'.$task;
			}
		}

		// save current class attribute
		$class = $item->getAttribute('class');
		if (!empty($class)) {
			$classes[] = $class;
		}

		// set active class
		if ($item->getId() == $id || $item->hasChild($id, true)) {
			$classes[] = 'active';
		}

		// replace the old class attribute
		$item->setAttribute('class', implode(' ', $classes));		
	}
	
	public static function nameFilter(YMenuItem $item) {		
		if ($item->getId() != 'new' && $item->getId() != 'manager') {
			$item->setName(htmlspecialchars($item->getName(), ENT_QUOTES, 'UTF-8'));
		}
	}

}