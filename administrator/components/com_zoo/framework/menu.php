<?php
/**
* @package   ZOO Component
* @file      menu.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YMenu
		Simple menu class.
*/
class YMenu extends YTree {

	protected $_name;
	
	protected static $_menus = array();

	/*
		Function: __construct
			Constructor

		Parameters:
			$name - Menu name

		Returns:
			YMenu
	*/	
	public function __construct($name) {
		parent::__construct();
		
		$this->_name = $name;
	}
	
	/*
		Function: getInstance
			Get a menu instance

		Parameters:
			$name - Menu name

		Returns:
			YMenu
	*/	
	public static function getInstance($name) {
		
		if (isset(self::$_menus[$name])) {
			return self::$_menus[$name];
		}
		
		self::$_menus[$name] = new YMenu($name);
		
		return self::$_menus[$name];
	}

	/*
		Function: render
			Retrieve menu html output

		Returns:
			String
	*/	
	public function render() {
						
		// create html
		$html = '<ul>';
		foreach ($this->_root->getChildren() as $child) {
			$html .= $child->render($this);
		}
		$html .= '</ul>';

		// decorator callbacks ?
		if (func_num_args()) {

			// parse html
			if ($xml = YXML::loadString($html)) {
			
				foreach (func_get_args() as $callback) {
					if (is_callable($callback)) {
						$xml->map($callback);
					}
				}
				
				$html = $xml->asXML(true);
			}
		}
		
		return $html;
	}

}

/*
	Class: YMenuItem
		Simple menu item class.
*/
class YMenuItem extends YTreeItem {

	protected $_id;
	protected $_name;	
	protected $_link;
	protected $_attributes;
	
	/*
		Function: __construct
			Constructor

		Parameters:
			$id - Identifier
			$name - Item name
			$link - Item link
			$attributes - Tag attributes

		Returns:
			YMenuItem
	*/	
	public function __construct($id = null, $name = '', $link = null, array $attributes = array()) {
		$this->_id		   = $id;
		$this->_name 	   = $name;
		$this->_link 	   = $link;
		$this->_attributes = $attributes;
	}

	/*
		Function: getName
			Retrieve menu item name

		Returns:
			Mixed
	*/	
	public function getName() {
		return $this->_name;
	}		
	
	/*
		Function: setName
			Set a menu item name

		Returns:
			YMenuItem
	*/	
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}
	
	/*
		Function: getID
			Retrieve menu item identifier

		Returns:
			Mixed
	*/	
	public function getID() {
		return $this->_id ? $this->_id : parent::getId();
	}	

	/*
		Function: getAttribute
			Retrieve menu item attribute

		Returns:
			Mixed
	*/	
	public function getAttribute($key) {

		if (isset($this->_attributes[$key])) {
			return $this->_attributes[$key];
		}

		return null;
	}

	/*
		Function: setAttribute
			Set a menu item attribute

		Returns:
			YMenuItem
	*/	
	public function setAttribute($key, $value) {
		$this->_attributes[$key] = $value;
		return $this;
	}
	
	/*
		Function: render
			Retrieve menu item html output

		Returns:
			String
	*/	
	public function render() {
		
		$link   = YRequest::getVar('hidemainmenu') ? null : $this->_link;
		$html[] = '<li '.JArrayHelper::toString($this->_attributes).'>';
		$html[] = ($link ? '<a href="'.JRoute::_($link).'">' : '<span>').'<span>'.JText::_($this->getName()).'</span>'.($link ? '</a>' : '</span>');

		if (count($this->getChildren())) {
			$html[] = '<ul>';
			foreach ($this->getChildren() as $child) {		
				$html[] = $child->render();		
			}
			$html[] = '</ul>';
		}

		$html[] = '</li>';
				
		return implode("\n", $html);
	}
	
}

/*
	Class: YMenuDecorator
		Decorator for menu class.
*/
class YMenuDecorator {

	/*
		Function: index
			Add item index and level to class attribute

		Parameters:
			$node - XML node
			$args - Callback arguments

		Returns:
			Void
	*/	
	public static function index(YXMLElement $node, $args) {
		
		if ($node->getName() == 'ul') {
			
			// set ul level
			$level = ($args['level'] / 2) + 1;
			$node->addAttribute('class', trim($node->attributes()->class.' level'.$level));

			// set order/first/last for li
			$count = count($node->children());
			foreach ($node->children() as $i => $child) {
				$child->addAttribute('level', $level);
				$child->addAttribute('order', $i + 1);
				if ($i == 0) $child->addAttribute('first', 1);
				if ($i == $count - 1) $child->addAttribute('last', 1);
			}

		}

		if ($node->getName() == 'li') {

			// level and item order
			$css  = 'level'.$node->attributes()->level;
			$css .= ' item'.$node->attributes()->order;
	
			// first, last and parent
			if ($node->attributes()->first) $css .= ' first';
			if ($node->attributes()->last)  $css .= ' last';
			if (isset($node->ul))           $css .= ' parent';

			// add li css classes
			$node->addAttribute('class', trim($node->attributes()->class.' '.$css));

			// add a/span css classes
			if ($firstChild = $node->firstChild()) {
				$firstChild->addAttribute('class', trim($firstChild->attributes()->class.' '.$css));
			}
		}

		$node->removeAttribute('level')
			->removeAttribute('order')
			->removeAttribute('first')
			->removeAttribute('last');
	}

}