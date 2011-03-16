<?php
/**
* @package   ZOO Component
* @file      item.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ItemController
		The controller class for item
*/
class ItemController extends YController {

	public $application;
	
	const MAX_MOST_USED_TAGS = 8;
	
	public function __construct($default = array()) {
		parent::__construct($default);
		
		// get application
		$this->application 	= Zoo::getApplication();

		// register tasks
		$this->registerTask('element', 'display');
		$this->registerTask('apply', 'save');
		$this->registerTask('saveandnew', 'save' );
	}

	public function display() {

		jimport('joomla.html.pagination');

		// get app from Request (currently used in zooapplication element)
		if ($id = YRequest::getInt('app_id')) {
			$this->application = YTable::getInstance('application')->get($id);
		}

		// get database
		$this->db = JFactory::getDBO();

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Items')));
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom('docopy', 'copy.png', 'copy_f2.png', 'Copy');
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		ZooHelper::toolbarHelp();

		JHTML::_('behavior.tooltip');

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

		// select
		$select = 'a.*';

		// get from
		$from = $table->getTableName().' AS a';

		// get data from the table
		$where = array();

		// application filter
		$where[] = 'a.application_id = ' . (int) $this->application->id;

		// category filter
		if ($filter_category_id > 0) {
			$select  = 'DISTINCT a.*';
			$from   .= ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS ci ON a.id = ci.item_id';
			$where[] = 'ci.category_id = ' . (int) $filter_category_id;
		} else if ($filter_category_id === '') {
			$from   .= ' LEFT JOIN '.ZOO_TABLE_CATEGORY_ITEM.' AS ci ON a.id = ci.item_id';
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

		$options = array(
			'select' => $select,
			'from' =>  $from,
			'conditions' => array(implode(' AND ', $where)),
			'order' => $filter_order.' '.$filter_order_Dir);

		$count = $table->count($options);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		$this->items = $table->all($limit > 0 ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
		$this->items = array_merge($this->items);

		$this->pagination = new JPagination($count, $limitstart, $limit);

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

		// display view
		$layout = $this->getTask() == 'element' ? 'element' : 'default';
		$this->getView()->setLayout($layout)->display();
	}
	
	public function loadtags() {

		// get request vars
		$tag = YRequest::getString('tag', '');

		$tags = array();
		if (!empty($tag)) {
			// get tags
			$tag_objects = YTable::getInstance('tag')->getAll($this->application->id, $tag, '', 'a.name asc');
			
			foreach($tag_objects as $tag) {
				$tags[] = $tag->name;
			}
		}

		echo json_encode($tags);
	}	
	
	public function add() {

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Item') .': <small><small>[ '.JText::_('New').' ]</small></small>'));
		JToolBarHelper::cancel();
		
		// get types
		$this->types = $this->application->getTypes();
		
		// no types available ?
		if (count($this->types) == 0) {
			JError::raiseNotice(0, JText::_('Please create a type first.'));
			$this->joomla->redirect($this->link_base.'&controller=manager');
		}
		
		// only one type ? then skip type selection
		if (count($this->types) == 1) {
			$type = array_shift($this->types);
			$this->joomla->redirect($this->baseurl.'&task=edit&type='.$type->id);
		}

		// display view
		$this->getView()->setLayout('add')->display();
	}

	public function edit($tpl = null) {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);

		// get database
		$this->db = JFactory::getDBO();

		// get request vars
		$cid  = YRequest::getArray('cid.0', '', 'int');
		$edit = $cid > 0;

		// get item
		if ($edit) {
			$this->item = YTable::getInstance('item')->get($cid);
		} else {
			$this->item = new Item();
			$this->item->application_id = $this->application->id;
			$this->item->type = JRequest::getVar('type');
			$this->item->publish_down = $this->db->getNullDate();
		}

		// get item params
		$this->params = $this->item->getParams();

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Item').': '.$this->item->name.' <small><small>[ '.($edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>'));
		JToolBarHelper::save();
		JToolBarHelper::custom('saveandnew', 'saveandnew', 'saveandnew', 'Save & New', false);
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel', $edit ? 'Close' : 'Cancel');
		ZooHelper::toolbarHelp();

		// published select
		$this->lists['select_published'] = JHTML::_('select.booleanlist', 'state', null, $this->item->state);
		
		// published searchable
		$this->lists['select_searchable'] = JHTML::_('select.booleanlist', 'searchable', null, $this->item->searchable);		

		// categories select
		$related_categories = $this->item->getRelatedCategoryIds();
		$this->lists['select_frontpage']  = JHTML::_('select.booleanlist', 'frontpage', null, in_array(0, $related_categories));	
		$this->lists['select_categories'] = JHTML::_('zoo.categorylist', $this->application, array(), 'categories[]', 'size="15" multiple="multiple"', 'value', 'text', $related_categories);
		$this->lists['select_primary_category'] = JHTML::_('zoo.categorylist', $this->application, array(JHTML::_('select.option', '', JText::_('none'))), 'params[primary_category]', '', 'value', 'text', $this->params->get('config.primary_category'));

		// most used tags
		$this->lists['most_used_tags'] = YTable::getInstance('tag')->getAll($this->application->id, null, null, 'items DESC, a.name ASC', null, self::MAX_MOST_USED_TAGS);
		
		// comments enabled select
		$this->lists['select_enable_comments'] = JHTML::_('select.booleanlist', 'params[enable_comments]', null, $this->params->get('config.enable_comments', 1));		
		
		// display view
		$this->getView()->setLayout('edit')->display();
	}	

