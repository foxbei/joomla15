<?php
/**
* @package   ZOO Component
* @file      default.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: DefaultController
		Site controller class
*/
class DefaultController extends YController {

	public $application;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application = Zoo::getApplication();

		// registers tasks
		$this->registerTask('frontpage', 'category');
	}

	/*
	 	Function: display
			View method for MVC based architecture

		Returns:
			Void
	*/
	public function display() {
				
		// execute task
		$this->_taskMap['display'] = null;
		$this->_taskMap['__default'] = null;
		$this->execute(YRequest::getCmd('view'));
	}

	/*
	 	Function: callElement
			Element callback method

		Returns:
			Void
	*/
	public function callElement() {
		
		// get request vars		
		$element = YRequest::getCmd('element', '');
		$method  = YRequest::getCmd('method', '');
		$args    = YRequest::getVar('args', array(), 'default', 'array');
		$item_id = (int) YRequest::getInt('item_id', 0);
		
		JArrayHelper::toString($args);

		// get item
		$item = YTable::getInstance('item')->get($item_id);
		
		// raise warning when item can not be accessed
		if (empty($item->id) || !$item->canAccess($this->user)) {
			JError::raiseError(500, JText::_('Unable to access item'));
			return;
		}
		
		// raise warning when item is not published
		$nulldate     = JFactory::getDBO()->getNullDate();
		$date         = JFactory::getDate()->toUnix();
		$publish_up   = JFactory::getDate($item->publish_up);
		$publish_down = JFactory::getDate($item->publish_down);
		if ($item->state != 1 || !(
		   ($item->publish_up == $nulldate || $publish_up->toUnix() <= $date) &&
		   ($item->publish_down == $nulldate || $publish_down->toUnix() >= $date))) {
			JError::raiseError(404, JText::_('Item not published'));
			return;
		}
		
		// get element and execute callback method
		if ($element = $item->getElement($element)) {
			$element->callback($method, $args);
		}
	}

	public function item() {

		// get request vars
		$item_id = (int) YRequest::getInt('item_id', $this->params->get('item_id', 0));	

		// get item
		$this->item = YTable::getInstance('item')->get($item_id);

		// raise warning when item can not be accessed
		if (empty($this->item->id) || !$this->item->canAccess($this->user)) {
			JError::raiseError(500, JText::_('Unable to access item'));
			return;
		}

		// add canonical
		$document = JFactory::getDocument();
		if ($document instanceof JDocumentHTML) {
			$document->addHeadLink(JRoute::_(RouteHelper::getItemRoute($this->item)), 'canonical');
		}	

		// get category_id
		$category_id = (int) YRequest::getInt('category_id', $this->item->getPrimaryCategoryId());	

		// raise warning when item is not published
		$nulldate     = JFactory::getDBO()->getNullDate();
		$date         = JFactory::getDate()->toUnix();
		$publish_up   = JFactory::getDate($this->item->publish_up);
		$publish_down = JFactory::getDate($this->item->publish_down);
		if ($this->item->state != 1 || !(
		   ($this->item->publish_up == $nulldate || $publish_up->toUnix() <= $date) &&
		   ($this->item->publish_down == $nulldate || $publish_down->toUnix() >= $date))) {
			JError::raiseError(404, JText::_('Item not published'));
			return;
		}

		// create item pathway
		$itemid = $this->params->get('item_id');
		if ($this->item->id != $itemid) {
			$categories = $this->application->getCategoryTree(true);
			if (isset($categories[$category_id])) {
				$category = $categories[$category_id];
				$addpath = false;
				$catid   = $this->params->get('category');
				foreach ($category->getPathway() as $cat) {
					if (!$catid || $addpath) {
						$link = JRoute::_(RouteHelper::getCategoryRoute($cat));
						$this->pathway->addItem($cat->name, $link);
					}
					if ($catid && $catid == $cat->id) {
						$addpath = true;
					}
				}
			}

			$link = RouteHelper::getItemRoute($this->item);
			$this->pathway->addItem($this->item->name, JRoute::_($link));
		}
		
		// update hit count
		$this->item->hit();

		// get page title, if exists
		$title = $this->item->name;
		$menus = JSite::getMenu();
		$menu  = $menus->getActive();
		if ($menu) {
			$menu_params = new JParameter($menu->params);
			if ($menu_params->get('page_title')) {
				$title = $menu_params->get('page_title');
			}
		}

	 	// set metadata
		$this->document->setTitle($title);
		if ($this->joomla->getCfg('MetaTitle')) $this->document->setMetadata('title', $this->item->name);
		if ($this->joomla->getCfg('MetaAuthor')) $this->document->setMetadata('author', $this->item->getAuthor());
		if ($description = $this->item->getParams()->get('metadata.description')) $this->document->setDescription($description);
		foreach (array('keywords', 'author', 'robots') as $meta) {
			if ($value = $this->item->getParams()->get("metadata.$meta")) $this->document->setMetadata($meta, $value);
		}

		// get template and params
		if (!$this->template = $this->application->getTemplate()) {
			JError::raiseError(500, JText::_('No template selected'));
			return;
		}
		$this->params   = $this->item->getParams('site');

		// set renderer
		$this->renderer = new ItemRenderer();
		$this->renderer->addPath(array($this->template->getPath(), ZOO_SITE_PATH));
		
		// display view
		$this->getView('item')->addTemplatePath($this->template->getPath())->setLayout('item')->display();
	}
	
    public function submission() {

        // perform the request task
		$controller = $this->application->getController('submission');
		$controller->execute(JRequest::getWord('layout'));
		$controller->redirect();

    }

	public function category() {
		
		// get request vars
		$page        = YRequest::getInt('page', 1);
		$category_id = (int) YRequest::getInt('category_id', $this->params->get('category'));
				
		// init vars
		$this->category   = YTable::getInstance('category')->get($category_id);
		$params	          = $this->category ? $this->category->getParams('site') : $this->application->getParams('frontpage');
		$this->item_order = $this->_getItemOrder($params->get('config.item_order'));
		$layout 		  = $category_id == 0 ? 'frontpage' : 'category';
		$items_per_page   = $params->get('config.items_per_page', 15);
		$offset			  = ($page - 1) * $items_per_page;
		$table			  = YTable::getInstance('item');

		// get categories and items
		$this->categories = $this->application->getCategoryTree(true, $this->user, true);
		$this->items      = $table->getFromCategory($this->application->id, $category_id, true, null, $this->item_order, $offset, $items_per_page);
		$item_count		  = $table->getItemCountFromCategory($this->application->id, $category_id, true);

		// raise warning when category can not be accessed
		if (!isset($this->categories[$category_id])) {
			JError::raiseWarning(500, JText::_('Unable to access category'));
			return;
		}

		// set category and categories to display
		$this->category = $this->categories[$category_id];
		$this->selected_categories = $this->categories[$category_id]->getChildren();

		// get item pagination
		$this->pagination = new YPagination('page', $item_count, $page, $items_per_page);
		$this->pagination->setShowAll($items_per_page == 0);
		if ($layout == 'category') {
			$this->pagination_link = RouteHelper::getCategoryRoute($this->category);
		} else {
			$this->pagination_link = RouteHelper::getFrontpageRoute($this->application->id);
		}

		// create pathway
		$addpath = false;
		$catid   = $this->params->get('category');
		foreach ($this->category->getPathway() as $cat) {
			if (!$catid || $addpath) {
				$link = RouteHelper::getCategoryRoute($cat);
				$this->pathway->addItem($cat->name, $link);
			}
			if ($catid && $catid == $cat->id) {
				$addpath = true;
			}
		}

		// add feed links
		if ($params->get('config.show_feed_link')) {
			if ($alternate = $params->get('config.alternate_feed_link')) {
				$this->document->addHeadLink($alternate, 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
			} else {
				$this->document->addHeadLink(JRoute::_(RouteHelper::getFeedRoute($this->category, 'rss')), 'alternate', 'rel', array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'));
				$this->document->addHeadLink(JRoute::_(RouteHelper::getFeedRoute($this->category, 'atom')), 'alternate', 'rel', array('type' => 'application/atom+xml', 'title' => 'Atom 1.0'));
			}
		}

		// set alphaindex
		if ($params->get('template.show_alpha_index')) {
			$this->alpha_index = $this->_getAlphaindex();
		}

		// set template and params
		if (!$this->template = $this->application->getTemplate()) {
			JError::raiseError(500, JText::_('No template selected'));
			return;
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = new ItemRenderer();
		$this->renderer->addPath(array($this->template->getPath(), ZOO_SITE_PATH));
		
		// display view
		$this->getView($layout)->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
	}

	public function alphaindex() {

		// get request vars
		$page             = YRequest::getInt('page', 1);
		$this->alpha_char = YRequest::getString('alpha_char', '');

		// get params
		$params 	      = $this->application->getParams('site');
		$items_per_page   = $params->get('config.items_per_page', 15);
		$this->item_order = $this->_getItemOrder($params->get('config.item_order'));
		$add_alpha_index  = $params->get('config.alpha_index', 0);

		// get categories
		$this->categories = $add_alpha_index == 1 || $add_alpha_index == 3 ? $this->application->getCategoryTree(true, $this->user, true) : array();

		// set alphaindex
		$this->alpha_index = $this->_getAlphaindex();		
		$this->alpha_char = empty($this->alpha_char) ? $this->alpha_index->getOther() : $this->alpha_index->getChar($this->alpha_char);

		// get items
		$this->items = array();
		if ($add_alpha_index == 2 || $add_alpha_index == 3) {
			$table = YTable::getInstance('item');
			if ($this->alpha_char == $this->alpha_index->getOther()) {
				$this->items = $table->getByCharacter($this->application->id, $this->alpha_index->getIndex(), true, true, null, $this->item_order);
			} else {
				$this->items = $table->getByCharacter($this->application->id, $this->alpha_char, false, true, null, $this->item_order);
			}
		}
		
		// get item pagination
		$this->pagination = new YPagination('page', count($this->items), $page, $items_per_page);
		$this->pagination->setShowAll($items_per_page == 0);
		$this->pagination_link = RouteHelper::getAlphaIndexRoute($this->application->id, $this->alpha_char);

		// slice out items
		if (!$this->pagination->getShowAll()) {
			$this->items = array_slice($this->items, $this->pagination->limitStart(), $items_per_page);
		}

		// set category and categories to display
		if (isset($this->categories[0])) {
			$this->category = $this->categories[0];
		}
		$this->selected_categories = $this->alpha_index->getObjects($this->alpha_char, 'category');

		// create pathway
		$link = JRoute::_(RouteHelper::getAlphaIndexRoute($this->application->id, $this->alpha_char));
		$this->pathway->addItem(JText::_('Alpha Index'), $link);

		// set template and params
		if (!$this->template = $this->application->getTemplate()) {
			JError::raiseError(500, JText::_('No template selected'));
			return;
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = new ItemRenderer();
		$this->renderer->addPath(array($this->template->getPath(), ZOO_SITE_PATH));

		// display view
		$this->getView('alphaindex')->addTemplatePath($this->template->getPath())->setLayout('alphaindex')->display();
	}
	
	public function tag() {

		// get request vars
		$page      = YRequest::getInt('page', 1);
		$this->tag = YRequest::getString('tag', '');

		// get params
		$params 	 	  = $this->application->getParams('site');
		$items_per_page   = $params->get('config.items_per_page', 15);
		$this->item_order = $this->_getItemOrder($params->get('config.item_order'));

		// get categories and items
		$this->categories = $this->application->getCategoryTree(true);
		$this->items = YTable::getInstance('item')->getByTag($this->application->id, $this->tag, true, null, $this->item_order);

		// get item pagination
		$this->pagination = new YPagination('page', count($this->items), $page, $items_per_page);
		$this->pagination->setShowAll($items_per_page == 0);
		$this->pagination_link = RouteHelper::getTagRoute($this->application->id, $this->tag);

		// slice out items
		if (!$this->pagination->getShowAll()) {
			$this->items = array_slice($this->items, $this->pagination->limitStart(), $items_per_page);
		}

		// set alphaindex
		if ($params->get('template.show_alpha_index')) {
			$this->alpha_index = $this->_getAlphaindex();
		}

		// create pathway
		$link = JRoute::_(RouteHelper::getTagRoute($this->application->id, $this->tag));
		$this->pathway->addItem(JText::_('Tags').': '.$this->tag, $link);
		
		// get template and params
		if (!$this->template = $this->application->getTemplate()) {
			JError::raiseError(500, JText::_('No template selected'));
			return;
		}
		$this->params   = $params;

		// set renderer
		$this->renderer = new ItemRenderer();
		$this->renderer->addPath(array($this->template->getPath(), ZOO_SITE_PATH));

		// display view
		$this->getView('tag')->addTemplatePath($this->template->getPath())->setLayout('tag')->display();
	}	
	
	public function feed() {

		// get request vars
		$category_id = (int) YRequest::getInt('category_id', $this->params->get('category'));

		// get params
		$all_categories	= $this->application->getCategoryTree(true);

		// raise warning when category can not be accessed
		if (!isset($all_categories[$category_id])) {
			JError::raiseWarning(500, JText::_('Unable to access category'));
			return;
		}

		$category 		= $all_categories[$category_id];
		$params 	 	= $category ? $category->getParams('site') : $this->application->getParams('frontpage');		
		$show_feed_link = $params->get('config.show_feed_link', 0);
		$feed_title     = $params->get('config.feed_title', '');
				
		// init vars
		$document = JFactory::getDocument();

		// raise error when feed is link is disabled
		if (empty($show_feed_link)) {
			JError::raiseError(500, JText::_('Unable to access feed'));
			return;
		}

		// get feed items from category
		$categories = $category->getChildren(true);
		$categories[$category->id] = $category;

		$feed_limit = $this->joomla->getCfg('feed_limit');

		$feed_items = YTable::getInstance('item')->getFromCategory($this->application->id, array_keys($categories), true, null, 'created DESC', 0, $feed_limit);

		// set title
		if ($feed_title) {
			$document->setTitle(html_entity_decode($this->getView()->escape($feed_title)));
		}

		// set feed link
		$document->link =  JRoute::_($this->link_base.'&task=category');

		// set renderer
		$renderer = new ItemRenderer();
		$renderer->addPath(array($this->application->getTemplate()->getPath(), ZOO_SITE_PATH));

		foreach ($feed_items as $feed_item) {

			// create feed item
			$item         	   = new JFeedItem();
			$item->title  	   = html_entity_decode($this->getView()->escape($feed_item->name));
			$item->link   	   = JRoute::_(RouteHelper::getItemRoute($feed_item));
			$item->date 	   = $feed_item->created;
			$item->author	   = $feed_item->getAuthor();
			$item->description = $this->_relToAbs($renderer->render('item.feed', array('item' => $feed_item)));

			// add to feed document
			$document->addItem($item);
		}
		
	}
	
	protected function _getAlphaindex() {
		
		// set alphaindex
		$alpha_index = new YAlphaindex($this->application->getPath().'/config/alphaindex.xml');

		// add categories
		$add_alpha_index = $this->application->getParams('site')->get('config.alpha_index', 0);
		
		if ($add_alpha_index == 1 || $add_alpha_index == 3) {
			$alpha_index->addObjects(array_filter($this->categories, create_function('$category', 'return $category->id != 0 && $category->totalItemCount();')), 'name');
		}
		// add items
		if ($add_alpha_index == 2 || $add_alpha_index == 3) {

			$db = YDatabase::getInstance();
			
			$access_id = $this->user->get('aid', 0);
			
			// get date
			$date = JFactory::getDate();
			$now  = $db->Quote($date->toMySQL());
			$null = $db->Quote($db->getNullDate());

			$query = 'SELECT DISTINCT BINARY CONVERT(LOWER(LEFT(name, 1)) USING utf8) letter'
					.' FROM ' . ZOO_TABLE_ITEM . ' AS ci'
					.' WHERE id IN (SELECT item_id FROM ' . ZOO_TABLE_CATEGORY_ITEM . ')'
					.' AND application_id = '.(int) $this->application->id
					.' AND access <= '.(int) $access_id
					.' AND state = 1'
					.' AND (publish_up = '.$null.' OR publish_up <= '.$now.')'
					.' AND (publish_down = '.$null.' OR publish_down >= '.$now.')';

			$alpha_index->addObjects($db->queryObjectList($query), 'letter');			
		}
		return $alpha_index;
	}
	
	protected function _getItemOrder($order) {

		$orderings = array(
			'date'   => 'a.created ASC',
			'rdate'  => 'a.created DESC',
			'alpha'  => 'a.name ASC',
			'ralpha' => 'a.name DESC',
			'hits'   => 'a.hits DESC',
			'rhits'  => 'a.hits ASC');
	
		return isset($orderings[$order]) ? $orderings[$order] : '';
	}
	
	protected function _relToAbs($text)	{

		// convert relative to absolute url
		$base = JURI::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)(?!\/)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
		$base = JURI::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
		return $text;
	}	

}

/*
	Class: DefaultControllerException
*/
class DefaultControllerException extends YException {}