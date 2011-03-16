<?php
/**
* @package   ZOO Component
* @file      renderer.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Renderer
		The general class for rendering objects.
*/
class YRenderer {

	protected $_layout;
	protected $_path = array();
	protected $_folder = 'renderer';
	protected $_separator = '.';
	protected $_extension = '.php';
	protected $_metafile = 'metadata.xml';
	
	const MAX_RENDER_RECURSIONS = 100;

	/*
		Function: render
			Render objects using a layout file.

		Parameters:
			$layout - Layout name.
			$args - Arguments to be passed to into the layout scope.

		Returns:
			String
	*/
	public function render($layout, $args = array()) {

		// prevent render to recurse indefinitely
		static $count = 0;
		$count++;

		if ($count < self::MAX_RENDER_RECURSIONS) {	
		
			// init vars
			$parts = explode($this->_separator, $layout);
			$this->_layout = preg_replace('/[^A-Z0-9_\.-]/i', '', array_pop($parts));
	
			// render layout
			if ($__layout = JPath::find($this->_getPath(implode(DIRECTORY_SEPARATOR, $parts)), $this->_layout.$this->_extension)) {
	
				// import vars and layout output
				extract($args);
				ob_start();
				include($__layout);
				$output = ob_get_contents();
				ob_end_clean();
				
				$count--;
				
				return $output;
			}
	
			$count--;
			
			// raise warning, if layout was not found
			JError::raiseWarning(0, 'Renderer Layout "'.$layout.'" not found. ('.YUtility::debugInfo(debug_backtrace()).')');
	
			return null;
		}
		
		// raise warning, if render recurses indefinitly
		JError::raiseWarning(0, 'Warning! Render recursed indefinitly. ('.YUtility::debugInfo(debug_backtrace()).')');

		return null;		
	}
	
	/*
		Function: addPath
			Add layout path(s) to renderer.

		Parameters:
			$path - String or array of paths.

		Returns:
			Renderer
	*/	
	public function addPath($path) {
		settype($path, 'array');

		foreach ($path as $dir) {
			$dir = trim($dir);

			if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
				$dir .= DIRECTORY_SEPARATOR;
			}

			array_unshift($this->_path, $dir);
		}
		
		return $this;
	}

	/*
		Function: getLayouts
			Retrieve an array of layout filenames.
		
		Returns:
			Array
	*/
	public function getLayouts($dir) {

		// init vars
		$layouts = array();

		// find layouts in path(s)
		foreach ($this->_getPath($dir) as $path) {
			if (JFolder::exists($path)) {
				$files = JFolder::files($path, '\.php$');
			
				if (is_array($files)) {
					$layouts = array_merge($layouts, $files);
				}
			}
		}

		return array_map(create_function('$layout', 'return basename($layout, "'.$this->_extension.'");'), $layouts);
	}

	/*
		Function: getLayoutMetaData
			Retrieve metadata array of a layout.
		
		Returns:
			Array
	*/
	public function getLayoutMetaData($layout) {

		// init vars
		$metadata = new YArray();
		$parts    = explode($this->_separator, $layout);
		$name     = array_pop($parts);

		if ($file = JPath::find($this->_getPath(implode(DIRECTORY_SEPARATOR, $parts)), $this->_metafile)) {
			if ($xml = YXML::loadFile($file)) {
				foreach ($xml->children() as $child) {
					$attributes = $child->attributes();
					if ($child->getName() == 'layout' && (string) $attributes->name == $name) {

						foreach ($attributes as $key => $attribute) {
							$metadata[$key] = (string) $attribute;
						}
						
						$metadata['layout'] = $layout;
						$metadata['name'] = (string) $child->name;
						$metadata['description'] = (string) $child->description;

						break;
					}
				}
			}
		}
		
		return $metadata;
	}

	/*
		Function: getFolder
			Retrieve the renderers folder.
		
		Returns:
			String
	*/
	public function getFolder() {
		return $this->_folder;
	}

	/*
		Function: _getPath
			Retrieve paths where to find the layout files.
		
		Returns:
			Array
	*/
	protected function _getPath($dir) {
		settype($dir, 'array');

		// init vars
		$paths = array();

		// get paths to find the layout in
		foreach ($this->_path as $path) {
			foreach ($dir as $name) {
				$paths[] = $path.$this->_folder.DIRECTORY_SEPARATOR.strtolower($name);
			}
		}

		return $paths;
	}
	
}