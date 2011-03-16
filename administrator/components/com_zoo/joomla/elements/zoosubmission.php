<?php
/**
* @package   ZOO Component
* @file      zoosubmission.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementZooSubmission extends JElement {

	var	$_name = 'ZooSubmission';

	function fetchElement($name, $value, &$node, $control_name) {

		JHTML::_('behavior.modal', 'a.modal');
		JHTML::stylesheet('zoosubmission.css', ZOO_ADMIN_URI.'/joomla/elements/');
		JHTML::script('zoosubmission.js', ZOO_ADMIN_URI.'/joomla/elements/');
	
		// init vars
		$params = $this->_parent;
		$table  = YTable::getInstance('application');

        $show_types = $node->attributes('types');

		// create application/category select
        $submissions = array();
		$types       = array();
		$app_options = array(JHTML::_('select.option', '', '- '.JText::_('Select Application').' -'));
        
		foreach ($table->all(array('order' => 'name')) as $app) {
			// application option
			$app_options[$app->id] = JHTML::_('select.option', $app->id, $app->name);

            // create submission select
            $submission_options = array();
            foreach($app->getSubmissions() as $submission) {
                $submission_options[$submission->id] = JHTML::_('select.option', $submission->id, $submission->name);

                if ($show_types) {
                    $type_options = array();
                    $type_objects = $submission->getSubmittableTypes();
                    if (!count($type_objects)) {
                        unset($submission_options[$submission->id]);
                        continue;
                    }

                    foreach ($submission->getTypes() as $type) {
                        $type_options[] = JHTML::_('select.option', $type->id, $type->name);
                    }

                    $attribs = 'class="type submission-'.$submission->id.' app-'.$app->id.'" role="'.$control_name.'[type]"';
                    $types[] = JHTML::_('select.genericlist', $type_options, $control_name.'[type]', $attribs, 'value', 'text', $params->get('type'));
                }
            }

            if (!count($submission_options)) {
                unset($app_options[$app->id]);
                continue;
            }

			$attribs = 'class="submission app-'.$app->id.'" role="'.$control_name.'[submission]"';
			$submissions[] = JHTML::_('select.genericlist', $submission_options, $control_name.'[submission]', $attribs, 'value', 'text', $params->get('submission'));
		}


		// create html
		$html[] = '<div id="'.$name.'" class="zoo-submission">';
		
		// create application html	
		$html[] = JHTML::_('select.genericlist', $app_options, $control_name.'['.$name.']', 'class="application"', 'value', 'text', $value);

		// create submission html
		$html[] = '<div class="submissions">'.implode("\n", $submissions).'</div>';


		// create types html
        if ($show_types) {
            $html[] = '<div class="types">'.implode("\n", $types).'</div>';
        }
		
		$html[] = '</div>';

		$javascript  = 'jQuery("#'.$name.'").ZooSubmission();';
		$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";
		
		return implode("\n", $html).$javascript;
	}

}