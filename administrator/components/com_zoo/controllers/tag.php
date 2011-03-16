<?php
/**
* @package   ZOO Component
* @file      tag.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: TagController
		The controller class for tag
*/
class TagController extends YController {

	public function display() {

		jimport('joomla.html.pagination');

		// get application
		$app = Zoo::getApplication();

		// set toolbar items
		$this->joomla->set('JComponentTitle', $app->getToolbarTitle(JText::_('Tags')));		
		JToolBarHelper::deleteList();
		ZooHelper::toolbarHelp();

		JHTML::_('behavior.tooltip');

		// get request vars
		$state_prefix     = $this->option.'_'.$app->id.'.tags.';		
		$filter_order	  = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$limit		      = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart		  = $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');
		$search	          = $this->joomla->getUserStateFromRequest($state_prefix.'search', 'search', '', 'string');
		$search			  = JString::strtolower($search);
		
		// is filtered ?
		$this->is_filtered = !empty($search);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		// get data
		$filter     = ($filter_order) ? $filter_order . ' ' . $filter_order_Dir : '';

		$count = (int) YTable::getInstance('tag')->count($app->id, $search);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		$this->tags = YTable::getInstance('tag')->getAll($app->id, $search, '', $filter, $limitstart, $limit);

		$this->pagination = new JPagination($count, $limitstart, $limit);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']     = $filter_order;		
		$this->lists['search']    = $search;		

		// display view
		$this->getView()->display();
	}

	public function remove() {

		// init vars
		$tags = YRequest::getArray('cid', array(), 'string');

		if (count($tags) < 1) {
			JError::raiseError(500, JText::_('Select a tag to delete'));
		}
		
		try {		
			
			$app = Zoo::getApplication();
			
			// delete tags
			YTable::getInstance('tag')->delete($app->id, $tags);

			// set redirect message
			$msg = JText::_('Tag Deleted');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseWarning(0, JText::_('Error Deleting Tag').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}	
	
	public function update() {
	
		// init vars
		$old = YRequest::getString('old');
		$new = YRequest::getString('new');
		$msg = null;
		
		try {		

			$app = Zoo::getApplication();
			
			// update tag
			if (!empty($new) && $old != $new) {
				YTable::getInstance('tag')->update($app->id, $old, $new);

				// set redirect message
				$msg = JText::_('Tag Updated Successfully');
			}
			
		} catch (YException $e) {

			// raise notice on exception
			JError::raiseWarning(0, JText::_('Error Updating Tag').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl, $msg);
	}		
	
}

/*
	Class: TagControllerException
*/
class TagControllerException extends YException {}