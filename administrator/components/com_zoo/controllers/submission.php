<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: SubmissionController
		The controller class for submission
*/
class SubmissionController extends YController {

	public $application;

	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application 	= Zoo::getApplication();

		// register tasks
        $this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('saveandnew', 'save' );
	}

	public function display() {

		jimport('joomla.html.pagination');

		// get database
		$this->db = JFactory::getDBO();

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Items')));
		JToolBarHelper::custom('docopy', 'copy.png', 'copy_f2.png', 'Copy');
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		ZooHelper::toolbarHelp();

		JHTML::_('behavior.tooltip');

		$state_prefix       = $this->option.'_'.$this->application->id.'.submission';
		$filter_order	    = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order', 'filter_order', 'name', 'cmd');
		$filter_order_Dir   = $this->joomla->getUserStateFromRequest($state_prefix.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

        $table = YTable::getInstance('submission');
		$this->groups = ZooHelper::getGroups();
        
        // get data from the table
		$where = array();

		// application filter
		$where[] = 'application_id = ' . (int) $this->application->id;

		$options = array(
			'conditions' => array(implode(' AND ', $where)),
			'order' => $filter_order.' '.$filter_order_Dir);

		$this->submissions = $table->all($options);
        $this->submissions = array_merge($this->submissions);

		// table ordering and search filter
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']	  = $filter_order;

		// display view
		$this->getView()->display();
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
			$this->submission = YTable::getInstance('submission')->get($cid);
		} else {
			$this->submission = new Submission();
			$this->submission->application_id = $this->application->id;
            $this->submission->access = 1;
		}

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Submission').': '.$this->submission->name.' <small><small>[ '.($edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>'));
		JToolBarHelper::save();
		JToolBarHelper::custom('saveandnew', 'saveandnew', 'saveandnew', 'Save & New', false);
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel', $edit ? 'Close' : 'Cancel');
		ZooHelper::toolbarHelp();

        // published select
		$this->lists['select_published'] = JHTML::_('select.booleanlist', 'state', null, $this->submission->state);

        // tooltip select
		$this->lists['select_tooltip'] = JHTML::_('select.booleanlist', 'params[show_tooltip]', null, $this->submission->showTooltip());

        // type select
        $this->types = array();
        foreach ($this->application->getTypes() as $type) {

            // list types with submission layouts only
            if (count(ZooHelper::getLayouts($this->application, $type->id, 'submission')) > 0){

                $form = $this->submission->getForm($type->id);

                $this->types[$type->id]['name'] = $type->name;

                $options = array(JHTML::_('select.option', '', '- '.JText::_('not submittable').' -'));
                $this->types[$type->id]['select_layouts'] = JHTML::_('zoo.layoutList', $this->application, $type->id, 'submission', $options, 'params[form]['.$type->id.'][layout]', '', 'value', 'text', $form->get('layout'));

                $options = array(JHTML::_('select.option', '', '- '.JText::_('uncategorized').' -'));
                $this->types[$type->id]['select_categories'] = JHTML::_('zoo.categorylist', $this->application, $options, 'params[form]['.$type->id.'][category]', 'size="1"', 'value', 'text', $form->get('category'));

            }
        }

        // display view
		$this->getView()->setLayout('edit')->display();
	}

	public function save() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$post       = YRequest::get('post');
		$cid        = YRequest::getArray('cid.0', '', 'int');

		try {

			// get item table
			$table = YTable::getInstance('submission');

			// get item
			if ($cid) {
				$submission = $table->get($cid);
			} else {
				$submission = new Submission();
				$submission->application_id = $this->application->id;
			}

			// bind submission data
			$submission->bind($post, array('params'));
            
            // generate unique slug
            $submission->alias = SubmissionHelper::getUniqueAlias($submission->id, YString::sluggify($submission->alias));

			// set params
			$submission->params = $submission
                ->getParams()
                ->clear()
                ->set('form.', @$post['params']['form'])
                ->set('trusted_mode', @$post['params']['trusted_mode'])
				->set('show_tooltip', @$post['params']['show_tooltip'])
                ->toString();

			// save submission
			$table->save($submission);

			// set redirect message
			$msg = JText::_('Submission Saved');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Submission').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}

		$link = $this->baseurl;
		switch ($this->getTask()) {
			case 'apply' :
				$link .= '&task=edit&cid[]='.$submission->id;
				break;
			case 'saveandnew' :
				$link .= '&task=add';
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
			JError::raiseError(500, JText::_('Select a submission to delete'));
		}

		try {

			// get item table
			$table = YTable::getInstance('submission');

			// delete items
			foreach ($cid as $id) {
				$table->delete($table->get($id));
			}

			// set redirect message
			$msg = JText::_('Submission Deleted');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseWarning(0, JText::_('Error Deleting Submission').' ('.$e.')');
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
			JError::raiseError(500, JText::_('Select a submission to copy'));
		}

		try {

			// get category table
			$table = YTable::getInstance('submission');

			// copy submissions
			foreach ($cid as $id) {

				// get submission
				$submission = $table->get($id);

				// copy submission
				$submission->id         = 0;                         // set id to 0, to force new category
				$submission->name      .= ' ('.JText::_('Copy').')'; // set copied name
				$submission->alias      = SubmissionHelper::getUniqueAlias($id, $submission->alias.'-copy'); // set copied alias

				// track parent for ordering update
				$parents[] = $submission->parent;

				// save copied category data
				$table->save($submission);
			}

            // set redirect message
			$msg = JText::_('Submission Copied');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Copying Category').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl, $msg);
	}

	public function publish() {
		$this->_editState(1);
	}

	public function unpublish() {
		$this->_editState(0);
	}

	protected function _editState($state) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a submission to edit publish state'));
		}

		try {

			// get item table
			$table = YTable::getInstance('submission');

			// update item state
			foreach ($cid as $id) {
				$submission = $table->get($id);
				$submission->state = $state;
				$table->save($submission);
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error editing Submission Published State').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	public function accessPublic() {
		$this->_editAccess(0);
		$this->_editTrustedMode(0);
	}

	public function accessRegistered() {
		$this->_editAccess(1);
	}

	public function accessSpecial() {
		$this->_editAccess(2);
	}

	protected function _editAccess($access) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a submission to edit access'));
		}

		try {

			// get item table
			$table = YTable::getInstance('submission');

			// update item access
			foreach ($cid as $id) {
				$submission = $table->get($id);
				$submission->access = $access;
				$table->save($submission);
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Editing Submission Access').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

	public function enableTrustedMode() {
		$this->_editTrustedMode(1);
	}

	public function disableTrustedMode() {
		$this->_editTrustedMode(0);
	}

	protected function _editTrustedMode($enabled) {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a submission to enable/disable Trusted Mode'));
		}

		try {

			// get item table
			$table = YTable::getInstance('submission');

			// update item state
			foreach ($cid as $id) {
				$submission = $table->get($id);

				$submission->params = $submission
					->getParams()
					->set('trusted_mode', $enabled)
					->toString();

				$table->save($submission);
			}

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error enabling/disabling Submission Trusted Mode').' ('.$e.')');

		}

		$this->setRedirect($this->baseurl);
	}

}

/*
	Class: SubmissionControllerException
*/
class SubmissionControllerException extends YException {}