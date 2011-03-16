<?php
/**
* @package   ZOO Component
* @file      new.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: NewController
		The controller class for creating a new application
*/
class NewController extends YController {

	public $group;
	public $application;
	
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application group
		$this->group = YRequest::getString('group');

		// if group exists
		if ($this->group) {
			
			// add group to base url
			$this->baseurl .= '&group='.$this->group;

			// create application object
			$this->application = new Application();
			$this->application->setGroup($this->group);
		}
	}

	public function display() {

		// set toolbar items
		JToolBarHelper::title(JText::_('New App'), ZOO_ICON);		
		ZooHelper::toolbarHelp();
		
		// get applications
		$this->applications = Zoo::getApplicationGroups();	

		// display view
		$this->getView()->display();
	}

	public function add() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);

		// get application metadata
		$metadata = $this->application->getMetaData();
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('New App').': '.$metadata['name']));	
		JToolBarHelper::save();
		JToolBarHelper::custom('', 'back', '', 'Back', false);
		ZooHelper::toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

		// set default template
		$this->params->set('template', 'default');

		// template select
		$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Template').' -'));
		foreach ($this->application->getTemplates() as $template) {
			$metadata  = $template->getMetaData(); 
			$options[] = JHTML::_('select.option', $template->name, $metadata['name']);
		}
		
		$this->lists['select_template'] = JHTML::_('select.genericlist',  $options, 'template', '', 'value', 'text', $this->params->get('template'));

		// display view
		$this->getView()->setLayout('application')->display();		
	}

	public function save() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		// init vars
		$post = YRequest::get('post');
 
		try {

			// bind post
			$this->application->bind($post, array('params'));

			// set params
			$params = $this->application
				->getParams()
				->remove('global.')
				->set('group', @$post['group'])
				->set('template', @$post['template'])
				->set('global.config.', @$post['params']['config'])
				->set('global.template.', @$post['params']['template']);

			if (isset($post['addons']) && is_array($post['addons'])) {
				foreach ($post['addons'] as $addon => $value) {
					$params->set("global.$addon.", $value);
				}
			}

			$this->application->params = $params->toString();

			// save application
			YTable::getInstance('application')->save($this->application);
			
			// set redirect
			$msg  = JText::_('Application Saved');
			$link = $this->link_base.'&changeapp='.$this->application->id;

		} catch (YException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Application').' ('.$e.')');

			// set redirect
			$msg  = null;
			$link = $this->baseurl.'&task=add';
						
		}

		$this->setRedirect($link, $msg);
	}
	
	public function getApplicationParams() {

		// init vars
		$template     = YRequest::getCmd('template');
		$this->params = $this->application->getParams();

		// set template
		$this->params->set('template', $template);

		// display view
		$this->getView()->setLayout('_applicationparams')->display();
	}

}

/*
	Class: NewControllerException
*/
class NewControllerException extends YException {}