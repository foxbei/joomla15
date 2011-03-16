<?php
/**
* @package   ZOO Component
* @file      application.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Application
		Application related attributes and functions.
*/
class Application extends YObject {

    /*
		Variable: id
			Primary key.
    */
	public $id;

    /*
		Variable: name
			Application name.
    */
	public $name;

    /*
		Variable: alias
			Application alias.
    */
	public $alias;

    /*
		Variable: description
			Application description.
    */
	public $description;

    /*
		Variable: application_group
			Application group.
    */
	public $application_group;

    /*
       Variable: params
         Application params.
    */
	public $params;

    /*
		Variable: metaxml_file
			Application meta xml filename.
    */
	public $metaxml_file = 'application.xml';
	
    /*
       Variable: _params
         YParameter object.
    */
	public $_params;

    /*
       Variable: _categories
         Related categories.
    */
	public $_categories;
	
    /*
       Variable: _category_tree
         Related categories tree.
    */
	public $_category_tree;	
	
	/*
		Variable: _metaxml
			Application meta data YXMLElement.
    */
	public $_metaxml;

	/*
		Variable: _submissions
			Application submission instances.
    */
	protected $_submissions = array();

	/*
		Variable: _types
			Application types instances.
    */
	protected $_types = array();

	/*
		Variable: _templates
			Application template instances.
    */
	protected $_templates = array();

	/*
		Function: dispatch
			Dispatch application through executing the current controller.

		Returns:
			Void
	*/
	public function dispatch() {
		
		// get joomla and application table
		$joomla = JFactory::getApplication();
		
		// load site language
		if ($joomla->isSite()) {
			JFactory::getLanguage()->load('com_zoo', $this->getPath(), null, true);
		}
		
		// get request vars
		$task = YRequest::getCmd('task');
		$ctrl = YRequest::getWord('controller', 'default');

		// perform the request task
		$controller = $this->getController($ctrl);
		$controller->execute($task);
		$controller->redirect();
	}

	/*
		Function: getController
			Retrieve current controller.

		Parameters:
  			$name - Controller name.

		Returns:
			JController
	*/
	public function getController($name) {

		// set controller class
		$controller = $name.'Controller';

		// load controller
		if (!class_exists($controller) && ($path = JPath::find(JPATH_COMPONENT.'/controllers', $name.'.php'))) {
			require_once($path);
		}

		// set and return controller
		if (class_exists($controller)) {
			return new $controller();
		}

		throw new ApplicationException("Controller class not found [class]: $controller");
	}

	/*
		Function: getPath
			Retrieve application path.

		Returns:
			String - Application path
	*/
	public function getPath() {
		return ZOO_APPLICATION_PATH.'/'.$this->getGroup();
	}
	
	/*
		Function: getURI
			Retrieve application URI.

		Returns:
			String - Application URI
	*/		
	public function getURI() {
		return ZOO_APPLICATION_URI.'/'.$this->getGroup();
	}
	
	/*
		Function: hasIcon
			Checks for icon existence.

		Returns:
			Boolean - true if icon exists
	*/		
	public function hasIcon() {
		return JFile::exists($this->getPath() . '/application.png');
	}
	
	/*
		Function: getIcon
			Retrieve application icon.

		Returns:
			String - icon URI
	*/		
	public function getIcon() {
		if ($this->hasIcon()) {
			return $this->getURI() . '/application.png';
		} else {
			return ZOO_ADMIN_URI . '/assets/images/zoo.png';
		}
	}
	
	/*
		Function: getInfoImage
			Retrieve application info image.

		Returns:
			String - icon URI
	*/		
	public function getInfoImage() {
		if (JFile::exists($this->getPath() . '/application_info.png')) {
			return $this->getURI() . '/application_info.png';
		}
		return '';
	}

