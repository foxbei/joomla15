<?php
/**
* @package   ZOO Component
* @file      item.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: ItemController
		Item controller class
*/
class ItemController extends YController {

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

	}
	
	public function element() {

		$this->_loadGeneralCSS();

		jimport('joomla.html.pagination');

		// get database
		$this->db = JFactory::getDBO();

		// get request vars
		$this->filter_item	= YRequest::getInt('item_filter', 0);
		$this->type_filter	= YRequest::getArray('type_filter', array());
		$state_prefix       = $this->option.'_'.$this->application->id.'.'.($this->getTask() == 'element' ? 'element' : 'item').'.';
		$filter_order	    = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', 'a.created', 'cmd');
		$filter_order_Dir   = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$filter_category_id = $this->joomla->getUserStateFromRequest($state_prefix.'filter_category_id', 'filter_category_id', '0', 'string');
		$limit		        = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart			= $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');
		$filter_type     	= $this->joomla->getUserStateFromRequest($state_prefix.'filter_type', 'filter_type', '', 'string');
		$filter_author_id   = $this->joomla->getUserStateFromRequest($state_prefix.'filter_author_id', 'filter_author_id', 0, 'int');
		$search	            = $this->joomla->getUserStateFromRequest($state_prefix.'search', 'search', '', 'string');
		$search			    = JString::strtolower($search);

		// is filtered ?
		$this->is_filtered = $filter_category_id <> '0' || !empty($filter_type) || !empty($filter_author_id) || !empty($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$table        = YTable::getInstance('item');
		$this->users  = $table->getUsers($this->application->id);
		$this->groups = ZooHelper::getGroups();

		// get data from the table
		$where = array();

		// application filter
		$where[] = 'a.application_id = ' . (int) $this->application->id;

		// category filter
		if ($filter_category_id > 0) {
			$where[] = 'ci.category_id = ' . (int) $filter_category_id;
		} else if ($filter_category_id === '') {
            $where[] = 'ci.item_id IS NULL';
        }

		// type filter
		if (!empty($this->type_filter)) {
			$where[] = 'a.type IN ("' . implode('", "', $this->type_filter) . '")';
		} else if (!empty($filter_type)) {
			$where[] = 'a.type = "' . (string) $filter_type . '"';
		}

		// item filter
		if ($this->filter_item > 0) {
			$where[] = 'a.id != ' . (int) $this->filter_item;
		}

		// author filter
		if ($filter_author_id > 0) {
			$where[] = 'a.created_by = ' . (int) $filter_author_id;
		}

		if ($search) {
			$where[] = 'LOWER(a.name) LIKE '.$this->db->Quote('%'.$this->db->getEscaped($search, true).'%', false);
		}

		// access filter
		$where[] = 'a.access <= ' . (int) $this->user->get('aid', 0);

		// state filter
		$where[] = 'a.state = 1';

		$options = array(
			'select' => 'DISTINCT a.*',
			'from' => $table->getTableName().' AS a LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS ci ON a.id = ci.item_id',
			'conditions' => array(implode(' AND ', $where)),
			'order' => $filter_order.' '.$filter_order_Dir);

		$this->items = $table->all($limit > 0 ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
		$this->items = array_merge($this->items);
		$this->pagination = new JPagination($table->count($options), $limitstart, $limit);

		// category select
		$options = array();
        $options[] = JHTML::_('select.option', '0:0', '- ' . JText::_('Select Category') . ' -');
        $options[] = JHTML::_('select.option', '', '- ' . JText::_('uncategorized') . ' -');
		$this->lists['select_category'] = JHTML::_('zoo.categorylist', $this->application, $options, 'filter_category_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_category_id);

		// type select
		$options = array(JHTML::_('select.option', '0', '- '.JText::_('Select Type').' -'));
		$this->lists['select_type'] = JHTML::_('zoo.typelist',  $options, 'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $filter_type, false, false, $this->type_filter);

		// author select
		$options = array(JHTML::_('select.option', '0', '- '.JText::_('Select Author').' -'));
		$this->lists['select_author'] = JHTML::_('zoo.itemauthorlist',  $options, 'filter_author_id', 'class="inputbox auto-submit"', 'value', 'text', $filter_author_id);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']	  = $filter_order;
		$this->lists['search']    = $search;

		$this->addViewPath(ZOO_ADMIN_PATH.'/views/item/');
		$view = $this->getView('', '', '', array('base_path' => ZOO_ADMIN_PATH));

		$view->setLayout('element')->display();

	}

	protected function _loadGeneralCSS() {

		// Load the template name from the database
		$query = 'SELECT template'
			. ' FROM #__templates_menu'
			. ' WHERE client_id = 1'
			. ' AND menuid = 0';
		$template = YDatabase::getInstance()->queryResult($query);

		$template = JFilterInput::clean($template, 'cmd');

		if (!file_exists(JPATH_ROOT.DS.'administrator'.DS.$template.DS.'index.php')) {
			$template = 'khepri';
		}

		JHTML::stylesheet('general.css', 'administrator/templates/'.$template.'/css/');

	}

}
