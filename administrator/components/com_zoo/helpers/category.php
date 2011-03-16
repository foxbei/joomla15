<?php
/**
* @package   ZOO Component
* @file      category.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: CategoryHelper
   The Helper Class for category
*/
class CategoryHelper {

	/*
		Function: translateIDToAlias
			Translate category id to alias.

		Parameters:
			$id - Category id

		Returns:
			Mixed - Null or Category alias string
	*/
	public static function translateIDToAlias($id){
		$category = YTable::getInstance('category')->get($id);

		if ($category) {
			return $category->alias;
		}
		
		return null;
	}

	/*
		Function: translateAliasToID
			Translate category alias to id.
		
		Return:
			Int - The category id or 0 if not found
	*/
	public static function translateAliasToID($alias) {

		// init vars
		$db = YDatabase::getInstance();

		// search alias
		$query = 'SELECT id'
			    .' FROM '.ZOO_TABLE_CATEGORY
			    .' WHERE alias = '.$db->Quote($alias)
				.' LIMIT 1';

		return $db->queryResult($query);
	}

	/*
		Function: getAlias
			Get unique category alias.

		Parameters:
			$id - Category id
			$alias - Category alias

		Returns:
			Mixed - Null or Category alias string
	*/	
	public static function getUniqueAlias($id, $alias = '') {

		if (empty($alias) && $id) {
			$alias = JFilterOutput::stringURLSafe(YTable::getInstance('category')->get($id)->name);
		}
				
		if (!empty($alias)) {
			$i = 2;
			$new_alias = $alias;
			while (self::checkAliasExists($new_alias, $id)) {
				$new_alias = $alias . '-' . $i++;
			}
			return $new_alias;
		}
		
		return $alias;
	}	
	
	/*
 		Function: checkAliasExists
 			Method to check if a alias already exists.
	*/
	public static function checkAliasExists($alias, $id = 0) {

		$xid = intval(self::translateAliasToID($alias));
		if ($xid && $xid != intval($id)) {
			return true;
		}
		
		return false;
	}

	/*
		Function: getItemsRelatedCategoryIds
			Method to retrieve item's related category id's.

		Returns:
			Array - category id's
	*/
	public static function getItemsRelatedCategoryIds($item_id, $published = false) {

		// get database
		$db = YDatabase::getInstance();

		// select item to category relations
		$query = 'SELECT b.id'
		        .' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
		        .' JOIN '.ZOO_TABLE_CATEGORY.' AS b ON a.category_id = b.id'
			    .' WHERE a.item_id='.(int) $item_id
			    .($published == true ? ' AND b.published = 1' : '')
				.' UNION SELECT 0'
				.' FROM '.ZOO_TABLE_CATEGORY_ITEM.' AS a'
				.' WHERE a.item_id='.(int) $item_id .' AND a.category_id = 0';
		return $db->queryResultArray($query);
		
	}

	/*
		Function: saveCategoryItemRelations
			Method to add category related item's.

		Returns:
			Boolean - true on succes
	*/
	public static function saveCategoryItemRelations($item_id, $categories){

		$categories = array_unique((array) $categories);

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
			    ." WHERE item_id=".(int) $item_id;

		// execute database query
		YDatabase::getInstance()->query($query);	

		$query_string = '(%s,' . (int) $item_id.')';
		$category_strings = array();
		foreach ($categories as $category) {
			if ($category !== '' && $category !== null) {
				$category_strings[] = sprintf($query_string, $category);
			}
		}

		// add category to item relations
		// insert relation to database
		if (!empty($category_strings)) {
			$query = "INSERT INTO ".ZOO_TABLE_CATEGORY_ITEM
					." (category_id, item_id) VALUES " . implode(',', $category_strings);

			// execute database query
			YDatabase::getInstance()->query($query);
		}
		
		return true;
	}

	/*
		Function: deleteCategoryItemRelations
			Method to delete category related item's.

		Returns:
			Boolean - true on succes
	*/
	public static function deleteCategoryItemRelations($category_id){

		// delete category to item relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
			    ." WHERE category_id = ".(int) $category_id;

		// execute database query
		YDatabase::getInstance()->query($query);	

		return true;
	}

	/*
		Function: buildTree
			Build category tree.

		Parameters:
			$application_id - the application id
			$categories 	- Category data from database table

		Returns:
			Array - Category list
	*/
	public static function buildTree($application_id, $categories) {

		// create root category
		$root = new Category();
		$root->id = 0;
		$root->name = 'ROOT';
		$root->alias = '_root';
		$root->application_id = $application_id;
		$categories[0] = $root;

		foreach ($categories as $category) {		
			// set parent and child relations
			if (isset($category->parent) && isset($categories[$category->parent])) {
				$category->setParent($categories[$category->parent]);
				$categories[$category->parent]->addChildren($category);
			}
		}
		
		return $categories;
	}

	/*
		Function: buildTreeList
			Build category list which reflects the tree structure.

		Parameters:
			$id - Category id to start
			$categories - Category data tree created with self::buildTree()
			$list - Category tree list return value
			$prefix - Sublevel prefix
			$spacer - Spacer
			$indent - Indent
			$level - Start level
			$maxlevel - Maximum level depth

		Returns:
			Array - Category tree list
	*/
	public static function buildTreeList($id, $categories, $list = array(), $prefix = '<sup>|_</sup>&nbsp;', $spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $indent = '', $level = 0, $maxlevel = 9999) {

		if (isset($categories[$id]) && $level <= $maxlevel) {
			foreach ($categories[$id]->getChildren() as $child) {

				// set treename
				$id        = $child->id;
				$list[$id] = $child;
				$list[$id]->treename = $indent.($indent == '' ? $child->name : $prefix.$child->name);
				$list = self::buildTreeList($id, $categories, $list, $prefix, $spacer, $indent.$spacer, $level+1, $maxlevel);
			}
		}

		return $list;
	}

}