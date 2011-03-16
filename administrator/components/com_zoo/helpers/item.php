<?php
/**
* @package   ZOO Component
* @file      item.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: ItemHelper
   The Helper Class for item
*/
class ItemHelper {

	/*
		Function: translateIDToAlias
			Translate item id to alias.

		Parameters:
			$id - Item id

		Returns:
			Mixed - Null or Item alias string
	*/
	public static function translateIDToAlias($id){
		$item = YTable::getInstance('item')->get($id);

		if ($item) {
			return $item->alias;
		}
		
		return null;
	}
	
	/*
		Function: translateAliasToID
			Translate item alias to id.
		
		Return:
			Int - The item id or 0 if not found
	*/
	public static function translateAliasToID($alias) {

		// init vars
		$db = YDatabase::getInstance();

		// search alias
		$query = 'SELECT id'
			    .' FROM '.ZOO_TABLE_ITEM
			    .' WHERE alias = '.$db->Quote($alias)
				.' LIMIT 1';

		return $db->queryResult($query);
	}

	/*
		Function: getUniqueAlias
			Get unique item alias.

		Parameters:
			$id - Item id
			$alias - Item alias

		Returns:
			Mixed - Null or Item alias string
	*/	
	public static function getUniqueAlias($id, $alias = '') {

		if (empty($alias) && $id) {
			$alias = JFilterOutput::stringURLSafe(YTable::getInstance('item')->get($id)->name);
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

		$xid = (int)(ItemHelper::translateAliasToID($alias));
		if ($xid && $xid != (int)($id)) {
			return true;
		}
		
		return false;
	}

}