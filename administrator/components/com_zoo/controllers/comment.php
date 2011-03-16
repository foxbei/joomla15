<?php
/**
* @package   ZOO Component
* @file      comment.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CommentController
		The controller class for comments
*/
class CommentController extends YController {

	public function display() {

		jimport('joomla.html.pagination');

		// get application
		$app = Zoo::getApplication();

		// get request vars
		$db            = YDatabase::getInstance();
		$state_prefix  = $this->option.'_'.$app->id.'.comment.';			
		$limit		   = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart    = $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0, 'int');
		$filter_state  = $this->joomla->getUserStateFromRequest($state_prefix.'filter-state', 'filter-state', '', 'string');
		$filter_item   = $this->joomla->getUserStateFromRequest($state_prefix.'filter-item', 'filter-item', 0, 'int');
		$filter_author = $this->joomla->getUserStateFromRequest($state_prefix.'filter-author', 'filter-author', '', 'string');
		$search	       = $this->joomla->getUserStateFromRequest($state_prefix.'search', 'search', '', 'string');
		$search		   = JString::strtolower($search);

		// is filtered ?
		$this->is_filtered = $filter_state <> '' || !empty($filter_item) || !empty($filter_author) || !empty($search);		
		
		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0;

		// set toolbar items
		if ($filter_item && $item_object = YTable::getInstance('item')->get($filter_item)) {
			$this->joomla->set('JComponentTitle', $app->getToolbarTitle(JText::_('Comments on') . ': ' . $item_object->name));	
		} else {
			$this->joomla->set('JComponentTitle', $app->getToolbarTitle(JText::_('Comments')));	
		}			
		JToolBarHelper::custom('approve', 'publish', '', 'Approve');			
		JToolBarHelper::custom('unapprove', 'unpublish', '', 'Unapprove');			
		JToolBarHelper::custom('spam', 'trash', '', 'Spam');			
		JToolBarHelper::deleteList();		
				
		// build where condition
		$where = array('b.application_id = '.(int) $app->id);

		if ($filter_state === '') {
			$where[] = 'a.state <> 2'; // all except spam
		} else {
			$where[] = 'a.state = '.(int) $filter_state;
		}
		
		if ($filter_item) {
			$where[] = 'a.item_id = '.(int) $filter_item;
		}
		
		if ($filter_author == '_anonymous_') {
			$where[] = 'a.author = ""';
		} elseif ($filter_author) {
			$where[] = 'a.author = "'.$db->getEscaped($filter_author).'"';
		}

		if ($search) {
			$where[] = 'LOWER(a.content) LIKE "%'.$db->getEscaped($search, true).'%"';
		}

		// build query options
		$options = array(
			'select'     => 'a.*',
			'from'       => ZOO_TABLE_COMMENT.' AS a LEFT JOIN '.ZOO_TABLE_ITEM.' AS b ON a.item_id = b.id',
			'conditions' => array(implode(' AND ', $where)),
			'order'      => 'created DESC');

		// query comment table
		$table = YTable::getInstance('comment');
		$count = $table->count($options);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;
		$this->comments = $table->all($limit > 0 ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
		$this->pagination = new JPagination($count, $limitstart, $limit);
		
		// search filter
		$this->lists['search'] = $search;

		// state select
		$options = array(
			JHTML::_('select.option', '', '- '.JText::_('Select Status').' -'),
			JHTML::_('select.option', '0', JText::_('Pending')),
			JHTML::_('select.option', '1', JText::_('Approved')),
			JHTML::_('select.option', '2', JText::_('Spam')));
		$this->lists['select_state'] = JHTML::_('select.genericlist', $options, 'filter-state', 'class="inputbox auto-submit"', 'value', 'text', $filter_state);
		
		// item select
		$options = array(JHTML::_('select.option', 0, '- '.JText::_('Select Item').' -'));
		$this->lists['select_item'] = JHTML::_('zoo.itemlist', $app, $options, 'filter-item', 'class="inputbox auto-submit"', 'value', 'text', $filter_item);

		// author select
		$options = array(
			JHTML::_('select.option', '', '- '.JText::_('Select Author').' -'),
			JHTML::_('select.option', '_anonymous_', '- '.JText::_('Anonymous').' -'));
		$this->lists['select_author'] = JHTML::_('zoo.commentauthorlist', $app, $options, 'filter-author', 'class="inputbox auto-submit"', 'value', 'text', $filter_author);		

		// get comment params
		$this->params = new YParameter();
		$this->params->loadArray($app->getParams()->get('global.comments.', array()));
		
		// display view
		$this->getView()->display();
	}