	public function save() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		// init vars
		$db         = JFactory::getDBO();
		$config     = JFactory::getConfig();
		$now        = JFactory::getDate();
		$post       = YRequest::get('post');
		$frontpage  = YRequest::getBool('frontpage', false);
		$categories	= YRequest::getArray('categories', null);
		$details	= YRequest::getArray('details', null);
		$metadata   = YRequest::getArray('meta', null);
		$cid        = YRequest::getArray('cid.0', '', 'int');		
		$tzoffset   = $config->getValue('config.offset');
		$post       = array_merge($post, $details);
				
		try {

			// get item table
			$table = YTable::getInstance('item');

			// get item
			if ($cid) {
				$item = $table->get($cid);
			} else {
				$item = new Item();
				$item->application_id = $this->application->id;
				$item->type = YRequest::getVar('type');
			}
			
			// bind item data
			$item->bind($post, array('elements', 'params', 'created_by'));
            $created_by = isset($post['created_by']) ? $post['created_by'] : '';
            $item->created_by = empty($created_by) ? JFactory::getUser()->id : $created_by == 'NO_CHANGE' ? $item->created_by : $created_by;
			$tags = isset($post['tags']) ? $post['tags'] : array();
			$item->setTags($tags);

			// bind element data
			foreach ($item->getElements() as $id => $element) {
				if (isset($post['elements'][$id])) {
					$element->bindData($post['elements'][$id]);
				} else {
					$element->bindData();
				}
			}

			// set alias
			$item->alias = ItemHelper::getUniqueAlias($item->id, YString::sluggify($item->alias));

			// set modified
			$item->modified	   = $now->toMySQL();
			$item->modified_by = $this->user->get('id');

			// set created date
			if ($item->created && strlen(trim($item->created)) <= 10) {
				$item->created .= ' 00:00:00';
			}
			$date = JFactory::getDate($item->created, $tzoffset);
			$item->created = $date->toMySQL();
	
			// set publish up date
			if (strlen(trim($item->publish_up)) <= 10) {
				$item->publish_up .= ' 00:00:00';
			}
			$date = JFactory::getDate($item->publish_up, $tzoffset);
			$item->publish_up = $date->toMySQL();
	
			// set publish down date
			if (trim($item->publish_down) == JText::_('Never') || trim($item->publish_down) == '') {
				$item->publish_down = $db->getNullDate();
			} else {
				if (strlen(trim($item->publish_down)) <= 10) {
					$item->publish_down .= ' 00:00:00';
				}
				$date = JFactory::getDate($item->publish_down, $tzoffset);
				$item->publish_down = $date->toMySQL();
			}

			// get primary category
			$primary_category = @$post['params']['primary_category'];
			if (empty($primary_category) && count($categories)) {
				$primary_category = $categories[0];
			}

			// set params
			$item->params = $item
				->getParams()
				->remove('metadata.')
				->remove('template.')
				->set('metadata.', @$post['params']['metadata'])
				->set('template.', @$post['params']['template'])
				->set('config.enable_comments', @$post['params']['enable_comments'])
				->set('config.primary_category', $primary_category)
				->toString();			
	
			// save item		
			$table->save($item);

			// make sure categories contain primary category
			if (!empty($primary_category) && !in_array($primary_category, $categories)) {
				$categories[] = $primary_category;
			}

			// save category relations
			if ($frontpage) {
				$categories[] = 0;
			}
			CategoryHelper::saveCategoryItemRelations($item->id, $categories);

			// set redirect message
			$msg = JText::_('Item Saved');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Item').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}
		
		$link = $this->baseurl;
		switch ($this->getTask()) {
			case 'apply' :
				$link .= '&task=edit&type='.$item->type.'&cid[]='.$item->id;
				break;
			case 'saveandnew' :
				$link .= '&task=add';
				break;
		}		

		$this->setRedirect($link, $msg);
	}

