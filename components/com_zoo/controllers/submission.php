<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: SubmissionController
		Site submission controller class
*/
class SubmissionController extends YController {

	/*
       Class constants
    */
	const SESSION_PREFIX   = 'ZOO_';
    const PAGINATION_LIMIT = 20;
	const TIME_BETWEEN_PUBLIC_SUBMISSIONS = 300;
	const EDIT_DATE_FORMAT = '%Y-%m-%d %H:%M:%S';
	const CALENDAR_DATE_FORMAT = '%Y-%m-%d';

    /*
       Variable: submission
         Current submission.
    */
    public $application;
	public $submission;
    public $type;
    public $item_id;
    public $item;
    public $renderer;
    public $layout;
    public $layout_path;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);
        
        $this->item_id = JRequest::getInt('item_id');

        // get submission info from Request
        if (!$submission_id = YRequest::getInt('submission_id')) {

            // else get submission info from menu item
            if ($menu = JSite::getMenu()->getActive()) {
                $this->menu_params   = new JParameter($menu->params);
                $submission_id = $this->menu_params->get('submission');
            }
        }

        // set submission
        if ($this->submission  = YTable::getInstance('submission')->get($submission_id)) {

            // set application
            $this->application = $this->submission->getApplication();

            // set template
            $this->template    = $this->application->getTemplate();

            // set renderer
            $this->renderer = new ItemRenderer();
            $this->renderer->addPath(array($this->template->getPath(), ZOO_SITE_PATH));

        }      

	}

    public function mysubmissions() {

        try {
            
            $this->_checkConfig();

			if ($this->user->get('aid', 0) < 1) {
				throw new SubmissionControllerException('Unsufficient User Rights.');
			}

            $limit = SubmissionController::PAGINATION_LIMIT;
            $state_prefix      = $this->option.'_'.$this->application->id.'.submission.'.$this->submission->id;
            $this->filter_type = $this->joomla->getUserStateFromRequest($state_prefix.'.filter_type', 'filter_type', '', 'string');
            $page              = $this->joomla->getUserStateFromRequest($state_prefix.'.page', 'page', 1, 'int');

            $limitstart = ($page - 1) * $limit;

            $table = YTable::getInstance('item');

            $this->types = $this->submission->getSubmittableTypes();

            // type select
			if (count($this->types) > 1) {
				$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Type').' -'));
				foreach ($this->types as $id => $type) {
					$options[] = JHTML::_('select.option', $id, $type->name);
				}
				$this->lists['select_type'] = JHTML::_('select.genericlist', $options, 'filter_type', 'class="inputbox auto-submit"', 'value', 'text', $this->filter_type);
			}

            // get data from the table
            $where = array();

            // application filter
            $where[] = 'application_id = ' . (int) $this->application->id;

            // type filter
            if (empty($this->filter_type)) {
                $where[] = 'type IN ("' . implode('", "', array_keys($this->types)) . '")';
            } else {
                $where[] = 'type = "' . (string) $this->filter_type . '"';
            }

            // author filter
            $where[] = 'created_by = ' . $this->user->id;

            // user rights
            $where[] = 'access <= ' . $this->user->get('aid', 0);

            $options          = array('conditions' => array(implode(' AND ', $where)), 'order' => 'created DESC');
            $this->items      = $table->all($limit ? array_merge($options, array('offset' => $limitstart, 'limit' => $limit)) : $options);
            $this->pagination = new YPagination('page', $table->count($options), $page, $limit);

            // display view
            $this->getView('submission')->addTemplatePath($this->template->getPath())->setLayout('mysubmissions')->display();

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            JError::raiseWarning(0, (string) $e);

        }
    }

    public function submission() {

        try {

            $this->_init();

            // is current user the item owner and does the user have sufficient user rights
            if ($this->item->id && (!$this->item->canAccess($this->user) || $this->item->created_by != $this->user->id)) {
                throw new YControllerException('You are not allowed to edit this item.');
            }

            // build new form, if no form was found in session
            if (!$this->form = $this->retrieveFormFromSession()) {

                $this->form = new ItemForm();
                $this->form->config(array('submission' => $this->submission, 'item' => $this->item, 'elements_config' => $this->elements_config));
                $this->form->setIgnoreErrors(true);
                $this->form->bindItem(); 
				
            }

            // build cancel url
            if (!empty($this->redirect)) {
                $this->cancel_url = RouteHelper::getMySubmissionsRoute($this->submission);
				$text = $this->item->id ? JText::_('Edit Submission') : JText::_('Add Submission');
				$this->pathway->addItem($text);
            }		

            if ($this->submission->isInTrustedMode()) {
                // most used tags
                $this->lists['most_used_tags'] = YTable::getInstance('tag')->getAll($this->application->id, null, null, 'items DESC, a.name ASC', null, 8);
            }

            // display view
            $this->getView('submission')->addTemplatePath($this->template->getPath())->setLayout('submission')->display();

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            JError::raiseWarning(0, (string) $e);

        }

    }

    public function save() {

        // check for request forgeries
        YRequest::checkToken() or jexit('Invalid Token');

        // init vars
        $post	   = JRequest::get('post');
		$db		   = YDatabase::getInstance();
		$tzoffset  = JFactory::getConfig()->getValue('config.offset');
		$now	   = JFactory::getDate();
		$now->setOffset($tzoffset);		

        try {

            $this->_init();

			// is this an item edit?
			$edit = (int) $this->item->id;

            // is current user the item owner and does the user have sufficient user rights
            if ($edit && (!$this->item->canAccess($this->user) || $this->item->created_by != $this->user->id)) {
                throw new YControllerException('You are not allowed to make changes to this item.');
            }

            // get submission params
            $categories = array();
            if (!$this->submission->isInTrustedMode() && ($category = $this->submission->getForm($this->type->id)->get('category'))) {
                $categories[] = $category;
            }
            
            // get element data from post
            if (isset($post['elements'])) {

                // filter element data
                if (!$this->submission->isInTrustedMode() && !UserHelper::isJoomlaAdmin($this->user)) {
                    JRequest::setVar('elements', SubmissionHelper::filterData($post['elements']));
                    $post = JRequest::get('post');
                }
                
                // merge elements into post
                $post = array_merge($post, $post['elements']);
            }

			// fix publishing dates in trusted mode
			if ($this->submission->isInTrustedMode()) {

				// set publish up date
				if (isset($post['publish_up'])) {
					if (empty($post['publish_up'])) {
						$post['publish_up'] = $now->toMySQL(true);
					}
				}

				// set publish down date
				if (isset($post['publish_down'])) {
					if (trim($post['publish_down']) == JText::_('Never') || trim($post['publish_down']) == '') {
						$post['publish_down'] = $db->getNullDate();
					}
				}
			}

            // sanatize tags
            if (!isset($post['tags'])) {
                $post['tags'] = array();
            }

            // build new item form and bind it with post data
            $form = new ItemForm(array('submission' => $this->submission, 'item' => $this->item, 'elements_config' => $this->elements_config));
            $form->bind($post);

            if ($form->isValid()) {

                // set name
                $this->item->name = $form->getValue('name');

                // bind elements
                foreach ($this->elements_config as $data) {
                    if (($element = $this->item->getElement($data->element)) && $field = $form->getFormField($data->element)) {
                        if ($field_data = $field->hasError() ? $field->getTaintedValue() : $field->getValue()) {
                            $element->bindData($field_data);
                        } else {
                            $element->bindData();
                        }

                        // perform submission uploads
                        if ($element instanceof iSubmissionUpload) {
                            $element->doUpload();
                        }
                    }
                }

                // set alias
                $this->item->alias = ItemHelper::getUniqueAlias($this->item->id, YString::sluggify($this->item->name));

                // set modified
                $this->item->modified	 = $now->toMySQL();
                $this->item->modified_by = $this->user->get('id');

                // set created date
                if (!$this->item->id) {
                    $this->item->created 	= $now->toMySQL();
                    $this->item->created_by = $this->user->get('id');
                }
                
                if ($this->submission->isInTrustedMode()) {

                    // set state
                    $this->item->state = $form->getValue('state');

					// set publish up
					if (($publish_up = $form->getValue('publish_up')) && !empty($publish_up)) {
						$date = JFactory::getDate($publish_up, $tzoffset);
						$publish_up = $date->toMySQL();
					}
					$this->item->publish_up = $publish_up;

					// set publish down
					if (($publish_down = $form->getValue('publish_down')) && !empty($publish_down) && !($publish_down == $db->getNullDate())) {
						$date = JFactory::getDate($publish_down, $tzoffset);
						$publish_down = $date->toMySQL();
					}
					$this->item->publish_down = $publish_down;

                    // set searchable
                    $this->item->searchable = $form->getValue('searchable');

                    // set comments enabled
                    $this->item->params = $this->item
                        ->getParams()
                        ->set('config.enable_comments', $form->getValue('enable_comments'))
                        ->toString();

                    // set frontpage
                    if ($form->getValue('frontpage')) {
                        $categories[] = 0;
                    }

                    // set categories
					$tmp_categories = $form->getValue('categories');
					if (!empty($tmp_categories)) {
						foreach ($form->getValue('categories') as $category) {
							$categories[] = $category;
						}
					}

                    // set tags
                    $tags = $form->hasError('tags') ? $form->getTaintedValue('tags') : $form->getValue('tags');
                    $this->item->setTags($tags);

                } else {

					// set publish up
					$this->item->publish_up = $now->toMySQL();

					// spam protection - user may only submit items every SubmissionController::TIME_BETWEEN_PUBLIC_SUBMISSIONS seconds
					if (empty($this->item->id)) {
						$timestamp = $this->session->get('ZOO_LAST_SUBMISSION_TIMESTAMP');
						$now = time();
						if ($now < $timestamp + SubmissionController::TIME_BETWEEN_PUBLIC_SUBMISSIONS) {
							throw new SubmissionControllerException('You are submitting to fast, please try again in a few moments.');
						}
						$this->session->set('ZOO_LAST_SUBMISSION_TIMESTAMP', $now);
					}					

					$this->item->state = 0;
				}

                // save item
                YTable::getInstance('item')->save($this->item);

                // save category relations - only if not editing in none trusted mode
				if (!$edit || $this->submission->isInTrustedMode()) {
					CategoryHelper::saveCategoryItemRelations($this->item, $categories);
				}

                // set redirect message
				if ($this->submission->isInTrustedMode()) {
					$msg = JText::_('Thanks for your submission.');
				} else {
					$msg = JText::_('Thanks for your submission. It will be reviewed before being posted on the site.');
				}

            } else {

                $this->addFormToSession($form);

            }

        } catch (SubmissionControllerException $e) {

            // raise warning on exception
            JError::raiseWarning(0, (string) $e);

        } catch (YException $e) {

            // raise warning on exception
            JError::raiseWarning(0, JText::_('There was an error saving your submission, please try again later.'));

            // add exception details, for super administrators only
            if ($this->user->superadmin) {
                JError::raiseWarning(0, (string) $e);
            }

        }
        
        // redirect to mysubmissions
        if ($this->redirect == 'mysubmissions' && $form && $form->isValid()) {			
            $link = RouteHelper::getMySubmissionsRoute($this->submission);
        // redirect to edit form
        } else {
			$link = RouteHelper::getSubmissionRoute($this->submission, $this->type->id, $this->hash, $this->item_id, $this->redirect);
        }

        $link = JRoute::_($link, false);

        $this->setRedirect($link, $msg);
    }

    public function remove() {

        // init vars
        $msg = null;

        try {

            $this->_checkConfig();

            if (!$this->submission->isInTrustedMode()) {
                throw new YControllerException('The submission is not in Trusted Mode.');
            }

			// get item table and delete item
			$table = YTable::getInstance('item');

            $item = $table->get($this->item_id);

            // is current user the item owner and does the user have sufficient user rights
            if ($item->id && (!$item->canAccess($this->user) || $item->created_by != $this->user->id)) {
                throw new YControllerException('You are not allowed to make changes to this item.');
            }           

            $table->delete($item);

			// set redirect message
			$msg = JText::_('Submission Deleted');

		} catch (YException $e) {

            // raise warning on exception
            JError::raiseWarning(0, JText::_('There was an error deleting your submission, please try again later.'));

            // add exception details, for super administrators only
            if ($this->user->superadmin) {
                JError::raiseWarning(0, (string) $e);
            }         

		}

        $link = RouteHelper::getMySubmissionsRoute($this->submission);
        $this->setRedirect(JRoute::_($link,false), $msg);

    }

    public function loadtags() {

		// load controller
		require_once(ZOO_ADMIN_PATH."/controllers/item.php");

        // perform the request task
		$controller = new ItemController();
		$controller->execute('loadtags');
		$controller->redirect();

    }

    protected function _checkConfig() {

        if (!$this->application || !$this->submission) {
            throw new SubmissionControllerException('Submissions are not configured correctly.');
        }

        if (!$this->submission->getState()) {
            throw new SubmissionControllerException('Submissions are disabled.');
        }

        if (!$this->submission->canAccess($this->user)) {
            throw new SubmissionControllerException('Unsufficient User Rights.');
        }
    }

    protected function _init() {

        //init vars
        $type_id        = JRequest::getString('type_id');
        $hash           = JRequest::getString('submission_hash');
        $this->redirect = YRequest::getString('redirect');

        // check config
        $this->_checkConfig();

        // get submission info from request
        if ($type_id) {

            if ($hash != SubmissionHelper::getSubmissionHash($this->submission->id, $type_id, $this->item_id)) {
                throw new SubmissionControllerException('Hashes did not match.');
            }

        // else get submission info from active menu
        } elseif ($this->menu_params) {
            $type_id = $this->menu_params->get('type');

            // remove item_id (menu item may not have an item_id)
            $this->item_id = null;
        }

        // set type
        $this->type  = $this->submission->getType($type_id);

        // check type
        if (!$this->type) {
            throw new SubmissionControllerException('Submissions are not configured correctly.');
        }

        // set hash
        $this->hash = $hash ? $hash : SubmissionHelper::getSubmissionHash($this->submission->id, $this->type->id, $this->item_id);

        // set layout
        $this->layout = $this->submission->getForm($this->type->id)->get('layout', '');

        // check layout
        if (empty($this->layout)) {
            throw new SubmissionControllerException('Submission is not configured correctly.');
        }

        // set layout path
        $this->layout_path = 'item.';
        if ($this->renderer->pathExists('item/'.$this->type->id)) {
                $this->layout_path .= $this->type->id.'.';
        }
        $this->layout_path .= $this->layout;

        // get positions
        $positions = $this->renderer->getConfig('item')->get($this->application->getGroup().'.'.$this->type->id.'.'.$this->layout, array());

        // get elements from positions
        $this->elements_config = array();
        foreach ($positions as $position) {
            foreach ($position as $element) {
				if ($element_obj = $this->type->getElement($element->element)) {
					if (!$this->submission->isInTrustedMode()) {
						$metadata = $element_obj->getMetaData();
						if ($metadata['trusted'] == 'true') {
							continue;
						}
					}

					$this->elements_config[$element->element] = $element;
				}
            }
        }

        // get item table
        $table = YTable::getInstance('item');

        // get item
		if (!$this->item_id || !($this->item = $table->get($this->item_id))) {
            $this->item = new Item();
            $this->item->application_id = $this->application->id;
            $this->item->type = $this->type->id;
			$now = JFactory::getDate();
			$config = JFactory::getConfig();
			$offset = $config->getValue('config.offset');
			$now->setOffset($offset);
			$this->item->publish_up = $now->toFormat(SubmissionController::EDIT_DATE_FORMAT);
			$this->item->publish_down = YDatabase::getInstance()->getNullDate();
        }

    }


	/*
		Function: addFormToSession
			Adds form to session.

		Parameters:
  			$form - submission form.

		Returns:
			void
	*/
	public function addFormToSession($form = null) {
		return $this->joomla->setUserState($this->getSessionFormKey(), serialize($form));
	}

	/*
		Function: retrieveFormFromSession
			Retrieve form from session. Creating a new form if it doesn't exist.

		Returns:
			ItemForm
	*/
	public function retrieveFormFromSession()  {

        // load corresponding element classes
        $this->type->getElements();

        // build new form, if no form was found in session
        if ($form = unserialize($this->joomla->getUserState($this->getSessionFormKey()))) {
            $form->setItem($this->item);
            $form->setSubmission($this->submission);
        }

        // remove form from session
        $this->addFormToSession();

		return $form;
	}

    /*
		Function: getSessionFormKey
			Retrieve session form key.

		Returns:
			String - session form key
	*/
    public function getSessionFormKey() {
        return SubmissionController::SESSION_PREFIX . 'SUBMISSION_FORM_' . $this->submission->id;
    }

}

/*
	Class: SubmissionControllerException
*/
class SubmissionControllerException extends YException {}