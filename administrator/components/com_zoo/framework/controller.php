<?php
/**
* @package   ZOO Component
* @file      controller.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YController
		The Controller Class. Extends Joomla's JController functionality.
*/
class YController extends JController {

	public $joomla;
	public $user;
	public $session;
	public $document;
	public $dispatcher;
	public $params;
	public $pathway;
	public $option;
	public $link_base;
	public $controller;
	public $baseurl;
	protected static $_views = array();
	
 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			YController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// init vars
		$this->joomla     = JFactory::getApplication();
		$this->user       = JFactory::getUser();
		$this->session    = JFactory::getSession();
		$this->document   = JFactory::getDocument();
		$this->dispatcher = JDispatcher::getInstance();	
		$this->option     = YRequest::getCmd('option');
		$this->link_base  = 'index.php?option='.$this->option;
		$this->controller = $this->getName();

		// add super administrator var to user
		$this->user->superadmin = UserHelper::isJoomlaSuperAdmin($this->user);
		
		// init additional admin vars
		if ($this->joomla->isAdmin()) {
			$this->baseurl = 'index.php?option='.$this->option.'&controller='.$this->getName();
		}
		
		// init additional site vars
		if ($this->joomla->isSite()) {
			$this->itemid  = (int) $GLOBALS['Itemid'];
			$this->params  = $this->joomla->getParams();
			$this->pathway = $this->joomla->getPathway();
		}
	}

	/*
		Function: getView
			Method to get a reference to the current view and load it if necessary.

		Parameters:
			$name - The view name. Optional, defaults to the controller
			$type - The view type. Optional.
			$prefix - The class prefix. Optional.
			$config - Configuration array for view. Optional.

		Returns:
			YView
	*/
	public function getView($name = '', $type = '', $prefix = '', $config = array()) {

		// set name
		if (empty($name)) {
			$name = $this->getName();
		}

		// set prefix
		if (empty($prefix)) {
			$prefix = $this->getName().'View';
		}

		// clean vars
		$name   = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
		$type   = preg_replace('/[^A-Z0-9_]/i', '', $type);

		// create view
		if (empty($this->_views[$name])) {

			$class  = $prefix.$name;
			$file   = strtolower($name).'/view'.(!empty($type) ? '.'.$type : null).'.php';
			$config = array_merge(array('name' => $name, 'base_path' => $this->_basePath), $config);

			// load view class, if not exists
			if (!class_exists($class)) {
				if ($path = JPath::find($this->_path['view'], $file)) {
					require_once($path);
				}
			}

			// set default view, if view class is not loaded
			if (!class_exists($class)) {
				$class = 'yview';
			}

			$this->_views[$name] = new $class($config);
		}

		// automatically pass all public class variables on to view
		foreach (get_object_vars($this) as $var => $value) {
			if (substr($var, 0, 1) != '_') {
				$this->_views[$name]->$var = $value;
			}
		}
		
		return $this->_views[$name];
	}

}

/*
	Class: YControllerException
*/
class YControllerException extends YException {}