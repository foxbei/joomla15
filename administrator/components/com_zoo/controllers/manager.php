<?php
/**
* @package   ZOO Component
* @file      manager.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ManagerController
		The controller class for application manager
*/
class ManagerController extends YController {

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
		
		// register tasks
		$this->registerTask('addtype', 'edittype');
		$this->registerTask('applytype', 'savetype');
		$this->registerTask('applyelements', 'saveelements');			
		$this->registerTask('applyassignelements', 'saveassignelements');
		$this->registerTask('applysubmission', 'savesubmission');
	}

	public function display() {

		// set toolbar items
		JToolBarHelper::title(JText::_('App Manager'), ZOO_ICON);    
		JToolBar::getInstance('toolbar')->appendButton('Popup', 'config', 'Check Requirements', JRoute::_($this->baseurl.'&task=checkrequirements&tmpl=component'), 700, 700);
		ZooHelper::toolbarHelp();
		
		// get applications
		$this->applications = Zoo::getApplicationGroups();	

		// display view
		$this->getView()->display();
	}
	
	public function info() {

		// get application metadata
		$metadata = $this->application->getMetaData();
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Information').': '.$metadata['name']));
		JToolBarHelper::custom('doexport', 'archive', 'Archive', 'Export', false);
		JToolBarHelper::custom('uninstallapplication', 'delete', 'Delete', 'Uninstall', false);
		JToolBarHelper::deleteList('APP_DELETE_WARNING', 'removeapplication');
		ZooHelper::toolbarHelp();
		
		// get application instances for selected group
		$this->applications = ApplicationHelper::getApplications($this->application->getGroup());

		// display view
		$this->getView()->setLayout('info')->display();
	}

	public function installApplication() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');
		
		try {

			$result = InstallHelper::installApplicationFromUserfile($userfile);
			$update = $result == 2 ? 'updated' : 'installed';
			
			// set redirect message
			$msg = JText::_('Application group '.$update.' successfully.');
			
		} catch (InstallHelperException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error installing Application group').' ('.$e.')');
			$msg = null;

		}
		
		$this->setRedirect($this->baseurl, $msg);
	}
	
	public function uninstallApplication() {
		
		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		try {

			InstallHelper::uninstallApplication($this->application);
			
			// set redirect message
			$msg = JText::_('Application group uninstalled successful.');
			$link = $this->baseurl;
		} catch (InstallHelperException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error uninstalling application group').' ('.$e.')');
			$msg = null;
			$link = $this->baseurl.'&task=info';

		}
		
		$this->setRedirect($link, $msg);		
	}

	public function removeApplication() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'int');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a application to delete'));
		}
		
		// get application table
		$table = YTable::getInstance('application');
		
		try {

			// delete applications
			foreach ($cid as $id) {
				$table->delete($table->get($id));
			}

			// set redirect message
			$msg = JText::_('Application Deleted');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Deleting Application').' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->baseurl.'&task=info', $msg);
	}
	
	public function types() {
		
		// get application metadata
		$metadata = $this->application->getMetaData();
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Types').': ' . $metadata['name']));		
		JToolBarHelper::custom('copytype', 'copy', '', 'Copy');			
		JToolBarHelper::deleteList('', 'removetype');
		JToolBarHelper::editListX('edittype');
		JToolBarHelper::addNewX('addtype');
		ZooHelper::toolbarHelp();
				
		// get types
		$this->types = $this->application->getTypes();
		
		// get templates
		$this->templates = $this->application->getTemplates();

		// get modules
		$this->modules = array();
		$module_path = JPATH_ROOT.'/modules/';
		foreach (JFolder::folders($module_path) as $module) {
			if (JFolder::exists($module_path.$module.'/renderer')) {
				$this->modules[] = array('name' => $module, 'path' => $module_path.$module);
			}
		}

		// get plugins
		$this->plugins = array();
		$plugin_path = JPATH_ROOT.'/plugins/';
		foreach (JFolder::folders($plugin_path) as $plugin_type) {
			foreach (JFolder::folders($plugin_path.$plugin_type) as $plugin) {
				if (JFolder::exists($plugin_path.$plugin_type.'/'.$plugin.'/renderer')) {
					$this->plugins[$plugin_type][] = array('name' => $plugin, 'path' => $plugin_path.$plugin_type.'/'.$plugin);
				}
			}
		}
						
		// display view
		$this->getView()->setLayout('types')->display();		
	}

	public function editType() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);

		// get request vars
		$cid  = YRequest::getArray('cid.0', '', 'string');
		$this->edit = $cid ? true : false;	
		
		// get type
		if (empty($cid)) {
			$this->type = new Type(null, $this->application->getPath().'/types');
		} else {
			$this->type = $this->application->getType($cid);
		}

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.($this->edit ? JText::_('Edit') : JText::_('New')).' ]</small></small>'));
		JToolBarHelper::save('savetype');
		JToolBarHelper::apply('applytype');
		JToolBarHelper::cancel('types', $this->edit ?	'Close' : 'Cancel');
		ZooHelper::toolbarHelp();

		// display view
		$this->getView()->setLayout('edittype')->display();
	}	

	public function copyType() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$cid = YRequest::getArray('cid', array(), 'string');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a type to copy'));
		}
		
		// copy types
		foreach ($cid as $id) {
			try {

				// get type
				$type = $this->application->getType($id);

				$xml  = $type->getXML();

				// copy type
				$type->id          = null;                      // set id to null, to force new
				$type->identifier .= '-copy';                   // set copied alias
				TypeHelper::setUniqueIndentifier($type);		// set unique identifier
				$type->name       .= ' ('.JText::_('Copy').')'; // set copied name
				
				// save copied type
				$type->save();				

				// save xml
				$type->setXML($xml)->save();

				// copy template positions
				$path = $this->application->getPath().'/templates/';
				foreach (JFolder::folders($path, '.', false, true) as $template) {
					$this->_copyPositionsConfig($id, $template, $type);
				}

				// copy module positions
				$module_path = JPATH_ROOT.'/modules/';
				foreach (JFolder::folders($module_path) as $module) {
					if (JFolder::exists($module_path.$module.'/renderer')) {
						$this->_copyPositionsConfig($id, $module_path.$module, $type);
					}
				}

				// copy plugin positions
				$plugin_path = JPATH_ROOT.'/plugins/';
				foreach (JFolder::folders($plugin_path) as $plugin_type) {
					foreach (JFolder::folders($plugin_path.$plugin_type) as $plugin) {
						if (JFolder::exists($plugin_path.$plugin_type.'/'.$plugin.'/renderer')) {
							$this->_copyPositionsConfig($id, $plugin_path.$plugin_type.'/'.$plugin, $type);
						}
					}
				}
				
				$msg = JText::_('Type Copied');
				
			} catch (YException $e) {
				
				// raise notice on exception
				JError::raiseNotice(0, JText::_('Error Copying Type').' ('.$e.')');
				$msg = null;
				break;

			}
		}

		$this->setRedirect($this->baseurl.'&task=types', $msg);
	}

	protected function _copyPositionsConfig($id, $path, $type) {
		// get renderer
		$renderer = new ItemRenderer();
		$renderer->addPath($path);

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			JFolder::copy($folder.$id, $folder.$type->id);
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($this->group.'.'.$id.'.');
		$config->set($this->group.'.'.$type->id.'.', $params);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

	}

	public function saveType() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$post = YRequest::get('post');
		$cid  = YRequest::getArray('cid.0', '', 'string');

		// get type
		$type = $this->application->getType($cid);
		
		// type is new ?
		if (!$type) {
			$type = new Type(null, $this->application->getPath().'/types');
			$type->setApplication($this->application);
		} 

		// filter identifier
		$post['identifier'] = YString::sluggify($post['identifier'] == '' ? $post['name'] : $post['identifier']);

		try {
			
			// set post data and save type
			$type->bind($post);
 			TypeHelper::setUniqueIndentifier($type);
            
            // clean and save layout positions
            if ($type->id != $type->identifier) {

				// update templates
                $path = $this->application->getPath().'/templates';
                foreach (JFolder::folders($path, '.', false, true) as $template) {
                    $this->_sanatizePositionsConfig($template, $type);
                }

                // update submissions
				if (!empty($type->id)) {
					$table = YTable::getInstance('submission');
					$applications = array_keys(ApplicationHelper::getApplications($this->application->getGroup()));
					if (!empty($applications)) {
						$submissions = $table->all(array('conditions' => 'application_id IN ('.implode(',', $applications).')'));
						foreach($submissions as $submission) {
							$params = $submission->getParams();
							if ($tmp = $params->get('form.'.$type->id)) {
								$params->set('form.'.$type->identifier, $tmp);
								$params->remove('form.'.$type->id);
								$submission->params = $params->toString();
								$table->save($submission);
							}
						}
					}
				}

				// update modules
				$module_path = JPATH_ROOT.'/modules/';
				foreach (JFolder::folders($module_path) as $module) {
					if (JFolder::exists($module_path.$module.'/renderer')) {
						$this->_sanatizePositionsConfig($module_path.$module, $type);
					}
				}

				// update plugins
				$plugin_path = JPATH_ROOT.'/plugins/';
				foreach (JFolder::folders($plugin_path) as $plugin_type) {
					foreach (JFolder::folders($plugin_path.$plugin_type) as $plugin) {
						if (JFolder::exists($plugin_path.$plugin_type.'/'.$plugin.'/renderer')) {
							$this->_sanatizePositionsConfig($plugin_path.$plugin_type.'/'.$plugin, $type);
						}
					}
				}

            }

            $type->save();
						
			// set redirect message
			$msg = JText::_('Type Saved');

		} catch (YException $e) {

			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Type').' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;

		}

		switch ($this->getTask()) {
			case 'applytype':
				$link = $this->baseurl.'&task=edittype&cid[]='.$type->id;
				break;
			case 'savetype':
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	protected function _sanatizePositionsConfig($path, $type) {
		// get renderer
		$renderer = new ItemRenderer();
		$renderer->addPath($path);

		// rename folder if special type
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type->id)) {
			$folder = $path.DIRECTORY_SEPARATOR.$renderer->getFolder().DIRECTORY_SEPARATOR.'item'.DIRECTORY_SEPARATOR;
			JFolder::move($folder.$type->id, $folder.$type->identifier);
		}

		// get positions and config
		$config = $renderer->getConfig('item');
		$params = $config->get($this->group.'.'.$type->id.'.');
		$config->set($this->group.'.'.$type->identifier.'.', $params);
		$config->remove($this->group.'.'.$type->id.'.');
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');

	}

	public function removeType() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
	
		// init vars
		$cid = YRequest::getArray('cid', array(), 'string');

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a type to delete'));
		}
		
		foreach ($cid as $id) {
			try {

				// delete type
				$type = $this->application->getType($id);
				$type->delete();

				// update submissions
				$table = YTable::getInstance('submission');
				$applications = array_keys(ApplicationHelper::getApplications($this->application->getGroup()));
				if (!empty($applications)) {
					$submissions = $table->all(array('conditions' => 'application_id IN ('.implode(',', $applications).')'));
					foreach($submissions as $submission) {
						$params = $submission->getParams();
						$params->remove('form.'.$id);
						$submission->params = $params->toString();
						$table->save($submission);
					}
				}

				// set redirect message
				$msg = JText::_('Type Deleted');

			} catch (YException $e) {

				// raise notice on exception
				JError::raiseNotice(0, JText::_('Error Deleting Type').' ('.$e.')');
				$msg = null;
				break;

			}
		}

		$this->setRedirect($this->baseurl.'&task=types', $msg);
	}

	public function editElements() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);

		// get request vars
		$cid = YRequest::getArray('cid.0', '', 'string');

		// get type
		$this->type = $this->application->getType($cid);

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Edit elements').' ]</small></small>'));
		JToolBarHelper::save('saveelements');
		JToolBarHelper::apply('applyelements');
		JToolBarHelper::cancel('types', 'Close');
		ZooHelper::toolbarHelp();
		
		// sort elements by group
		foreach (ElementHelper::getAll($this->application->getPath().'/elements') as $element) {
			$metadata = $element->getMetaData();
			$this->elements[$metadata['group']][$element->getElementType()] = $element;
		}
		ksort($this->elements);
		foreach($this->elements as $group => $elements) {
			ksort($elements);
			$this->elements[$group] = $elements; 	
		}

		// display view
		$this->getView()->setLayout('editElements')->display();
	}

	public function addElement() {

		// get request vars		
		$element = YRequest::getWord('element', 'text');
		$count	 = YRequest::getVar('count', 0);
		
		// load element
		$this->element = ElementHelper::loadElement($element, $this->application->getPath().'/elements');
		$this->var     = 'new_elements['.$count.']';

		// display view
		$this->getView()->setLayout('addElement')->display();
	}

	public function saveElements() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$post = YRequest::get('post');
		$cid  = YRequest::getArray('cid.0', '', 'string');

		try {

			// get type
			$type = $this->application->getType($cid);

			// save elements
			ElementHelper::saveElements($post, $type);

			// save type
			$type->save();

			// reset related item search data
			$table = YTable::getInstance('item');
			$items = $table->getByType($type->id, $this->application->id);
			foreach ($items as $item) {
				$table->save($item);
			}
			
			$msg = JText::_('Elements Saved');	
		
		} catch (YException $e) {
			
			JError::raiseNotice(0, JText::_('Error Saving Elements').' ('.$e.')');
			$this->_task = 'applyelements';
			$msg = null;
	
		}

		switch ($this->getTask()) {
			case 'applyelements':
				$link = $this->baseurl.'&task=editelements&cid[]='.$type->id;
				break;
			case 'saveelements':
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	public function assignElements() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);
		
		// init vars
		$type           = YRequest::getString('type');
		$this->template = YRequest::getString('template');
		$this->module   = YRequest::getString('module');
		$this->plugin   = YRequest::getString('plugin');
		$this->layout   = YRequest::getString('layout');

		// get type
		$this->type = $this->application->getType($type);		

        if ($this->type) {
            // set toolbar items
            $this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Assign elements').': '. $this->layout .' ]</small></small>'));
            JToolBarHelper::save('saveassignelements');
            JToolBarHelper::apply('applyassignelements');
            JToolBarHelper::cancel('types');
            ZooHelper::toolbarHelp();

            // for template, module, plugin
            if ($this->template) {
                $this->path = $this->application->getPath().'/templates/'.$this->template;
            } else if ($this->module) {
                $this->path = JPATH_ROOT.'/modules/'.$this->module;
            } else if ($this->plugin) {
                $this->path = JPATH_ROOT.'/plugins/'.$this->plugin;
            }

            // get renderer
            $renderer = new ItemRenderer();
            $renderer->addPath($this->path);

            // get positions and config
            $this->config = $renderer->getConfig('item')->get($this->group.'.'.$type.'.'.$this->layout);

            $prefix = 'item.';
            if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type)) {
                $prefix .= $type.'.';
            }
            $this->positions = $renderer->getPositions($prefix.$this->layout);

            // display view
            $this->getView()->setLayout('assignelements')->display();

        } else {

			JError::raiseNotice(0, JText::_('Unable find type. ').' ('.$type.')');
			$this->setRedirect($this->baseurl . '&task=types&group=' . $this->application->getGroup());
            
		}
	}

	public function saveAssignElements() {
		
		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$type      = YRequest::getString('type');		
		$template  = YRequest::getString('template');
		$module    = YRequest::getString('module');
		$layout    = YRequest::getString('layout');
		$plugin    = YRequest::getString('plugin');
		$positions = YRequest::getVar('positions', array(), 'post', 'array');

		// for template, module
		if ($template) {
			$path = $this->application->getPath().'/templates/'.$template;
		} else if ($module) {
			$path = JPATH_ROOT.'/modules/'.$module;
		} else if ($plugin) {
			$path = JPATH_ROOT.'/plugins/'.$plugin;
		}

		// get renderer
		$renderer = new ItemRenderer();
		$renderer->addPath($path);
	
		// clean config
		$config = $renderer->getConfig('item');
		foreach ($config->toArray() as $key => $value) {
			$parts = explode('.', $key);
			if ($parts[0] == $this->group && !$this->application->getType($parts[1])) {
				$config->remove($key);
			}
		}

		// save config
		$config->set($this->group.'.'.$type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');
		
		switch ($this->getTask()) {
			case 'applyassignelements':
				$link  = $this->baseurl.'&task=assignelements&type='.$type.'&layout='.$layout;
				$link .= $template ? '&template='.$template : null;
				$link .= $module ? '&module='.$module : null;
				$link .= $plugin ? '&plugin='.$plugin : null;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('Elements Assigned'));
	}

	public function assignSubmission() {

		// disable menu
		YRequest::setVar('hidemainmenu', 1);
		
		// init vars
		$type           = YRequest::getString('type');
		$this->template = YRequest::getString('template');
		$this->layout   = YRequest::getString('layout');

		// get type
		$this->type = $this->application->getType($type);		
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Type').': '.$this->type->name.' <small><small>[ '.JText::_('Assign Submittable elements').': '. $this->layout .' ]</small></small>'));
		JToolBarHelper::save('savesubmission');
		JToolBarHelper::apply('applysubmission');
		JToolBarHelper::cancel('types');
		ZooHelper::toolbarHelp();

		// for template, module, plugin
		if ($this->template) {
			$this->path = $this->application->getPath().'/templates/'.$this->template;
		}

		// get renderer
		$renderer = new ItemRenderer();
		$renderer->addPath($this->path);

		// get positions and config
		$this->config    = $renderer->getConfig('item')->get($this->group.'.'.$type.'.'.$this->layout);

		$prefix = 'item.';
		if ($renderer->pathExists('item'.DIRECTORY_SEPARATOR.$type)) {
			$prefix .= $type.'.';
		}
		$this->positions = $renderer->getPositions($prefix.$this->layout);
		
		// display view
		$this->getView()->setLayout('assignsubmission')->display();
	}
	
	public function saveSubmission() {
		
		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// init vars
		$type      = YRequest::getString('type');		
		$template  = YRequest::getString('template');
		$layout    = YRequest::getString('layout');
		$positions = YRequest::getVar('positions', array(), 'post', 'array');
        unset($positions['unassigned']);

		// for template, module
		if ($template) {
			$path = $this->application->getPath().'/templates/'.$template;
		}

		// get renderer
		$renderer = new ItemRenderer();
		$renderer->addPath($path);
	
		// clean config
		$config = $renderer->getConfig('item');
		foreach ($config->toArray() as $key => $value) {
			$parts = explode('.', $key);
			if ($parts[0] == $this->group && !$this->application->getType($parts[1])) {
				$config->remove($key);
			}
		}

		// save config
		$config->set($this->group.'.'.$type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/item/positions.config');
		
		switch ($this->getTask()) {
			case 'applysubmission':
				$link  = $this->baseurl.'&task=assignsubmission&type='.$type.'&layout='.$layout;
				$link .= $template ? '&template='.$template : null;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('Submitable Elements Assigned'));
	}	
	
	public function doExport() {
		
		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');		
		
		$group = $this->application->getGroup();
		
		require_once(JPATH_ROOT.'/administrator/includes/pcl/pclzip.lib.php');
		
		$filepath = JPATH_ROOT . '/tmp/' . $group . '.zip';
		$read_directory = ZOO_APPLICATION_PATH . '/' . $group . '/';
		$zip = new PclZip($filepath);
		$files = YFile::readDirectoryFiles($read_directory, $read_directory, '', '/^[^\.]/');
		$zip->create($files, PCLZIP_OPT_ADD_PATH, '../', PCLZIP_OPT_REMOVE_PATH, $read_directory);
		if (is_readable($filepath) && JFile::exists($filepath)) {
			YFile::output($filepath);
			if (!JFile::delete($filepath)) {
				JError::raiseNotice(0, JText::_('Unable to delete file').' ('.$filepath.')');			
				$this->setRedirect($this->baseurl.'&task=info');
			}
		} else {
			JError::raiseNotice(0, JText::_('Unable to create file').' ('.$filepath.')');			
			$this->setRedirect($this->baseurl.'&task=info');			
		}	
		
	}

    public function checkRequirements() {

        // check requirements
        JLoader::register('YRequirements', ZOO_ADMIN_PATH.'/installation/requirements.php');

        $requirements = new YRequirements();
        $requirements->checkRequirements();
        $requirements->displayResults();

    }

}

/*
	Class: ManagerControllerException
*/
class ManagerControllerException extends YException {}