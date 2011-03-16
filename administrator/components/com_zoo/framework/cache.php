<?php
/**
* @package   ZOO Component
* @file      cache.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class YCache {
	
	protected $_file  = 'config.txt';
	protected $_items = array();
	protected $_dirty = false;
	protected $_hash  = true;

	public function __construct($file, $hash = true, $lifetime = null) {

		// if cache file doesn't exist, create it
		if (!JFile::exists($file)) {
			JFolder::create(dirname($file));
			JFile::write($file, '');
		}
		
		// set file and parse it
		$this->_file = $file;
		$this->_hash = $hash;
		$this->_parse();
		
		// clear out of date values
		if ($lifetime) {
			$lifetime = (int) $lifetime;
			$remove = array();
			foreach($this->_items as $key => $value) {
				if ((time() - $value['timestamp']) > $lifetime) {
					$remove[] = $key;
				}
			}
			foreach($remove as $key) {
				unset($this->_items[$key]);
			}
			
		}
	}

	public function check() {
		return is_readable($this->_file) && is_writable($this->_file);
	}

	public function get($key) {
		if ($this->_hash) $key = md5($key);
		if (!array_key_exists($key, $this->_items)) return null;

		return $this->_items[$key]['value'];
	}

	public function set($key, $value) {
		if ($this->_hash) $key = md5($key);
		if (array_key_exists($key, $this->_items) && $this->_items[$key] == $value) return;

		$this->_items[$key]['value'] = $value;
		$this->_items[$key]['timestamp'] = time();
		$this->_dirty = true;
		return $this;
	}

	protected function _parse() {
		$content = JFile::read($this->_file);
		if (!empty($content)) {
			$this->_items = json_decode($content, true);
		}
		return $this;
	}

	public function save() {
		if ($this->_dirty) {
			JFile::write($this->_file, json_encode($this->_items));
		}
		return $this;
	}
	
	public function clear() {
		$this->_items = array();
		$this->_dirty = true;
		return $this;
	}
}

/*
	Class: YCacheException
*/
class YCacheException extends YException {}