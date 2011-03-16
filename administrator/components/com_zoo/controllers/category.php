<?php
/**
* @package   ZOO Component
* @file      category.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CategoryController
		The controller class for category
*/
class CategoryController extends YController {

	public $application;	
	
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application = Zoo::getApplication();			
		
		// registers tasks
		$this->registerTask('apply', 'save');
		$this->registerTask('saveandnew', 'save' );
		$this->registerTask('add', 'edit');
	}

	public function display() {

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Categories')));
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::custom('docopy', 'copy.png', 'copy_f2.png', 'Copy', false);		
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		ZooHelper::toolbarHelp();

		JHTML::_('behavior.tooltip');

		// get data
		$this->categories = $this->application->getCategoryTree(false, null, true);
		
		// display view
		$this->getView()->display();
	}
	
	public function edit() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);

		// get request vars
		$cid  = YRequest::getArray('cid.0', '', 'int');
		$edit = $cid > 0;

		// get category
		if ($edit) {
			$this->category = YTable::getInstance('category')->get($cid);
		} else {
			$this->category = new Category();
			$this->category->parent = 0;
		}

		// get category params
		$this->params = $this->category->getParams();

		// set toolbar items
		$text = $edit ? JText::_('Edit') : JText::_('New');
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Category').': '.$this->category->name.' <small><small>[ '.$text.' ]</small></small>'));
		JToolBarHelper::save();
		JToolBarHelper::custom('saveandnew', 'saveandnew', 'saveandnew', 'Save & New', false);
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel', $edit ? 'Close' : 'Cancel');
		ZooHelper::toolbarHelp();

		// select published state
		$this->lists['select_published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->category->published);

		// get categories and exclude the current category
		$categories = $this->application->getCategories();
		unset($categories[$this->category->id]);

		// build category tree
		$list = CategoryHelper::buildTreeList(0, CategoryHelper::buildTree($this->application->id, $categories));

		$options = array(JHTML::_('select.option', '0', JText::_('Root')));
		foreach ($list as $item) {
			$options[] = JHTML::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename);
		}

		// select parent category
		$this->lists['select_parent'] = JHTML::_('select.genericlist', $options, 'parent', 'class="inputbox" size="10"', 'value', 'text', $this->category->parent);

		// display view
		$this->getView()->setLayout('edit')->display();
	}

	public function save() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
			
		// init vars
		$post = YRequest::get('post');
		$cid  = YRequest::getArray('cid.0', '', 'int');

		// set application
		$post['application_id'] = $this->application->id;

		// get raw description from post data
		$post['description'] = YRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);

		try {

			// get category table
			$table = YTable::getInstance('category');			
			
			// get category and bind post data
			$category = ($cid) ? $table->get($cid) : new Category();
			$category->bind($post, array('params'));
			$category->alias = CategoryHelper::getUniqueAlias($category->id, YString::sluggify($category->alias));
			$category->params = $category
				->getParams()
				->remove('content.')
				->remove('config.')
				->remove('template.')
				->set('content.', @$post['params']['content'])
				->set('config.', @$post['params']['config'])
				->set('template.', @$post['params']['template'])
				->toString();

			// save category and update category ordering
			$table->save($category);
			$table->updateorder($this->application->id, $category->parent);
			
			// set redirect message
			$msg = JText::_('Category Saved');			
			
		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Category').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}
		
		$link = $this->baseurl;
		switch ($this->getTask()) {
			case 'apply' :
				$link .= '&task=edit&cid[]='.$category->id;
				break;
			case 'saveandnew' :
				$link .= '&task=edit&cid[]=';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function remove() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a Category to delete'));
		}

		try {

			// get category table
			$table = YTable::getInstance('category');
			
			// delete categories
			$parents = array();
			
			foreach ($cid as $id) {
				$category  = $table->get($id);
				$parents[] = $category->parent;
				$table->delete($category);
			}
	
			// update category ordering
			$table->updateorder($this->application->id, $parents);
		
			// set redirect message		
			$msg = JText::_('Category Deleted');
		
		} catch (YException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Deleting Category').' ('.$e.')');
			$msg = null;

		}		

		$this->setRedirect($this->baseurl, $msg);
	}

	public function docopy() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a category to copy'));
		}
		
		try {

			// get category table
			$table = YTable::getInstance('category');		
			
			// copy categories
			$parents = array();
			foreach ($cid as $id) {
				
				// get category
				$category = $table->get($id);
				
				// copy category
				$category->id         = 0;                         // set id to 0, to force new category
				$category->name      .= ' ('.JText::_('Copy').')'; // set copied name
				$category->alias      = CategoryHelper::getUniqueAlias($id, $category->alias.'-copy'); // set copied alias			
				$category->published  = 0;                         // unpublish category
	
				// track parent for ordering update
				$parents[] = $category->parent;
	
				// save copied category data
				$table->save($category);
			}
	
			// update category ordering
			$table->updateorder($this->application->id, $parents);

			// set redirect message	
			$msg = JText::_('Category Copied');
		
		} catch (YException $e) {
			
			// raise notice on exception			
			JError::raiseNotice(0, JText::_('Error Copying Category').' ('.$e.')');
			$msg = null;

		}			

		$this->setRedirect($this->baseurl, $msg);
	}

	public function saveorder() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		// group categories by parent
		$category_ordering = array();
		foreach (YRequest::getArray('category', array(), 'int') as $id => $parent) {
			$category_ordering[$parent][] = $id;
		}

		try {

			// get categories
			$table = YTable::getInstance('category');
			$categories = $table->all();

			// update category parent & ordering
			foreach ($category_ordering as $parent => $cat_ids) {
				$parent = $parent == 'root' ? 0 : $parent;
				foreach ($cat_ids as $ordering => $id) {
					// only update, if changed
					if (isset($categories[$id]) && ($categories[$id]->parent != $parent || $categories[$id]->ordering != $ordering)) {
						$categories[$id]->parent = $parent;
						$categories[$id]->ordering = $ordering;
						$table->save($categories[$id]);
					}
				}
			}

			// set redirect message
			$msg = json_encode(array(
				'group' => 'info',
				'title' => JText::_('Success!'),
				'text'  => JText::_('New ordering saved')));

		} catch (YException $e) {

			// raise error on exception
			$msg = json_encode(array(
				'group' => 'error',
				'title' => JText::_('Error!'),
				'text'  => JText::_('Error Reordering Category').' ('.$e.')'));

		}

		echo $msg;
	}

	public function publish() {
		$this->_editPublished(1, JText::_('Select a category to publish'));
	}

	public function unpublish() {
		$this->_editPublished(0, JText::_('Select a category to unpublish'));
	}

	public function _editPublished($published, $msg) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid    = YRequest::getArray('cid', array(), 'int');
		
		if (count($cid) < 1) {
			JError::raiseError(500, $msg);
		}

		try {

			// get category table
			$table = YTable::getInstance('category');

			// update published state
			foreach ($cid as $id) {
				$category = $table->get($id);
				$category->setPublished($published);
				$table->save($category);
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error editing Item Published State').' ('.$e.')');
			$msg = null;

		}	

		$this->setRedirect($this->baseurl);
	}

}

/*
	Class: CategoryControllerException
*/
class CategoryControllerException extends YException {}