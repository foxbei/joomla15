<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: Submission
		Submission related attributes and functions.
*/
class Submission extends YObject {

    /*
       Variable: id
         Submission id.
    */
	public $id;

    /*
       Variable: identifier
         Submission unique identifier.
    */
	public $application_id;

    /*
       Variable: name
         Submission name.
    */
	public $name;

    /*
        Variable: alias
          Submission alias.
    */
	public $alias;

    /*
       Variable: state
         Submission published state.
    */
	public $state = 0;

    /*
       Variable: access
         Submission access level.
    */
	public $access;

    /*
       Variable: params
         Submission params.
    */
	public $params;

    /*
       Variable: _params
         YParameter object.
    */
	protected $_params;

    /*
       Variable: _types
         Related Type objects.
    */
	protected $_types = array();

    /*
       Variable: _application
         Application object.
    */
    protected $_application;

	/*
    	Function: canAccess
    	  Check if item is accessible with users access rights.

	   Returns:
	      Boolean - True, if access granted
 	*/
	public function canAccess($user) {
		return $user->get('aid', 0) >= $this->access;
	}

	/*
    	Function: getState
    	  Get published state.

	   Returns:
	      -
 	*/
	public function getState() {
		return $this->state;
	}

	/*
    	Function: setState
    	  Set published state.

	   Returns:
	      -
 	*/
	public function setState($val) {
		$this->state = $val;
	}

	/*
		Function: getParams
			Gets submission params.

		Returns:
			Object - JParameter
	*/
	public function getParams() {

		// get params
		if (empty($this->_params)) {
			$this->_params = new YParameter();
			$this->_params->loadString($this->params);
		}

		return $this->_params;
	}

	/*
		Function: getTypes
			Gets submission types.

		Returns:
			array - types
	*/
    public function getTypes() {
        if (empty($this->_types)) {
            $application = YTable::getInstance('application')->get($this->application_id);
            foreach (array_keys($this->getParams()->get('form.', array())) as $type_id) {
				if ($type = $application->getType($type_id)) {
					$this->_types[$type_id] = $type;
				}
            }
        }
        
        return $this->_types;
    }

	/*
		Function: getType
			Retrieve submission type by id.

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
		Function: getSubmittableTypes
			Gets submissions submittable types.

		Returns:
			array - types
	*/
    public function getSubmittableTypes() {
        $types = $this->getTypes();
        $result = array();
        foreach ($types as $type) {
			if ($form = $this->getForm($type->id)) {
				$layout = $form->get('layout');
				if (!empty($layout)) {
					$result[$type->id] = $type;
				}
			}
        }
        return $result;
    }

	/*
		Function: getForm
			Retrieve submission parameters for a type.

		Parameters:
  			$type_id - Type id.

		Returns:
			Type
	*/
    public function getForm($type_id) {
        return new YArray($this->getParams()->get('form.'.$type_id, array()));
    }

	/*
		Function: getApplication
			Get related application object.

		Returns:
			Application - application object
	*/
	public function getApplication() {
 		// get params
		if (empty($this->_application)) {
			$this->_application = YTable::getInstance('application')->get($this->application_id);
		}

		return $this->_application;
	}

	/*
		Function: isInTrustedMode
			Is this submission in trusted mode?

		Returns:
			Bool
	*/
    public function isInTrustedMode() {
        return (bool) $this->getParams()->get('trusted_mode', false);
    }

	/*
		Function: showTooltip
			Show tooltip?

		Returns:
			Bool
	*/
    public function showTooltip() {
        return (bool) $this->getParams()->get('show_tooltip', true);
    }

}

/*
	Class: SubmissionException
*/
class SubmissionException extends YException {}