	public function docopy() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$now  = JFactory::getDate();
		$post = YRequest::get('post');
		$cid  = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to copy'));
		}

		try {

			// get item table
			$item_table = YTable::getInstance('item');
			$tag_table  = YTable::getInstance('tag');
			
			// get database
			$db = YDatabase::getInstance();
			// copy items
			foreach ($cid as $id) {
				
				// get item
				$item       = $item_table->get($id);
				$elements   = $item->getElements();
				$categories = $item->getRelatedCategoryIds();
				
				// copy item
				$item->id          = 0;                         						// set id to 0, to force new item
				$item->name       .= ' ('.JText::_('Copy').')'; 						// set copied name
				$item->alias       = ItemHelper::getUniqueAlias($id, $item->alias.'-copy'); 	// set copied alias	
				$item->state       = 0;                         						// unpublish item
				$item->created	   = $now->toMySQL();
				$item->created_by  = $this->user->get('id');
				$item->modified	   = $now->toMySQL();
				$item->modified_by = $this->user->get('id');
				$item->hits		   = 0;

				// copy tags
				$item->setTags($tag_table->getItemTags($id));
				
				// save copied item/element data
				$item_table->save($item);
				
				// save category relations
				CategoryHelper::saveCategoryItemRelations($item->id, $categories);
			}

			// set redirect message
			$msg = JText::_('Item Copied');

		} catch (YException $e){

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Copying Item').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function remove() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to delete'));
		}
		
		try {		

			// get item table
			$table = YTable::getInstance('item');

			// delete items
			foreach ($cid as $id) {
				$table->delete($table->get($id));
			}

			// set redirect message
			$msg = JText::_('Item Deleted');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseWarning(0, JText::_('Error Deleting Item').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function savepriority() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$msg      = JText::_('Order Priority saved');
		$priority = YRequest::getArray('priority', array(), 'int');

		try {

			// get item table
			$table = YTable::getInstance('item');

			// update the priority for items
			foreach ($priority as $id => $value) {
				$item = $table->get((int) $id);

				// only update, if changed
				if ($item->priority != $value) {
					$item->priority = $value;
					$table->save($item);
				}
			}

			// set redirect message
			$msg = json_encode(array(
				'group' => 'info',
				'title' => JText::_('Success!'),
				'text'  => JText::_('Item Priorities Saved')));

		} catch (YException $e) {

			// raise error on exception
			$msg = json_encode(array(
				'group' => 'error',
				'title' => JText::_('Error!'),
				'text'  => JText::_('Error editing item priority').' ('.$e.')'));

		}

		echo $msg;
	}
	
	public function resethits() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$msg = null;
		$cid = YRequest::getArray('cid.0', '', 'int');

		try {
			
			// get item table
			$table = YTable::getInstance('item');
						
			// get item
			$item = $table->get($cid);
	
			// reset hits
			if ($item->hits > 0) {
				$item->hits = 0;
				
				// save item
				$table->save($item);

				// set redirect message
				$msg = JText::_('Item Hits Reseted');
			}			

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Reseting Item Hits').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl.'&task=edit&cid[]='.$item->id, $msg);
	}

	public function publish() {
		$this->_editState(1);
	}

	public function unpublish() {
		$this->_editState(0);
	}
	
	public function accessPublic() {
		$this->_editAccess(0);
	}

	public function accessRegistered() {
		$this->_editAccess(1);
	}

	public function accessSpecial() {
		$this->_editAccess(2);
	}

	public function makeSearchable() {
		$this->_editSearchable(1);
	}

	public function makeNoneSearchable() {
		$this->_editSearchable(0);
	}
	
	public function enableComments() {
		$this->_editComments(1);
	}

	public function disableComments() {
		$this->_editComments(0);
	}		

	protected function _editState($state) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to edit publish state'));
		}
		
		try {

			// get item table
			$table = YTable::getInstance('item');

			// update item state
			foreach ($cid as $id) {
				$item = $table->get($id);
				$item->state = $state;
				$table->save($item);	
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error editing Item Published State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	protected function _editAccess($access) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to edit access'));
		}
		
		try {

			// get item table
			$table = YTable::getInstance('item');
			
			// update item access
			foreach ($cid as $id) {
				$item = $table->get($id);
				$item->access = $access;
				$table->save($item);
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Editing Item Access').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	protected function _editSearchable($searchable) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to edit searchable state'));
		}
		
		try {

			// get item table
			$table = YTable::getInstance('item');

			// update item state
			foreach ($cid as $id) {
				$item = $table->get($id);
				$item->searchable = $searchable;
				$table->save($item);	
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error editing Item Searchable State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}
	
	protected function _editComments($enabled) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a item to enable/disable comments'));
		}
		
		try {

			// get item table
			$table = YTable::getInstance('item');

			// update item state
			foreach ($cid as $id) {
				$item = $table->get($id);

				$item->params = $item
					->getParams()
					->set('config.enable_comments', $enabled)
					->toString();

				$table->save($item);	
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error enabling/disabling Item Comments').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}	
	
	public function callElement() {
		
		// get request vars		
		$element_identifier = YRequest::getString('elm_id', '');
		$item_id			= YRequest::getInt('item_id', 0);
		$type	 			= YRequest::getString('type', '');
		$this->method 		= YRequest::getCmd('method', '');
		$this->args       	= YRequest::getVar('args', array(), 'default', 'array');
		
		JArrayHelper::toString($this->args);
						
		// load element
		if ($item_id) {
			$item = YTable::getInstance('item')->get($item_id);
		} elseif (!empty($type)){
			$item = new Item();
			$item->application_id = $this->application->id;
			$item->type = $type;
		}
		
		// execute callback method
		if ($element = $item->getElement($element_identifier)) {
			echo $element->callback($this->method, $this->args);
		}		
		
	}		
	
}

/*
	Class: ItemControllerException
*/
class ItemControllerException extends YException {}