	public function edit() {

		// get request vars
		$cid = YRequest::getInt('cid');

		// get comment
		$this->comment = YTable::getInstance('comment')->get($cid);
		
		// display view
		$this->getView()->setLayout('_edit')->display();
	}

	public function reply() {
	
		// get request vars
		$this->cid = YRequest::getInt('cid');
		
		// display view
		$this->getView()->setLayout('_reply')->display();
	}
	
	public function save() {
		
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$post = YRequest::get('post');
		$cid  = YRequest::getArray('cid.0', '', 'int');
		$pid  = YRequest::getInt('parent_id', 0);
		$now  = JFactory::getDate();
		
		try {

			// get content as raw and filter it
			$post['content'] = YRequest::getVar('content', null, '', 'string', JREQUEST_ALLOWRAW);
			$post['content'] = CommentHelper::filterContentInput($post['content']);
						
			// get comment table
			$table = YTable::getInstance('comment');		
											
			// get comment or create reply
			if ($cid) {
				$comment = $table->get($cid);
			} else {
				$parent  = $table->get($pid);
				$comment = new Comment();
				$comment->item_id = $parent->getItem()->id;
				$comment->user_id = $this->user->id;
				$comment->author = $this->user->name;
				$comment->email = $this->user->email;
				$comment->ip = CommentHelper::getClientIP();
				$comment->created = $now->toMySQL();
				$comment->state = Comment::STATE_APPROVED;
			}

			// bind post data
			$comment->bind($post);

			// save comment		
			$table->save($comment);

			// get view
			$view = $this->getView();

			// set view vars
			$view->option = $this->option;
			$view->comment = $comment;

			// display view
			$view->setLayout('_row');
			$view->display();		

		} catch (YException $e) {

			// raise error on exception
			echo json_encode(array(
				'group' => 'error',
				'title' => JText::_('Error Saving Comment'),
				'text'  => (string) $e));
		}

	}

	/*
		Function: remove
			reaction on clicking the remove botton.
			the selected list of comments will be removed from the database.

		Returns:
			Void
	*/	
	public function remove() {
	
		// check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$msg = null;
		$cid = YRequest::getArray('cid', '', 'int');
		
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a comment to delete'));
		}

		try {
			
			$table = YTable::getInstance('comment');
			
			// delete comments
			foreach ($cid as $id) {
				$table->delete($table->get($id));
			}
					
			// set redirect message
			$msg = JText::_('Comment(s) Deleted');
			

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseWarning(0, JText::_('Error Deleting Comment(s)').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	/*
		Function: approve
			Approve a comment
								
		Returns:
			Void
	*/	
	public function approve() {
		$this->_editState(1);
	}

	/*
		Function: unapprove
			Unapprove a comment
								
		Returns:
			Void
	*/	
	public function unapprove() {
		$this->_editState(0);
	}

	/*
		Function: spam
			Mark comment as spam
								
		Returns:
			Void
	*/	
	public function spam() {
		$this->_editState(2);
	}

	protected function _editState($state) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a comment to edit state'));
		}
		
		try {

			// get comment table
			$table = YTable::getInstance('comment');

			// update comment state
			foreach ($cid as $id) {
				$comment = $table->get($id);
				$comment->state = $state;
				$table->save($comment);	
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error editing Comment State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

}

/*
	Class: CommentControllerException
*/
class CommentControllerException extends YException {}