<?php
/**
* @package   ZOO Component
* @file      application.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: TableApplication
		The table class for application.
*/
class ApplicationTable extends YTable {

	protected function __construct() {
		parent::__construct('Application', ZOO_TABLE_APPLICATION, 'id');
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if ($object->name == '') {
			throw new ApplicationTableException('Invalid name');
		}

		return parent::save($object);
	}

	/*
		Function: delete
			Override. Delete object from database table.

		Returns:
			Boolean.
	*/
	public function delete($object) {

		// delete related categories
		$table = YTable::getInstance('category');
		$categories = $table->getAll($object->id);
		foreach ($categories as $category) {
			$table->delete($category);
		}
		
		// delete related items
		$table = YTable::getInstance('item');
		$items = $table->findAll($object->id);
		foreach ($items as $item) {
			$table->delete($item);
		}

		return parent::delete($object);
	}

	protected function _initObject($object) {

		// init vars
		$params = new YParameter();
		$params->loadString($object->params);
		$group  = $object->application_group;
		$class  = $group.'Application';

		// load application class
		if (!class_exists($class)) {
			$path = ZOO_APPLICATION_PATH."/$group/application.php";

			if (is_file($path)) {
				require_once($path);
			}
		}

		if (class_exists($class)) {

			$data = get_object_vars($object);

			// create application instance
			$object = new $class();

			// set data
			if (is_array($data)) {
				foreach ($data as $name => $value) {
					$object->$name = $value;
				}
			}
			
		}
		
		return $object;
	}	
	
}

/*
	Class: ApplicationTableException
*/
class ApplicationTableException extends YException {}