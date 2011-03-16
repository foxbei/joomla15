<?php
/**
* @package   ZOO Tag
* @file      helper.php
* @version   2.1.0
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modZooTagHelper {

	const MIN_FONT_WEIGHT = 1;
	const MAX_FONT_WEIGHT = 10;
	
	public static function buildTagCloud($app, $params) {
		
		// get tags
		$tags = YTable::getInstance('tag')->getAll($app->id, null, null, 'items DESC', null, $params->get('count', 10), true);		
		
		if (count($tags)) {
			
			// init vars
			$min_count 		 = $tags[count($tags)-1]->items;
			$max_count 		 = $tags[0]->items;
			$font_span 		 = ($max_count - $min_count) / 100;
			$font_class_span = (self::MAX_FONT_WEIGHT - self::MIN_FONT_WEIGHT) / 100;
			$menu_item 		 = $params->get('menu_item', 0);
			$itemid    		 = $menu_item ? '&Itemid='.$menu_item : '';
			
			// attach font size, href
			foreach ($tags as $tag) {
				$tag->weight = $font_span ? round(self::MIN_FONT_WEIGHT + (($tag->items - $min_count) / $font_span) * $font_class_span) : 1;
				$tag->href   = $menu_item ? sprintf('index.php?option=com_zoo&task=tag&tag=%s&app_id=%d%s', $tag->name, $app->id, $itemid) : RouteHelper::getTagRoute($app->id, $tag->name);
			}
			
			self::orderTags($tags, $params->get('order'));
	
			return $tags;
		
		}
		
		return array();
		
	}
	
	public static function orderTags(&$tags, $order) {
		switch ($order) {
			case 'alpha':
				usort($tags, create_function('$a, $b', 'return strcmp($a->name, $b->name);'));
				break;
			case 'ralpha':
				usort($tags, create_function('$a, $b', 'return strcmp($b->name, $a->name);'));
				break;
			case 'acount':
				krsort($tags);
				$tags = array_merge($tags);
				break;
			case 'ocount':		
				self::_count_sort($tags);
				break;
			case 'icount':
				krsort($tags);
				self::_count_sort($tags);
				break;
			case 'random':
				shuffle($tags);
				break;				
		}
	}
	
	protected static function _count_sort(&$tags) {
		$tags = array_merge($tags);
		$sorted_tags = array();
		$prefix = 1;
		for ($i = 0; $i < count($tags); $i++) {
			$sorted_tags[(int)((count($tags) + ($prefix * $i)) / 2)] = $tags[$i];
			$prefix *= -1;
		}
		ksort($sorted_tags);
		$tags = $sorted_tags;		
	}
	
}