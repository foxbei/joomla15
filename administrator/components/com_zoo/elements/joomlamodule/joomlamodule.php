<?php
/**
* @package   ZOO Component
* @file      joomlamodule.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementJoomlamodule
       The Joomla module wapper element class
*/
class ElementJoomlamodule extends Element {

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// get modules
		$modules = self::_load();
		$value   = $this->_data->get('value');
		
		if ($value && isset($modules[$value])) {
			if ($modules[$value]->published) {

				$rendered = JModuleHelper::renderModule($modules[$value]);

				if (isset($modules[$value]->params)) {
					$module_params = new YParameter();
					$module_params->loadString($modules[$value]->params);
					if ($moduleclass_sfx = $module_params->get('moduleclass_sfx')) {
						$html[] = '<div class="'.$moduleclass_sfx.'">';
						$html[] = $rendered;
						$html[] = '</div>';

						return implode("\n", $html);
					}
				}

				return $rendered;
			}
		}
		
		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// get modules
		$modules = self::_load();
		
		if (count($modules)) {
		
			$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Module').' -'));
			
			foreach ($modules as $module) {
				$options[] = JHTML::_('select.option', $module->id, $module->title.' ('.$module->position.')');
			}
			
			return JHTML::_('select.genericlist', $options, 'elements[' . $this->identifier . '][value]', null, 'value', 'text', $this->_data->get('value'));
		}
		
		return JText::_("There are no modules to choose from.");
	}

	/*
	   Function: _load
	       Load Joomla modules.

	   Returns:
	       Array - modules
	*/
	protected function _load() {
		static $modules;

		if (isset($modules)) {
			return $modules;
		}

		$user    = JFactory::getUser();
		$db	     = JFactory::getDBO();
		$modules = array();
		
		$query = "SELECT id, title, module, position, content, showtitle, control, params, published"
			." FROM #__modules AS m"
			." LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id"
			." WHERE m.access <= ".(int) $user->get('aid', 0)
			." AND m.client_id = 0"
			." ORDER BY position, ordering";
			
		$db->setQuery($query);

		if (null === ($modules = $db->loadObjectList('id'))) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Error Loading Modules').$db->getErrorMsg());
			return false;
		}

		foreach ($modules as $i => $module) {
			$file					= $modules[$i]->module;
			$custom 				= JString::substr($file, 0, 4) == 'mod_' ? 0 : 1;
			$modules[$i]->user  	= $custom;
			$modules[$i]->name		= $custom ? $modules[$i]->title : JString::substr($file, 4);
			$modules[$i]->style		= null;
			$modules[$i]->position	= JString::strtolower($modules[$i]->position);
		}

		return $modules;
	}

}