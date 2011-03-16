<?php
/**
* @package   ZOO Component
* @file      validator.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: YValidator
		Validator Base Class.
*/
class YValidator {

	const ERROR_CODE_REQUIRED = 100;

    protected $_messages = array();
    protected $_options  = array();

    public function __construct($options = array(), $messages = array()) {
        $this->_options  = array_merge(array('required' => true, 'trim' => false, 'empty_value' => null), $this->_options);
        $this->_messages = array_merge(array('required' => JText::_('This field is required.'), 'invalid' => JText::_('Invalid.')), $this->_messages);

        $this->_configure($this->_options, $this->_messages);

 	    $this->_options  = array_merge($this->_options, $options);
 	    $this->_messages = array_merge($this->_messages, $messages);

    }

    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('invalid',  'Invalid.');
    }

    public function clean($value) {
        $clean = $value;

        if ($this->getOption('trim') && is_string($clean)) {
            $clean = JString::trim($clean);
        }

        if ($this->isEmpty($clean)) {
            if ($this->getOption('required')) {
                throw new YValidatorException($this->getMessage('required'), self::ERROR_CODE_REQUIRED);
            }

            return $this->getEmptyValue();
        }

        return $this->_doClean($clean);
    }

    public function addMessage($name, $value = null) {
        $this->_messages[$name] = $value;

        return $this;
    }

    public function getMessage($name) {
        return isset($this->_messages[$name]) ? $this->_messages[$name] : '';
    }

    protected function isEmpty($value) {
        return in_array($value, array(null, '', array()), true);
    }

    public function hasOption($name) {
        return isset($this->_options[$name]);
    }

    public function getOption($name) {
        if ($this->hasOption($name)) {
            return $this->_options[$name];
        }
        return null;
    }

    public function addOption($name, $value = null) {
        $this->_options[$name] = $value;
        return $this;
    }

    public function setOptions($options = array()) {
        $this->_options = $options;
        return $this;
    }

    public function getEmptyValue() {
        return $this->getOption('empty_value');
    }

    protected function _doClean($value) {
        return $value;
    }

}

/*
	Class: YValidatorPass
		YValidatorPass Class.
*/
class YValidatorPass extends YValidator {

    public function clean($value) {
        return $this->_doClean($value);
    }

    protected function _doClean($value) {
        return $value;
    }
}

/*
	Class: YValidatorString
		YValidatorString Class.
*/
class YValidatorString extends YValidator {

    protected function _doClean($value) {
        $clean = (string) $value;

        return $clean;
    }

    public function getEmptyValue() {
        return '';
    }

}

/*
	Class: YValidatorInteger
		YValidatorInteger Class.
*/
class YValidatorInteger extends YValidator {

    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not an integer.');
    }

    protected function _doClean($value) {

        $clean = intval($value);

        if (strval($clean) != $value) {
            throw new YValidatorException($this->getMessage('number'));
        }

        return $clean;
    }

    public function getEmptyValue() {
        return 0;
    }

}

/*
	Class: YValidatorNumber
		YValidatorNumber Class.
*/
class YValidatorNumber extends YValidator {

    protected function _configure($options = array(), $messages = array()) {
        $this->addMessage('number', 'This is not a number.');
    }

    protected function _doClean($value) {

        if (!is_numeric($value)) {
            throw new YValidatorException($this->getMessage('number'));
        }

        $clean = floatval($value);

        return $clean;
    }

    public function getEmptyValue() {
        return 0.0;
    }

}

/*
	Class: YValidatorFile
		YValidatorFile Class.
*/
class YValidatorFile extends YValidator {

    protected function _configure($options = array(), $messages = array()) {
        if (!ini_get('file_uploads')) {
            throw new YValidatorException('File uploads are disabled.');
        }

		$this->addOption('max_size');
        $this->addOption('mime_types');
		$this->addOption('mime_type_group');
		$this->addOption('extension');

		$this->addMessage('extension', 'This is not a valid extension.');
        $this->addMessage('max_size', 'File is too large (max %s KB).');
        $this->addMessage('mime_types', 'Invalid mime type.');
		$this->addMessage('mime_type_group', 'Invalid mime type.');
        $this->addMessage('partial', 'The uploaded file was only partially uploaded.');
        $this->addMessage('no_file', 'No file was uploaded.');
        $this->addMessage('no_tmp_dir', 'Missing a temporary folder.');
        $this->addMessage('cant_write', 'Failed to write file to disk.');
        $this->addMessage('err_extension', 'File upload stopped by extension.');

    }

