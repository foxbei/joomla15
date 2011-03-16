<?php
/**
* @package   ZOO Component
* @file      frontpage.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: FrontpageController
		The controller class for frontpage
*/
class FrontpageController extends YController {

	public $application;
	
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application = Zoo::getApplication();			
	}

	public function display() {
	
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Frontpage')));
		JToolBarHelper::save();
		ZooHelper::toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// display view
		$this->getView()->display();
	}	

	public function save() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		// init vars
		$post = YRequest::get('post');
		$post['description'] = YRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
 
		try {

			// bind post
			$this->application->bind($post, array('params'));

			// set params
			$this->application->params = $this->application
				->getParams()
				->remove('content.')
				->remove('config.')
				->remove('template.')
				->set('content.', @$post['params']['content'])
				->set('config.', @$post['params']['config'])
				->set('template.', @$post['params']['template'])
				->toString();

			// save application
			YTable::getInstance('application')->save($this->application);
			
			// set redirect message
			$msg = JText::_('Frontpage Saved');

		} catch (YException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Frontpage').' ('.$e.')');
			$msg = null;
						
		}

		$this->setRedirect($this->baseurl, $msg);
	}

}

/*
	Class: FrontpageControllerException
*/
class FrontpageControllerException extends YException {}