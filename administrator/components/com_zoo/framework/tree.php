<?php
/**
* @package   ZOO Component
* @file      tree.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YTree
		Base class for nested tree structures.
*/
class YTree {

	protected $_root;
	protected $_itemclass;
	protected $_filters = array();

	/*
		Function: __construct
			Constructor

		Parameters:
			$itemclass - Tree item class name

		Returns:
			YTree
	*/
	public function __construct($itemclass = null) {
		
		if ($itemclass == null) {
			$itemclass = get_class($this).'Item';
		}

		$this->_root = new $itemclass();
		$this->_itemclass = $itemclass;
	}

	/*
		Function: addFilter
			Add a tree filter

		Parameters:
			$filter - Filter callback method
			$args - Arguments for filter callback

		Returns:
			YTree
	*/
    public function addFilter($filter, $args = array()) {
		$this->_filters[] = compact('filter', 'args');
		return $this;
    }

	/*
		Function: applyFilter
			Execute all filters on all tree items recursively

		Returns:
			YTree
	*/
    public function applyFilter() {

		foreach ($this->_filters as $filter) {
			$this->_root->filter($filter['filter'], $filter['args']);
		}

		return $this;
    }

	/*
		Function: __call
			Delegate method calls to root tree item

		Returns:
			Mixed
	*/
    public function __call($method, $args) {
        return call_user_func_array(array($this->_root, $method), $args);
    }

}

/*
	Class: YTreeItem
		Base class for a tree item.
*/
class YTreeItem {

	protected $_parent;
	protected $_children = array();

	/*
		Function: getID
			Retrieve tree item identifier (unique object hash)

		Returns:
			String
	*/
	public function getID() {
		return spl_object_hash($this);
	}

	/*
		Function: getParent
			Retrieve parent tree item

		Returns:
			YTreeItem
	*/
	public function getParent() {
		return $this->_parent;
	}

	/*
		Function: setParent
			Set parent tree item

		Parameters:
			$item - Tree item

		Returns:
			YTreeItem
	*/
	public function setParent(YTreeItem $item) {
		$this->_parent = $item;
		return $this;
	}

	/*
		Function: getChildren
			Retrieve tree items children

		Returns:
			Array
	*/
	public function getChildren() {
		return $this->_children;
	}

	/*
		Function: getChildren
			Retrieve tree items children

		Parameters:
			$id - Treen item identifier
			$recursive - Search rescursively ?

		Returns:
			Boolean
	*/
	public function hasChild($id, $recursive = false) {
		
		if (isset($this->_children[$id])) {
			return true;
		}

		if ($recursive) {
			foreach ($this->_children as $child) {
				if ($child->hasChild($id, $recursive)) return true;
			}
		}

		return false;		
	}	

	/*
		Function: hasChildren
			Retrieve tree items children count

		Returns:
			Int
	*/
	public function hasChildren() {
		return count($this->_children);
	}

	/*
		Function: addChild
			Add child to tree item

		Parameters:
			$item - Tree item

		Returns:
			YTreeItem
	*/
	public function addChild(YTreeItem $item) {

		$item->setParent($this);
		$this->_children[$item->getID()] = $item;

		return $this;
	}

	/*
		Function: addChildren
			Add multiple children to tree item

		Parameters:
			$children - Tree item array

		Returns:
			YTreeItem
	*/
	public function addChildren(array $children) {

		foreach($children as $child) {
			$this->addChild($child);
		}

		return $this;
	}

	/*
		Function: removeChild
			Remove child from tree item

		Parameters:
			$item - Tree item

		Returns:
			YTreeItem
	*/
	public function removeChild(YTreeItem $item) {

		$item->setParent(null);
		unset($this->_children[$item->getID()]);

		return $this;
	}

	/*
		Function: getPathway
			Path to from current tree item to tree root

		Returns:
			Array
	*/
	public function getPathway() {
		
		if ($this->_parent == null) {
			return array();
		}
		
		$pathway   = $this->_parent->getPathway();
		$pathway[] = $this;

		return $pathway;
	}	

	/*
		Function: filter
			Filter all tree items recursively, through callback method

		Returns:
			Void
	*/
	public function filter($callback, $args = array()) {
		
		// call filter function
		call_user_func_array($callback, array_merge(array($this), $args));

		// filter all children
		foreach ($this->getChildren() as $child) {
			$child->filter($callback, $args);
		}
	}	

}

/*
	Class: YTreeException
*/
class YTreeException extends YException {}