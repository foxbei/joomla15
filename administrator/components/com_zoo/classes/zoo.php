<?php
/**
* @package   ZOO Component
* @file      zoo.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Zoo
		The general class for zoo and applications
*/
class Zoo {

	protected static $_application;

	/*
		Function: getApplication
		  Returns a reference to the currently active Application object.
	
	   Returns:
	      Application	
	*/	
	public static function getApplication() {

		// check if application object already exists
		if (isset(self::$_application)) {
			return self::$_application;
		}

		// get joomla and application table
		$joomla = JFactory::getApplication();
		$table  = YTable::getInstance('application');
		
		// handle admin
		if ($joomla->isAdmin()) {

			// create application from user state, or search for default
			$id   = $joomla->getUserState('com_zooapplication');
			$apps = $table->all(array('order' => 'name'));

			if (isset($apps[$id])) {
				self::$_application = $apps[$id];
			} else if (!empty($apps)) {
				self::$_application = array_shift($apps);
			}

			return self::$_application;
		}

		// handle site
		if ($joomla->isSite()) {

			// get component params
			$params = $joomla->getParams();
						
			// create application from menu item params / request
			if ($item_id = YRequest::getInt('item_id')) {
				if ($item = YTable::getInstance('item')->get($item_id)) {
					self::$_application = $item->getApplication();
				}
			} else if ($category_id = YRequest::getInt('category_id')) { 
				if ($category = YTable::getInstance('category')->get($category_id)) {
					self::$_application = $category->getApplication();
				}
			} else if ($id = YRequest::getInt('app_id')) {
				self::$_application = $table->get($id);
			} else if ($id = $params->get('application')) {
				self::$_application = $table->get($id);
			} else {
				// try to get application from default menu item
				$menu    = JSite::getMenu(true);
				$default = $menu->getDefault();
				if (isset($default->component) && $default->component == 'com_zoo') {
					if ($app_id = $menu->getParams($default->id)->get('application')) {
						self::$_application = $table->get($app_id);
					}
				}
			}

			return self::$_application;
		}
		
		return null;
	}

	public static function getApplicationGroups() {

		// get applications
		$apps = array();
		$path = ZOO_APPLICATION_PATH;

		if (is_dir($path) && ($folders = JFolder::folders($path))) {
			foreach ($folders as $folder) {
				if (JFile::exists($path.'/'.$folder.'/application.xml')) {
					$apps[$folder] = new Application();
					$apps[$folder]->setGroup($folder);
				}
			}
		}		
		
		return $apps;
	}
	
}