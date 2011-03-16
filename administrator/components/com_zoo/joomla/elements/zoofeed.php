<?php
/**
* @package   ZOO Component
* @file      zoofeed.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

class JElementZooFeed extends JElement {

	var	$_name = 'ZooFeed';

	protected static $_count = 1;	
	
	function fetchElement($name, $value, &$node, $control_name) {

		// load script
		JHTML::script('zoofeed.js', 'administrator/components/com_zoo/joomla/elements/');		

		// init vars
		$id      			 = 'feed-'.self::$_count++;		
		$params 			 = $this->_parent;
		$feed_title 		 = $params->getValue('feed_title');
		$alternate_feed_link = $params->getValue('alternate_feed_link');
		
		// create html
		$html[] = '<div class="zoo-feed">';	
		$html[] = JHTML::_('select.booleanlist', $control_name.'['.$name.']', null , $value);
		$html[] = '<div class="input">';
		$html[] = '<label class="hasTip" title="'.JText::_('OPTIONAL_FEED_TITLE').'" for="'.$id.'">'.JText::_('Feed title').'</label>';
		$html[] = JHTML::_('control.text', $control_name.'[feed_title]', $feed_title);
		$html[] = '<label class="hasTip" title="'.JText::_('ALTERNATE_FEED_LINK').'" for="'.$id.'">'.JText::_('Alternate feed link').'</label>';		
		$html[] = JHTML::_('control.text', $control_name.'[alternate_feed_link]', $alternate_feed_link);
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}

}