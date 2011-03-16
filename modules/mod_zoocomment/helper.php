<?php
/**
* @package   ZOO Comment
* @file      helper.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modZooCommentHelper {
	
	const MAX_CHARACTERS = 140;
	
	public static function getLatestComments($app, $categories, $limit) {

		// build where condition
		$where   = array('b.application_id = '.(int) $app->id);
		$where[] = 'a.state = ' . 1;
		$where[] = is_array($categories) ? "c.category_id IN (".implode(",", $categories).")" : "c.category_id = " . $categories;

		// build query options
		$options = array(
			'select'     => 'a.*, b.application_id',
			'from'       => ZOO_TABLE_COMMENT.' AS a'
							. ' LEFT JOIN '.ZOO_TABLE_ITEM.' AS b ON a.item_id = b.id'
							. ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS c ON b.id = c.item_id',
			'conditions' => array(implode(' AND ', $where)),
			'order'      => 'created DESC',
			'group'		 => 'a.id',
			'offset' 	 => 0,
			'limit'		 => $limit);
							
		// query comment table
		return YTable::getInstance('comment')->all($options);
	}
	
}