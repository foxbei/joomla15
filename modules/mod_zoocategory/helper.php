<?php
/**
* @package   ZOO Category
* @file      helper.php
* @version   2.1.0
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class CategoryRenderer {

    public static function render($category, $params, $level) {

		// init vars
		$menu_item = $params->get('menu_item');
		$max_depth = $params->get('depth', 0);

		if ($menu_item) {
			$url = 'index.php?option=com_zoo&task=category&category_id='.$category->id.'&Itemid='.$menu_item;
		} else {
			$url = RouteHelper::getCategoryRoute($category);
		}

		$result   = array();
		$result[] = '<li>';
		$result[] = '<a href="'.JRoute::_($url).'">'.$category->name.'</a>';

		if ((!$max_depth || $max_depth >= $level) && ($children = $category->getChildren()) && !empty($children)) {
			$result[] = '<ul class="level'.$level.'">';
			foreach ($children as $child) {
				$result[] = self::render($child, $params, $level+1);
			}
			$result[] = '</ul>';
		}

		$result[] = '</li>';

		return implode("\n", $result);
	}

}