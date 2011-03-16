<?php
/**
* @package   ZOO Component
* @file      table.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YTable
		The table class, to load objects from database.
*/
abstract class YTable {

	protected $_dbo;
	protected $_class;
	protected $_table;
	protected $_table_key;
	protected $_table_fields;
	protected $_objects = array();
	protected static $_tables = array();

	protected function __construct($class, $table, $table_key) {
		$this->_dbo = YDatabase::getInstance();
		$this->_class = $class;
		$this->_table = $table;
		$this->_table_key = $table_key;
	}

	public static function getInstance($name) {
		
		if (!isset(self::$_tables[$name])) {
			$classname = $name.'Table';
			self::$_tables[$name] = new $classname();
		}

		return self::$_tables[$name];
	}

	public function getDBO() {
		return $this->_dbo;
	}

	public function getClassName() {
		return $this->_class;
	}

	public function getTableName() {
		return $this->_table;
	}

	public function getTableFields() {
		
		// get database
		$db = $this->getDBO();
		
		if (empty($this->_table_fields)) {
			$this->_table_fields = array_shift($db->getTableFields($this->getTableName()));
		}
		
		return $this->_table_fields;
	}

	public function getKeyName() {
		return $this->_table_key;
	}

	public function get($key, $new = false) {
		$options = array('conditions' => array($this->getKeyName().' = ?', $key));

		// get new object
		if ($new) {
			return $this->find('first', $options);
		}
		
		// get saved object instance
		if (!isset($this->_objects[$key])) {
			$this->_objects[$key] = $this->find('first', $options);
		}
		
		return $this->_objects[$key];
	}

	public function first($options = null) {
		return $this->find('first', $options);
	}

	public function all($options = null) {
		return $this->find('all', $options);
	}

	public function find($mode = 'all', $options = null) {
		
		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);
		
		if ($mode == 'first') {
			return $this->_queryObject($query);
		}
		
		return $this->_queryObjectList($query);
	}

	public function count($options = null) {

		$options = is_array($options) ? $options : array();
		$query   = $this->_select($options);

		$db = $this->getDBO();
		$db->query($query);
		
		return $db->getNumRows();
	}

	public function save($object) {

		// get database
		$db = $this->getDBO();

		// init vars
		$vars = get_object_vars($object);
		$fields = $this->getTableFields();
		
		foreach ($fields as $key => $value) {
			$fields[$key] = array_key_exists($key, $vars) ? $vars[$key] : null;
		}
		
		// insert or update database
		$obj = (object) $fields;
		$key = $this->getKeyName();		
		if ($obj->$key) {

			// update object
			$db->updateObject($this->getTableName(), $obj, $key);

		} else {

			// insert object
			$db->insertObject($this->getTableName(), $obj, $key);

			// set insert id
			$object->$key = $obj->$key;
		}

	}

	public function delete($object) {

		// get database and table key
		$db = $this->getDBO();
		$key = $this->getKeyName();
		
		// delete object
		$query = 'DELETE FROM '.$this->getTableName().
				 ' WHERE '.$key.' = '.$db->getEscaped($object->$key);

		return $this->_query($query);
	}

	protected function _select(array $options) {

		// get database
		$db = $this->getDBO();

		// select
		$query[] = sprintf('SELECT %s', isset($options['select']) ? $options['select'] : '*');
		
		// from
		$query[] = sprintf('FROM %s', isset($options['from']) ? $options['from'] : $this->getTableName());
		
		// where
		if (isset($options['conditions'])) {
			$condition  = '';
			$conditions = (array) $options['conditions'];
			
			// parse condition
			$parts = explode('?', array_shift($conditions));
			foreach ($parts as $part) {
				$condition .= $part.$db->getEscaped(array_shift($conditions));
			}
			
			if (!empty($condition)) {
				$query[] = sprintf('WHERE %s', $condition);
			}
		}

		// group by
		if (isset($options['group'])) {
			$query[] = sprintf('GROUP BY %s', $options['group']);

			if (isset($options['having'])) {
				$query[] = sprintf('HAVING %s', $options['having']);
			}
		}

		// order
		if (isset($options['order'])) {
			$query[] = sprintf('ORDER BY %s', $options['order']);
		}

		// offset & limit
		if (isset($options['offset']) || isset($options['limit'])) {
			$offset  = isset($options['offset']) ? (int) $options['offset'] : 0;
			$limit   = isset($options['limit']) ? (int) $options['limit'] : 0;
			$query[] = sprintf('LIMIT %s, %s', $offset, $limit);
		}

		return implode(' ', $query);
	}

	protected function _query($query) {
		
		// get database
		$db = $this->getDBO();
		
		return $db->query($query);
	}

	protected function _queryResult($query) {

		// get database
		$db = $this->getDBO();

		return $db->queryResult($query);
	}

	protected function _queryObject($query) {

		// query database
		$result = $this->_dbo->query($query);

		// fetch object and execute init callback
		$object = null;
		if ($object = $this->_dbo->fetchObject($result, $this->_class)) {
			$object = $this->_initObject($object);
		}

		$this->_dbo->freeResult($result);
		return $object;
	}

	protected function _queryObjectList($query) {

		// query database
		$result = $this->_dbo->query($query);

		// fetch objects and execute init callback
		$objects = array();
		while ($object = $this->_dbo->fetchObject($result, $this->_class)) {
			$objects[$object->{$this->_table_key}] = $this->_initObject($object);
		}

		$this->_dbo->freeResult($result);
		return $objects;
	}

	protected function _initObject($object) {
		return $object;
	}

}

/*
	Class: YTableException
*/
class YTableException extends YException {}