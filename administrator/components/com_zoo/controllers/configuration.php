<?php
/**
* @package   ZOO Component
* @file      configuration.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ConfigurationController
		The controller class for application configuration
*/
class ConfigurationController extends YController {

	public $application;
	
	public function __construct($default = array()) {
		parent::__construct($default);

		// get application
		$this->application = Zoo::getApplication();			

		// register tasks
		$this->registerTask('applyassignelements', 'saveassignelements');
	}

	public function display() {

		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Config')));
		JToolBarHelper::save();
		ZooHelper::toolbarHelp();

		// get params
		$this->params = $this->application->getParams();

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
			
			// set redirect message
			$msg = JText::_('Application Saved');

		} catch (YException $e) {
			
			// raise notice on exception
			JError::raiseNotice(0, JText::_('Error Saving Application').' ('.$e.')');
			$msg = null;
						
		}

		$this->setRedirect($this->baseurl, $msg);
	}
	
	public function getApplicationParams() {

		// init vars
		$template     = YRequest::getCmd('template');

		// get params
		$this->params = $this->application->getParams();

		// set template
		$this->params->set('template', $template);

		// display view
		$this->getView()->setLayout('_applicationparams')->display();
	}

	public function importExport() {
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Import / Export')));
		ZooHelper::toolbarHelp();

		$files = YFile::readDirectoryFiles(ZOO_ADMIN_PATH . '/helpers/exporter/', ZOO_ADMIN_PATH . '/helpers/exporter/', '/.*\.php$/', false);
		
		$this->exporter = array();
		foreach ($files as $file) {
			if ($instance = ExportHelper::getInstance(basename($file, '.php'))) {
				if ($instance->getName() != 'Zoo v2') {
					$this->exporter[] = $instance;
				}
			}
		}

		// display view
		$this->getView()->setLayout('importexport')->display();
	}
	
	public function importFrom() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');
		
		$exporter = YRequest::getString('exporter');
				
		try {
			
			$xml = ExportHelper::getInstance($exporter)->export();
			
			$file = rtrim($this->joomla->getCfg('tmp_path'), '\/') . '/' . YUtility::generateUUID() . '.tmp';
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
			JFile::write($file, $xml);
						
		} catch (Exception $e) {
				
			// raise error on exception
			JError::raiseNotice(0, JText::_('Error During Export').' ('.$e.')');
			$this->setRedirect($this->baseurl.'&task=importexport', $msg);

		}

		$this->_import($file);
		
	}

	public function import() {

		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');		

		$userfile = null;

		$xmlfile = JRequest::getVar('import-xml', array(), 'files', 'array');

		try {

			// validate
			$validator = new YValidatorFile(array('extensions' => array('xml')));
			$userfile = $validator->clean($xmlfile);
			$type = 'xml';

		} catch (YValidatorException $e) {}

		$csvfile = JRequest::getVar('import-csv', array(), 'files', 'array');

		try {

			// validate
			$validator = new YValidatorFile(array('extensions' => array('csv')));
			$userfile = $validator->clean($csvfile);
			$type = 'csv';

		} catch (YValidatorException $e) {}

		if (!empty($userfile)) {
			$file = rtrim($this->joomla->getCfg('tmp_path'), '\/') . '/' . basename($userfile['tmp_name']);
			if (JFile::upload($userfile['tmp_name'], $file)) {

				$this->_import($file, $type);

			} else {
				// raise error on exception
				JError::raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
				$this->setRedirect($this->baseurl.'&task=importexport', $msg);
			}			
		} else {
			// raise error on exception
			JError::raiseNotice(0, JText::_('Error Importing (Unable to upload file.)'));
			$this->setRedirect($this->baseurl.'&task=importexport', $msg);
		}


	}

	public function importCSV() {

		$file = YRequest::getCmd('file', '');
		$file = rtrim($this->joomla->getCfg('tmp_path'), '\/') . '/' . $file;

		$this->_import($file, 'importcsv');
	}

	protected function _import($file, $type = 'xml') {
		
		// disable menu
		YRequest::setVar('hidemainmenu', 1);		
		
		// set toolbar items
		$this->joomla->set('JComponentTitle', $this->application->getToolbarTitle(JText::_('Import').': '.$this->application->name));
		JToolBarHelper::cancel('importexport', 'Cancel');
		ZooHelper::toolbarHelp();

		// set_time_limit doesn't work in safe mode
        if (!ini_get('safe_mode')) { 
		    @set_time_limit(0);
        }		

		$layout = '';
		switch ($type) {
			case 'xml':
				if ($document = YXML::loadFile($file)) {

					$this->info = ImportHelper::getImportInfo($document);
					$this->file = basename($file);

				} else {

					// raise error on exception
					JError::raiseNotice(0, JText::_('Error Importing (Not a valid XML file)'));
					$this->setRedirect($this->baseurl.'&task=importexport', $msg);

				}
				$layout = 'importxml';
				break;
			case 'csv':

				$this->file = basename($file);

				$layout = 'configcsv';
				break;
			case 'importcsv':
				$this->contains_headers = YRequest::getBool('contains-headers', false);
				$this->field_separator	= YRequest::getString('field-separator', ',');
				$this->field_separator	= empty($this->field_separator) ? ',' : substr($this->field_separator, 0, 1);
				$this->field_enclosure	= YRequest::getString('field-enclosure', '"');
				$this->field_enclosure	= empty($this->field_enclosure) ? '"' : substr($this->field_enclosure, 0, 1);

				$this->info = ImportHelper::getImportInfoCSV($file, $this->contains_headers, $this->field_separator, $this->field_enclosure);
				$this->file = basename($file);

				$layout = 'importcsv';
				break;
		}

		// display view
		$this->getView()->setLayout($layout)->display();
		
	}
	
	public function doImport() {
		
		// init vars
		$import_frontpage   = YRequest::getBool('import-frontpage', false);
		$frontpage_params 	= YRequest::getArray('frontpage-params', array());		
		$import_categories  = YRequest::getBool('import-categories', false);
		$category_params 	= YRequest::getArray('category-params', array());
		$element_assignment = YRequest::getArray('element-assign', array());
		$types				= YRequest::getArray('types', array());
		$file 				= YRequest::getCmd('file', '');		
		$file 				= rtrim($this->joomla->getCfg('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {
			
			// set_time_limit doesn't work in safe mode
	        if (!ini_get('safe_mode')) { 
			    @set_time_limit(0);
	        }				
			
			ImportHelper::import($file, $import_frontpage, $frontpage_params, $import_categories, $category_params, $element_assignment, $types);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}

	public function doImportCSV() {

		// init vars
		$contains_headers   = YRequest::getBool('contains-headers', false);
		$field_separator    = YRequest::getString('field-separator', ',');
		$field_enclosure    = YRequest::getString('field-enclosure', '"');
		$element_assignment = YRequest::getArray('element-assign', array());
		$type				= YRequest::getCmd('type', '');
		$file 				= YRequest::getCmd('file', '');
		$file 				= rtrim($this->joomla->getCfg('tmp_path'), '\/') . '/' . $file;

		if (JFile::exists($file)) {

			// set_time_limit doesn't work in safe mode
	        if (!ini_get('safe_mode')) {
			    @set_time_limit(0);
	        }

			ImportHelper::importCSV($file, $type, $contains_headers, $field_separator, $field_enclosure, $element_assignment);
		}

		$this->setRedirect($this->baseurl.'&task=importexport', JText::_('Import successfull'));
	}
	
	public function doExport() {
				
		$exporter = YRequest::getString('exporter');
		
		if ($exporter) {
			
			try {
												
				// set_time_limit doesn't work in safe mode
		        if (!ini_get('safe_mode')) { 
				    @set_time_limit(0);
		        }
				
				$xml = ExportHelper::getInstance($exporter)->export();

				header("Pragma: public");
		        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		        header("Expires: 0");
		        header("Content-Transfer-Encoding: binary");
				header ("Content-Type: text/xml");
				header('Content-Disposition: attachment;'
				.' filename="'.JFilterOutput::stringURLSafe($this->application->name).'.xml";'
				);

				echo $xml;
								
			} catch (ExportHelperException $e) {
				
				// raise error on exception
				JError::raiseNotice(0, JText::_('Error Exporting').' ('.$e.')');
				$this->setRedirect($this->baseurl.'&task=importexport', $msg);
				
			}
		}
	}
	
}

/*
	Class: ConfigurationControllerException
*/
class ConfigurationControllerException extends YException {}