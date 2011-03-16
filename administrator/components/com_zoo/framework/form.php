<?php
/**
* @package   ZOO Component
* @file      form.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YForm
		The form class
*/
class YForm implements ArrayAccess, Iterator, Countable, Serializable {

    protected $_form_fields   = array();
    protected $_is_bound      = false;
    protected $_count         = 0;
    	
    public function __construct($args = array()) {
        $this->config($args);
    }

    public function config($args = array()) {}

    public function getFormField($name) {
        return isset($this->_form_fields[$name]) ? $this->_form_fields[$name] : new YFormField($name);
    }

    public function getFormFields() {
        return $this->_form_fields;
    }

    public function setFormFields($form_fields = array()) {
        foreach ($form_fields as $name => $field) {
            $this[$name] = $field;
        }
        return $this;
    }

    public function addFormField(YFormField $form_field) {
        $this[$form_field->getName()] = $form_field;
        return $this;
    }

    public function hasFormField($name) {
        return isset($this->_form_fields[$name]);
    }

    public function bind($data) {

        if (is_array($data)) {
            foreach ($this as $name => $field) {
                $value = isset($data[$name]) ? $data[$name] : null;
                $field->bind($value);
            }
        } else if(is_object($data)) {
            foreach ($this as $name => $field) {
                $value = isset($data->$name) ? $data->$name : null;
                $field->bind($value);
            }
        }

        $this->_is_bound = true;
    }

    public function isBound() {
        return $this->_is_bound;
    }

    public function isValid() {
        $valid = true;
        foreach ($this as $field) {
            if ($field->hasError()) {
                $valid = false;
                break;
            }
        }
        return $this->isBound() && $valid;
    }

    public function getValue($name) {
        return isset($this[$name]) ? $this[$name]->getValue() : null;
    }

    public function getTaintedValue($name) {
        return isset($this[$name]) ? $this[$name]->getTaintedValue() : null;
    }

    public function hasError($name) {
        return isset($this[$name]) ? $this[$name]->hasError() : false;
    }

    public function getError($name) {
        return isset($this[$name]) ? $this[$name]->getError() : null;
    }

    public function getErrors() {
        $errors = array();
        foreach($this as $self) {
            if ($self->hasError()) {
                $errors[] = $self->getError();
            }
        }
        return $errors;
    }

    public function setIgnoreErrors($bool = false) {
        foreach($this as $self) {
            $self->setIgnoreErrors($bool);
        }
        return $this;
    }

    public function offsetSet($offset, $value) {
        $this->_form_fields[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->_form_fields[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_form_fields[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_form_fields[$offset]) ? $this->_form_fields[$offset] : null;
    }

    public function count() {
        return count($this->_form_fields);
    }

    public function rewind() {
        reset($this->_form_fields);

        $this->_count = count($this->_form_fields);
    }

    public function key() {
        return key($this->_form_fields);
    }

    public function current() {
        return current($this->_form_fields);
    }

    public function next() {
        next($this->_form_fields);

        --$this->_count;
    }

    public function valid() {
        return $this->_count > 0;
    }

    public function serialize() {
        $serialized = array();

        foreach(array_keys(get_class_vars(__CLASS__)) as $key) {
            $serialized[$key] = $this->$key;
        }
        return serialize($serialized);
    }

    public function unserialize($serialized) {
        $data = unserialize($serialized);

        foreach($data as $prop => $val) {
            $this->$prop = $val;
        }

        return true;
    }
	
}

/*
	Class: YFormField
		The YFormField class
*/
class YFormField {
	
    protected $_name;
    protected $_tainted_value;
    protected $_value;
    protected $_validator;
    protected $_error;
    protected $_ignore_errors = false;

    public function __construct($name, $validator = null) {
        $this->_name = $name;
        $this->_validator = $validator ? $validator : new YValidatorPass();
    }

    public function bind($value) {

        $this->_tainted_value = $value;

        try {

            $this->_value = $this->_ignore_errors ? $value : $this->getValidator()->clean($value);

        } catch (YValidatorException $e) {

            $this->setError($e);

        }

    }

    public function getName() {
        return $this->_name;
    }

    public function getValue() {
        return $this->_value;
    }

    public function getTaintedValue() {
        return $this->_tainted_value;
    }

    public function getValidator() {
        return $this->_validator;
    }

    public function getError() {
        return $this->_error;
    }

    public function setError(YValidatorException $e) {
        $this->_error = $e;
    }

    public function hasError() {
        return $this->_ignore_errors ? false : count($this->_error);
    }

    public function getIgnoreErrors() {
        return $this->_ignore_errors;
    }

    public function setIgnoreErrors($bool = false) {
        $this->_ignore_errors = $bool;
        return $this;
    }
	
}