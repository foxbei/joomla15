<?php
/**
* @package   ZOO Component
* @file      itemform.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ItemForm
		The class for validating item submissions.
*/
class ItemForm extends YForm {

    protected $_item;
    protected $_submission;
    protected $_elements_config = array();

    public function config($args = array()) {

        parent::config($args);

        // set item
        $this->_item = isset($args['item']) ? $args['item'] : null;

        // set submission
        $this->_submission = isset($args['submission']) ? $args['submission'] : null;

        // set elements
        $this->_elements_config = isset($args['elements_config']) ? $args['elements_config'] : array();

        // set submission form fields
        $this->setFormFields(
            array(
                'name'            => new YFormField('name', new YValidatorString()),
                'state'           => new YFormField('state', new YValidator(array('required' => false))),
				'publish_up'      => new YFormField('publish_up', new YValidatorDate(array('required' => false, 'allow_db_null_date' => true))),
				'publish_down'    => new YFormField('publish_down', new YValidatorDate(array('required' => false, 'allow_db_null_date' => true))),
                'searchable'      => new YFormField('searchable', new YValidator(array('required' => false))),
                'enable_comments' => new YFormField('enable_comments', new YValidator(array('required' => false))),
                'frontpage'       => new YFormField('frontpage', new YValidator(array('required' => false))),
                'categories'      => new YFormField('categories', new YValidator(array('required' => false))),
                'tags'            => new YFormField('tags', new YValidatorForeach(new YValidatorString(), array('required' => false)))
            )
        );
        
        foreach (array_keys($this->_elements_config) as $identifier) {
            $this->addFormField(new YFormFieldElement($identifier, $this));
        }
        
    }

    public function bindItem() {
        $data = array();
        if ($this->_item) {

			$tzoffset = JFactory::getConfig()->getValue('config.offset');
			$null = YDatabase::getInstance()->getNullDate();

            $data['name']            = $this->_item->name;
            $data['state']           = $this->_item->state;

			$publish_up = $this->_item->publish_up;
			if ($publish_up != $null) {
				$publish_up = JFactory::getDate($publish_up, -$tzoffset)->toMySQL();
			}
			$data['publish_up']      = $publish_up;

			$publish_down = $this->_item->publish_down;
			if ($publish_down != $null) {
				$publish_down = JFactory::getDate($publish_down, -$tzoffset)->toMySQL();
			}
			$data['publish_down']    = $publish_down;
			
            $data['searchable']      = $this->_item->searchable;
            $data['enable_comments'] = $this->_item->isCommentsEnabled();
			$related_categories		 = $this->_item->getRelatedCategoryIds();
            $data['frontpage']       = in_array(0, $related_categories);
            $data['categories']      = $related_categories;
            $data['tags']            = $this->_item->getTags();

            foreach (array_keys($this->_elements_config) as $identifier) {
                if ($element = $this->_item->getElement($identifier)) {
					if (is_subclass_of($element, 'ElementRepeatable')) {
						$element->rewind();
						foreach ($element as $index => $instance) {
							if (is_a($element, 'ElementDate')) {
								$value = $instance->getElementData()->get('value');
								if (!empty($value)) {
									$value = JFactory::getDate($value, -$tzoffset)->toMySQL();
								}
								$data[$identifier][$index]['value'] = $value;
							} else {
								$data[$identifier][$index] = $instance->getElementData()->getParams()->toArray();
							}
						}
					} else {
						$data[$identifier] = $element->getElementData()->getParams()->toArray();
					}
				}
            }
        }
        parent::bind($data);
    }

    public function setItem($item) {
        $this->_item = $item;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function getElementConfig($identifier) {
        if (isset($this->_elements_config[$identifier])) {
            return (array) $this->_elements_config[$identifier];
        }
        return array();
    }

    public function setSubmission($submission) {
        $this->_submission = $submission;
        return $this;
    }

    public function getSubmission() {
        return $this->_submission;
    }
	
}

/*
	Class: YFormFieldElement
		The YFormFieldElement class.
*/
class YFormFieldElement extends YFormField {

    protected $_form;

    public function __construct($name, $form) {
        parent::__construct($name);
        $this->_form = $form;
    }

    public function bind($value) {

        // bind unmodified value
        $this->_tainted_value = $value;

        try {

			// get element
			if ($element = $this->_form->getItem()->getElement($this->_name)) {

				// get params
				$params = new YArray(array_merge(array('trusted_mode' => $this->_form->getSubmission()->isInTrustedMode()), $this->_form->getElementConfig($this->_name)));

				// get YArray value
				$value = $value ? new YArray($value) : new YArray();

				// validate the element
				$this->_value = $this->_ignore_errors ? $value : $element->validateSubmission($value, $params);

			}

        } catch (YValidatorException $e) {

            $this->setError($e);

        }

    }
    
}