    public function clean($value) {
        if (!is_array($value) || !isset($value['tmp_name'])) {
			throw new YValidatorException($this->getMessage('invalid'));
        }

        if (!isset($value['name'])) {
			$value['name'] = '';
        }

        $value['name'] = JFile::makeSafe($value['name']);

        if (!isset($value['error'])) {
			$value['error'] = UPLOAD_ERR_OK;
        }

        if (!isset($value['size'])) {
			$value['size'] = filesize($value['tmp_name']);
        }

        if (!isset($value['type'])) {
            $value['type'] = 'application/octet-stream';
        }

        switch ($value['error']) {
			case UPLOAD_ERR_INI_SIZE:
				throw new YValidatorException($this->getMessage('max_size'), UPLOAD_ERR_INI_SIZE);
			case UPLOAD_ERR_FORM_SIZE:
				throw new YValidatorException($this->getMessage('max_size'), UPLOAD_ERR_FORM_SIZE);
			case UPLOAD_ERR_PARTIAL:
				throw new YValidatorException($this->getMessage('partial'), UPLOAD_ERR_PARTIAL);
			case UPLOAD_ERR_NO_FILE:
				throw new YValidatorException($this->getMessage('no_file'), UPLOAD_ERR_NO_FILE);
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new YValidatorException($this->getMessage('no_tmp_dir'), UPLOAD_ERR_NO_TMP_DIR);
			case UPLOAD_ERR_CANT_WRITE:
				throw new YValidatorException($this->getMessage('cant_write'), UPLOAD_ERR_CANT_WRITE);
			case UPLOAD_ERR_EXTENSION:
				throw new YValidatorException($this->getMessage('err_extension'), UPLOAD_ERR_EXTENSION);
        }

        // check mime type
        if ($this->hasOption('mime_types')) {
            $mime_types = $this->getOption('mime_types') ? $this->getOption('mime_types') : array();
            if (!in_array($value['type'], array_map('strtolower', $mime_types))) {
                throw new YValidatorException($this->getMessage('mime_types'));
            }
        }

		// check mime type group
		if ($this->hasOption('mime_type_group')) {
			if (!in_array($value['type'], $this->_getGroupMimeTypes($this->getOption('mime_type_group')))) {
                throw new YValidatorException($this->getMessage('mime_type_group'));
            }
		}

        // check file size
        if ($this->hasOption('max_size') && $this->getOption('max_size') < (int) $value['size']) {
			throw new YValidatorException(sprintf($this->getMessage('max_size'), ($this->getOption('max_size') / 1024)));
        }

		// check extension
		if ($this->hasOption('extension') && !in_array(YFile::getExtension($value['name']), $this->getOption('extension'))) {
			throw new YValidatorException($this->getMessage('extension'));
        }

        return $value;
    }

	protected function _getGroupMimeTypes($group) {
		$mime_types = new YArray(YFile::getMimeMapping());
		$mime_types = $mime_types->flattenRecursive();
		$mime_types = array_filter($mime_types, create_function('$a', 'return preg_match("/^'.$group.'\//i", $a);'));
		return array_map('strtolower', $mime_types);
	}

}

/*
	Class: YValidatorDate
		YValidatorDate Class.
*/
class YValidatorDate extends YValidatorString {

    protected function _configure($options = array(), $messages = array()) {
		$this->addOption('date_format_regex', '/^((\d{2}|\d{4}))-(\d{1,2})-(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?$/');
		$this->addOption('date_format', '%Y-%m-%d %H:%M:%S');
		$this->addOption('allow_db_null_date', false);
		$this->addOption('db_null_date', YDatabase::getInstance()->getNullDate());
		$this->addMessage('bad_format', '"%s" is not a valid date.');
	}

    protected function _doClean($value) {

		// init vars
		$value = parent::_doClean($value);

		if (!preg_match($this->getOption('date_format_regex'), $value)) {
			throw new YValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		if ($this->getOption('allow_db_null_date') && $value == $this->getOption('db_null_date')) {
			return $value;
		}

		$clean = strtotime($value);

		if (empty($clean)) {
			throw new YValidatorException(sprintf($this->getMessage('bad_format'), $value));
		}

		$clean = strftime($this->getOption('date_format'), $clean);
		return $clean;

    }

}

/*
	Class: YValidatorRegex
		YValidatorRegex Class.
*/
abstract class YValidatorRegex extends YValidatorString {

    protected function _doClean($value) {

        $clean = parent::_doClean($value);

        if ($pattern = $this->getPattern()) {
            if (!preg_match($pattern, $clean)) {
                throw new YValidatorException($this->getMessage('pattern'));
            }
        }

        return $clean;
    }

    public function setPattern($pattern) {
        $this->addOption('pattern', $pattern);
        return $this;
    }

    public function getPattern() {
        return $this->getOption('pattern');
    }

}

/*
	Class: YValidatorEmail
		YValidatorEmail Class.
*/
class YValidatorEmail extends YValidatorRegex {

    const REGEX_EMAIL = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

    protected function _configure($options = array(), $messages = array()) {
        $this->setPattern(self::REGEX_EMAIL);
        $this->addMessage('pattern', JText::_('Please enter a valid email address.'));
    }

}

/*
	Class: YValidatorUrl
		YValidatorUrl Class.
*/
class YValidatorUrl extends YValidatorRegex {

    const REGEX_URL ='/^(%s):\/\/(([a-z0-9-]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(\/?|\/\S+)$/i';

    protected function _configure($options = array(), $messages = array()) {
        $this->addOption('protocols', array('http', 'https', 'ftp', 'ftps'));
        $this->setPattern(sprintf(self::REGEX_URL, implode('|', $this->getOption('protocols'))));
        $this->addMessage('pattern', JText::_('Please enter a valid URL.'));
    }

}

/*
	Class: YValidatorForeach
		The YValidatorForeach class.
*/
class YValidatorForeach extends YValidator {

    protected $_validator;

    public function __construct($validator, $options = array(), $messages = array()) {

        parent::__construct($options, $messages);

        $this->_validator = $validator;

    }

    public function getValidator() {

        if (!$this->_validator) {
            $this->_validator = new YValidatorPass();
        }

        return $this->_validator;

    }

    protected function _doClean($value) {
        $clean = array();

        if (is_array($value)) {

            foreach ($value as $key => $single_value) {
                $clean[$key] = $this->getValidator()->clean($single_value);
            }

        } else {
            throw new YValidatorException($this->getMessage('invalid'));
        }

        return $clean;

    }

    public function getEmptyValue() {
        return array();
    }

}

/*
	Class: YValidatorException
*/
class YValidatorException extends YException {}