	/*
		Function: getToolbarTitle
			Retrieve applications toolbar title html.

		Returns:
			String - toolbar title html
	*/	
	public function getToolbarTitle($title) {

		$html[] = '<div class="header icon-48-'.(($this->hasIcon()) ? 'application"' : 'zoo"').'>';
		$html[] = $this->hasIcon() ? '<img src="'.$this->getIcon().'" width="48" height="48" style="margin-left:-55px;vertical-align:middle;" />' : null;
		$html[] = $title;
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/*
		Function: getGroup
			Retrieve application group.

		Returns:
			String - Application group
	*/
	public function getGroup() {
		return $this->application_group;
	}

	/*
		Function: setGroup
			Set application group.

		Returns:
			Application
	*/
	public function setGroup($group) {

		$this->application_group = $group;
		return $this;
		
	}
	
	/*
    	Function: getCategories
    	  Get categories. 

		Parameters:
	      $published - If true, return only published categories.

	   Returns:
	      Array - Categories
 	*/
	public function getCategories($published = false, $item_count = false) {

		// get categories
		if (empty($this->_categories)) {
			$this->_categories = YTable::getInstance('category')->getAll($this->id, $published, $item_count);
		}
		
		return $this->_categories;
	}

	/*
    	Function: getCategoryTree
    	  Get categories as tree. 

		Parameters:
	      $published - If true, return only published categories.
	      $user - User
	
	   Returns:
	      Array - Categories
 	*/
	public function getCategoryTree($published = false, $user = null, $item_count = false) {

		// get category tree
		if (empty($this->_category_tree)) {
			// get categories and item count
			$categories = $this->getCategories($published, $item_count);
			
			$this->_category_tree = CategoryHelper::buildTree($this->id, $categories);
		}
		
		return $this->_category_tree;
	}

	/*
    	Function: getCategoryCount
    	  Get categories count. 

	   Returns:
	      Int
 	*/
	public function getCategoryCount() {
		return YTable::getInstance('category')->count($this->id);
	}
	
	/*
    	Function: getItemCount
    	  Get item count. 

	   Returns:
	      Int
 	*/
	public function getItemCount() {
		return YTable::getInstance('item')->getApplicationItemCount($this->id);
	}	
	
	/*
		Function: getTemplate
			Get active application template.

		Returns:
			Template
	*/
	public function getTemplate() {
		$templates = $this->getTemplates();
		if (($name = $this->getParams()->get('template')) && isset($templates[$name])) {
			return $templates[$name];
		}
		
		return null;
	}	
	
	/*
		Function: getTemplates
			Get application templates.

		Returns:
			Array
	*/
	public function getTemplates() {

		if (empty($this->_templates)) {

			$path = $this->getPath().'/templates';

			if (is_dir($path) && ($folders = JFolder::folders($path))) {
				foreach ($folders as $folder) {
					$this->_templates[$folder] = new Template($folder, $path.'/'.$folder);
				}
			}		
		}
		
		return $this->_templates;
	}
	
	/*
		Function: getType
			Retrieve application type by id.

		Parameters:
  			id - Type id.

		Returns:
			Type
	*/
	public function getType($id) {
		$types = $this->getTypes();

		if (isset($types[$id])) {
			return $types[$id];
		}

		return null;
	}

	/*
		Function: getTypes
			Retrieve application types.

		Returns:
			Array
	*/
	public function getTypes() {

		if (empty($this->_types)) {

			$path   = $this->getPath().'/types';
			$filter = '^.*xml$';

			if (is_dir($path) && ($files = JFolder::files($path, $filter))) {
				foreach ($files as $file) {
					$alias = basename($file, '.xml');
					$this->_types[$alias] = new Type($alias, $path);
					$this->_types[$alias]->setApplication($this);
				}
			}		
		}
		
		return $this->_types;
	}

  	/*
		Function: getSubmission
			Retrieve application submission by id.

		Parameters:
  			id - Submission id.

		Returns:
			Submission
	*/
	public function getSubmission($id) {
		$submissions = $this->getSubmissions();

		if (isset($submissions[$id])) {
			return $submissions[$id];
		}

		return null;
	}

	/*
		Function: getSubmissions
			Retrieve application submissions.

		Returns:
			Array
	*/
	public function getSubmissions() {

		if (empty($this->_submissions)) {
			$table = YTable::getInstance('submission');
            $this->_submissions = $table->all(array('conditions' => array('application_id = ' . (int) $this->id)));
		}

		return $this->_submissions;
	}

	/*
		Function: getParams
			Gets application params.

		Parameters:
  			$for - Get params for a specific use, including overidden values.

		Returns:
			Object - JParameter
	*/
	public function getParams($for = null) {

		// get params
		if (empty($this->_params)) {
			$this->_params = new YParameter();
			$this->_params->loadString($this->params);
		}

		// get site params
		if ($for == 'site') {			

			$site_params = new YParameter();
			$site_params->loadArray($this->_params->toArray());
			$site_params->set('config.', $this->_params->get('global.config.'));
			$site_params->set('template.', $this->_params->get('global.template.'));

			return $site_params;

		// get frontpage params and inherit globals
		} elseif ($for == 'frontpage') {

			$frontpage_params = new YParameter();
			$frontpage_params->set('config.', $this->_params->get('global.config.'));
			$frontpage_params->set('template.', $this->_params->get('global.template.'));
			$frontpage_params->loadArray($this->_params->toArray());

			return $frontpage_params;
		}

		return $this->_params;
	}

	/*
		Function: getParamsForm
			Gets application params form.

		Returns:
			YParameterForm
	*/
	public function getParamsForm() {

		$xml = $this->getPath().'/'.$this->metaxml_file;

		// get parameter xml file
		if (JFile::exists($xml)) {

			// get form
			$form = new YParameterFormDefault($xml);
			$form->addElementPath(ZOO_ADMIN_PATH.'/joomla/elements');

			return $form;
		}
		
		return null;
	}
	
	/*
		Function: getAddonParamsForm
			Gets application addon params form.

		Returns:
			YParameterForm
	*/	
	public function getAddonParamsForms() {

		$forms = array();
		$files = JFolder::files($this->getPath().'/config/', '.xml', false, true);

		foreach ($files as $file) {
			
			// load xml
			$xml = YXML::loadFile($file);
			
			// get form
			if ($xml->getName() == 'config') {
				$forms[(string)$xml->name] = new YParameterFormDefault($file);
			}
		}

		return $forms;
	}

	/*
		Function: getMetaData
			Get application xml meta data.

		Returns:
			Array - Meta information
	*/
	public function getMetaData() {

		$data = array();
		$xml  = $this->getMetaXML();

		if (!$xml) {
			return false;
		}
		
		if ($xml->getName() != 'application') {
			return false;
		}

		$data['name'] 		  = (string) $xml->name;
		$data['creationdate'] = $xml->creationDate ? (string) $xml->creationDate : 'Unknown';
		$data['author'] 	  = $xml->author ? (string) $xml->author : 'Unknown';
		$data['copyright'] 	  = (string) $xml->copyright;
		$data['authorEmail']  = (string) $xml->authorEmail;
		$data['authorUrl']    = (string) $xml->authorUrl;
		$data['version'] 	  = (string) $xml->version;
		$data['description']  = (string) $xml->description;
		$data['license']  	  = (string) $xml->license;

		$data['positions'] = array();
		if (isset($xml->positions)) {
			foreach ($xml->positions->children() as $element) {
				$data['positions'][] = (string) $element;
			}
		}

		return $data;
	}
	
	/*
		Function: getMetaXML
			Get application xml meta file.

		Returns:
			Object - YXMLElement
	*/
	public function getMetaXML() {

		if (empty($this->_metaxml)) {
			$this->_metaxml = YXML::loadFile($this->getMetaXMLFile());
		}

		return $this->_metaxml;
	}

	/*
		Function: getMetaXMLFile
			Get application path to xml meta file.

		Returns:
			String
	*/
	public function getMetaXMLFile() {
		return $this->getPath() . '/' . $this->metaxml_file;
	}	
	
	/*
		Function: getImage
		  Get image resource info.

		Parameters:
	      $name - the param name of the image
	
	   Returns:
	      Array - Image info
	*/	
	public function getImage($name) {
		$params = $this->getParams();
		if ($image = $params->get($name)) {
			
			return JHTML::_('zoo.image', $image, $params->get($name . '_width'), $params->get($name . '_height'));
			
		}
		return null;
	}	
	
	/*
		Function: getImage
		  Executes Content Plugins on text.

		Parameters:
	      $text - the text
	
	   Returns:
	      text - string
	*/		
	public function getText($text) {
		return ZooHelper::triggerContentPlugins($text);
	}
	
	/*
		Function: addMenuItems
		  Add menu items of application in administrator menu.

	   Returns:
	      Void
	*/	
	public function addMenuItems($menu) {
		
		// get current controller
		$controller = YRequest::getWord('controller');
		$controller = in_array($controller, array('new', 'manager')) ? 'item' : $controller;
		
		// create application tab
		$tab = new YMenuItem($this->id, $this->name, 'index.php?option=com_zoo&controller='.$controller.'&changeapp='.$this->id);
		$menu->addChild($tab);
		
		// menu items
		$items = array(
			'item'          => JText::_('Items'),
			'category'      => JText::_('Categories'),
			'frontpage'     => JText::_('Frontpage'),
			'comment'       => JText::_('Comments'),
			'tag'           => JText::_('Tags'),
            'submission'    => JText::_('Submissions')
		);

		// add menu items
		foreach ($items as $controller => $name) {
			$tab->addChild(new YMenuItem($this->id.'-'.$controller, $name, 'index.php?option=com_zoo&controller='.$controller.'&changeapp='.$this->id));
		}

		// add config menu item
		$id     = $this->id.'-configuration';
		$link   = 'index.php?option=com_zoo&controller=configuration&changeapp='.$this->id;
		$config = new YMenuItem($id, JText::_('Config'), $link);
		$config->addChild(new YMenuItem($id, JText::_('Application'), $link));
		$config->addChild(new YMenuItem($id.'-importexport', JText::_('Import / Export'), $link.'&task=importexport'));
		$tab->addChild($config);
	}
	
	/*
    	Function: isCommentsEnabled
    	  Checks wether comments are activated, globally and item specific.

	   Returns:
	      Boolean.
 	*/
	public function isCommentsEnabled() {
		return $this->getParams()->get('global.comments.enable_comments', 1);
	}	
	
}

/*
	Class: ApplicationException
*/
class ApplicationException extends YException {}