<?php
/**
* @package   ZOO Component
* @file      route.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: RouteHelper
   The Helper Class for building links
*/
class RouteHelper {

	const LINK_BASE = 'index.php?option=com_zoo';
	
	protected static $_item_links, $_category_links = array();
	protected static $_menu_items;

	public function getAlphaIndexRoute($application_id, $alpha_char = null) {

		// build frontpage link
		$link = RouteHelper::LINK_BASE . '&task=alphaindex&app_id='.$application_id;
		$link .= $alpha_char !== null ? '&alpha_char=' . $alpha_char : '';

		if (($menu_item = self::_findFrontpage($application_id)) || ($menu_item = JSite::getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function getCategoryRoute($category) {

		YTable::getInstance('application')->get($category->application_id)->getCategoryTree(true);

		// have we found the link before?
		if (isset(self::$_category_links[$category->id])) {
			return self::$_category_links[$category->id];
		}

		// build category link
		$link = RouteHelper::LINK_BASE . '&task=category&category_id='.$category->id;

		// Priority 1: find direct link to category || Priority 2: find in category path
		if (($menu_item = self::_findCategory($category->id)) || ($menu_item = self::_findInCategoryPath($category))) {
			$link .= '&Itemid='.$menu_item->id;
		} else {

			// Priority 3: link to frontpage || Priority 4: current item id
			if (($menu_item = self::_findFrontpage($category->application_id)) || ($menu_item = JSite::getMenu()->getActive())) {
				$link .= '&Itemid='.$menu_item->id;
			}
		}

		// store link for future lookups
		self::$_category_links[$category->id] = $link;

		return $link;
		
	}

	public function getFeedRoute($category, $feed_type) {

		// build feed link
		$link = RouteHelper::LINK_BASE . '&task=feed&app_id='.$category->application_id.'&category_id='.$category->id.'&format=feed&type='.$feed_type;

		if (($menu_item = self::_findFrontpage($category->application_id)) || ($menu_item = JSite::getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function getFrontpageRoute($application_id) {

		// build frontpage link
		$link = RouteHelper::LINK_BASE . '&task=frontpage';

		if (($menu_item = self::_findFrontpage($application_id)) || ($menu_item = JSite::getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function getItemRoute($item) {

		YTable::getInstance('application')->get($item->application_id)->getCategoryTree(true);

		// have we found the link before?
		if (isset(self::$_item_links[$item->id])) {
			return self::$_item_links[$item->id];
		}

		// init vars
		$categories = $item->getRelatedCategoryIds(true);
		$categories = array_filter($categories, create_function('$id', 'return !empty($id);'));
		
		// build item link
		$link = RouteHelper::LINK_BASE . '&task=item&item_id='.$item->id;

		// Priority 1: direct link to item
		$itemid = null;
		if ($menu_item = self::_findItem($item->id)) {
			$itemid = $menu_item->id;
		}

		// are we in category view?
		$category_id = null;
		
		if (!$itemid && (YRequest::getCmd('task') == 'category' || YRequest::getCmd('view') == 'category')) {
			$category_id = (int) YRequest::getInt('category_id', JFactory::getApplication()->getParams()->get('category'));
			$category_id = in_array($category_id, $categories) ? $category_id : null;
		}

		if (!$itemid && !$category_id) {
			$primary = $item->getPrimaryCategory();
			
			// Priority 2: direct link to primary category
			if ($primary && $menu_item = self::_findCategory($primary->id)) {
				$itemid = $menu_item->id;
			// Priority 3: find in primary category path
			} else if ($primary && $menu_item = self::_findInCategoryPath($primary)) {
				$itemid = $menu_item->id;
			} else {
				$found = false;
				foreach ($categories as $category) {
					// Priority 4: direct link to any related category
					if ($menu_item = self::_findCategory($category)) {
						$itemid = $menu_item->id;
						$found = true;
						break;
					}
				}

				if (!$found) {
					$categories = $item->getRelatedCategories(true);
					foreach ($categories as $category) {
						// Priority 5: find in any related categorys path
						if ($menu_item = self::_findInCategoryPath($category)) {
							$itemid = $menu_item->id;
							$found = true;
							break;
						}
					}
				}

				// Priority 6: link to frontpage
				if (!$found && $menu_item = self::_findFrontpage($item->application_id)) {
					$itemid = $menu_item->id;
				}
			}
		}

		if ($category_id) {
			$link .= '&category_id=' . $category_id;
		}

		if($itemid) {
			$link .= '&Itemid='.$itemid;
		// Priority 7: current item id
		} else if ($menu = JSite::getMenu()->getActive()) {
			$link .= '&Itemid='.$menu->id;
		}

		// store link for future lookups
		self::$_item_links[$item->id] = $link;

		return $link;
	}

	public function getMySubmissionsRoute($submission) {

		$link = RouteHelper::LINK_BASE . '&view=submission&layout=mysubmissions&submission_id='.$submission->id;

		if ($menu_item = JSite::getMenu()->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function getSubmissionRoute($submission, $type_id, $hash, $item_id = null, $redirect = null) {
		
		$redirect = !empty($redirect) ? '&redirect='.$redirect : '';
		$item_id  = !empty($item_id) ? '&item_id='.$item_id : '';

		$link = RouteHelper::LINK_BASE . '&view=submission&layout=submission&submission_id='.$submission->id.'&type_id='.$type_id.$item_id.'&submission_hash='.$hash.$redirect;

		if ($menu_item = JSite::getMenu()->getActive()) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	public function getTagRoute($application_id, $tag) {

		// build tag link
		$link = RouteHelper::LINK_BASE . '&task=tag&tag='.$tag.'&app_id='.$application_id;

		// Priority 1: link to frontpage || Priority 2: current item id
		if (($menu_item = self::_findFrontpage($application_id)) || ($menu_item = JSite::getMenu()->getActive())) {
			$link .= '&Itemid='.$menu_item->id;
		}

		return $link;

	}

	protected function _findItem($item_id) {
		self::_setMenuItems();

		if (isset(self::$_menu_items['item'][$item_id])) {
			return self::$_menu_items['item'][$item_id];
		}	
	}

	protected function _findCategory($category_id)	{
		self::_setMenuItems();

		if (isset(self::$_menu_items['category'][$category_id])) {
			return self::$_menu_items['category'][$category_id];
		}
	}

	protected function _findInCategoryPath($category) {
		self::_setMenuItems();

		foreach ($category->getPathway() as $id => $cat) {
			if ($menu_item = self::_findCategory($id)) {
				return $menu_item;
			}
		}
	}

	protected function _findFrontpage($application_id)	{
		self::_setMenuItems();

		if (isset(self::$_menu_items['frontpage'][$application_id])) {
			return self::$_menu_items['frontpage'][$application_id];
		}
	}

	protected function _setMenuItems() {
		if (self::$_menu_items == null) {
			$component = JComponentHelper::getComponent('com_zoo');

			$menus		= JSite::getMenu();
			$menu_items	= $menus->getItems('componentid', $component->id);
			$menu_items = $menu_items ? $menu_items : array();

			self::$_menu_items = array('frontpage' => array(), 'category' => array(), 'item' => array());
			$params = new YParameter();
			foreach($menu_items as $menu_item) {
				switch (@$menu_item->query['view']) {
					case 'frontpage':
						self::$_menu_items['frontpage'][$params->loadString($menu_item->params)->get('application')] = $menu_item;
						break;
					case 'category':
						self::$_menu_items['category'][$params->loadString($menu_item->params)->get('category')] = $menu_item;
						break;
					case 'item':
						self::$_menu_items['item'][$params->loadString($menu_item->params)->get('item_id')] = $menu_item;
						break;
				}
			}
		}
		return self::$_menu_items;
	}

}
