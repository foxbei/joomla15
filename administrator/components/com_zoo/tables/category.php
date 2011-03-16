<?php
/**
* @package   ZOO Component
* @file      category.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CategoryTable
		The table class for categories.
*/
class CategoryTable extends YTable {

	protected function __construct() {
		parent::__construct('Category', ZOO_TABLE_CATEGORY, 'id');
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if ($object->name == '') {
			throw new CategoryTableException('Invalid name');
		}

		if ($object->alias == '' || $object->alias != YString::sluggify($object->alias)) {
			throw new CategoryTableException('Invalid slug');
		}
		
		if (CategoryHelper::checkAliasExists($object->alias, $object->id)) {
			throw new CategoryTableException('Slug already exists, please choose a unique slug');
		}		
		
		if (!is_numeric($object->parent)) {
			throw new CategoryTableException('Invalid parent id');
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

		// get database
		$db = $this->getDBO();
		
		// update childrens parent category
		$query = "UPDATE ".$this->getTableName()
		    	." SET parent=".$object->parent
			    ." WHERE parent=".$object->id;
		$db->query($query);

		// delete category to item relations
		CategoryHelper::deleteCategoryItemRelations($object->id);

		return parent::delete($object);
	}

	protected function _initObject($object) {

		// add to cache
		$key_name = $this->getKeyName();

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		return $object;
	}

	/*
		Function: getbyId
			Method to retrieve categories by id.

		Parameters:
			$category_id - Categoryid(s)

		Returns:
			Array - Array of categories
	*/
	public function getById($ids, $published = false){

		if (empty($ids)) {
			return array();
		}

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$ids = array_filter($ids, create_function('$id', 'return !empty($id);'));

		$objects = array_intersect_key($this->_objects, array_flip($ids));

		$ids = array_flip(array_diff_key(array_flip($ids), $objects));

		if (!empty($ids)) {
			$where = "id IN (".implode(",", $ids).")" . ($published == true ? " AND published = 1" : "");
			$objects = array_diff_key($this->all(array('conditions' => $where, 'order' => 'ordering')), $objects);
		}

		return $objects;
	}

	/*
		Function: getByName
			Method to retrieve categories by name.

		Parameters:
			$application_id - application id
			$name - Category name

		Returns:
			Array - Array of categories
	*/
	public function getByName($application_id, $name){

		if (empty($name) || empty($application_id)) {
			return array();
		}
		$conditions = "application_id=" . (int) $application_id . " AND name=" . YDatabase::getInstance()->quote($name);
		return $this->all(compact('conditions'));

	}

	/*
		Function: getAll
			Method to retrieve all categories of an application.

		Parameters:
			$application_id - Application Id
			$published - Only published categories

		Returns:
			Array - Array of categories
	*/
	public function getAll($application_id, $published = false, $item_count = false){

		if ($item_count) {

			$db = $this->getDBO();
			$db->query('SET SESSION group_concat_max_len = 1048576');

			$select = 'c.*, GROUP_CONCAT(DISTINCT ci.item_id) as item_ids';
			$from	= $this->getTableName() . ' as c  USE INDEX (APPLICATIONID_ID_INDEX) LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' as ci ON ci.category_id = c.id';

			if ($published) {

				// get user access id
				$access_id = JFactory::getUser()->get('aid', 0);

				// get dates
				$date = JFactory::getDate();
				$now  = $db->Quote($date->toMySQL());
				$null = $db->Quote($db->getNullDate());

				$select = 'c.*, GROUP_CONCAT(DISTINCT i.id) as item_ids';

				$from  .= ' LEFT JOIN '.ZOO_TABLE_ITEM.' AS i USE INDEX (MULTI_INDEX2) ON ci.item_id = i.id'
						.' AND i.access <= '.(int) $access_id
						.' AND i.state = 1'
						.' AND (i.publish_up = '.$null.' OR i.publish_up <= '.$now.')'
						.' AND (i.publish_down = '.$null.' OR i.publish_down >= '.$now.')';
			}

			$where  = 'c.application_id = ?' . ($published == true ? " AND c.published = 1" : "");
			$conditions = array($where, $application_id);
			$group = 'c.id';

			$categories = $this->all(compact('select', 'from', 'conditions', 'group'));

			// sort categories
			uasort($categories, create_function('$a, $b', '
				if ($a->ordering == $b->ordering) {
					return 0;
				}
				return ($a->ordering < $b->ordering) ? -1 : 1;'
			));

		} else {
			$where = "application_id = ?" . ($published == true ? " AND published = 1" : "");
			
			$categories = $this->all(array('conditions' => array($where, $application_id), 'order' => 'ordering'));
		}

		return $categories;
	}

	/*
		Function: getByItemId
			Method to retrieve item's related categories.

		Parameters:
			$item_id - Item id
			$published - Only published categories

		Returns:
			Array - Related categories
	*/
	public function getByItemId($item_id, $published = false) {
		$query = 'SELECT b.*'
	            .' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
	            .' JOIN '.$this->getTableName().' AS b ON b.id = a.category_id'
			    .' WHERE a.item_id='.(int) $item_id
			    .($published == true ? " AND b.published = 1" : "");

		return $this->_queryObjectList($query, $this->getKeyName());
	}

	/*
		Function: count
			Method to retrieve count categories of an application.

		Parameters:
			$application_id - Application id

		Returns:
			Int
	*/
	public function count($application_id){
		$query = "SELECT COUNT(*)"
			." FROM ".$this->getTableName()
			." WHERE application_id = ".(int) $application_id;

		return (int) $this->_queryResult($query);
	}
	
	/*
		Function: updateorder
			Method to check/fix category ordering.

		Parameters:
			$application_id - Application id
			$parents - Parent category id(s)

		Returns:
			Boolean. True on success
	*/
	public function updateorder($application_id, $parents = array()) {
		
		if (!is_array($parents)) {
			$parents = array($parents);
		}

		// execute update order for each parent categories
		$parents = array_unique($parents);
		foreach ($parents as $parent) {
			if (!$this->reorder('application_id = '.(int) $application_id.' AND parent = '.(int) $parent)) {
				return false;
			}
		}

		return true;
	}

	/*
		Function: reorder
			Compacts the ordering sequence of the selected records.

		Parameters:
			$where - SQL where condition

		Returns:
			Boolean. True on success
	*/
	public function reorder($where = '') {

		// get database
		$db = $this->getDBO();

		// get rows
		$query = 'SELECT '.$this->getKeyName().', ordering'
		        .' FROM '.$this->getTableName()
		        .($where ? ' WHERE '.$where : '')
		        .' ORDER BY ordering';
		$rows =	$db->queryObjectList($query, $this->getKeyName());

		// init vars
		$i      = 1;
		$update = array();

		// collect rows which ordering need to be updated
		foreach ($rows as $id => $row) {

			if ($row->ordering != $i) {
				$update[$i - $row->ordering][] = $id;
			}

			$i++;
		}
		
		// do the ordering update
		foreach ($update as $diff => $ids) {

			// build ordering update query
			$query = 'UPDATE '.$this->getTableName()
				    .sprintf(' SET ordering = (ordering'.($diff >= 0 ? '+' : '').'%s)', $diff)
				    .sprintf(' WHERE '.$this->getKeyName().(count($ids) == 1 ? ' = %s' : ' IN (%s)'), implode(',', $ids));

			// set and execute query
			$db->query($query);
		}

		return true;
	}	

}

/*
	Class: CategoryTableException
*/
class CategoryTableException extends YException {}