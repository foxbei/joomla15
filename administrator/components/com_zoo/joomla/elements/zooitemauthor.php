<?php
/**
* @package   ZOO Component
* @file      zooitemauthor.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a author element
 *
 * @package 	Joomla
 * @subpackage	Articles
 * @since		1.5
 */
class JElementZooItemAuthor extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'ZooItemAuthor';

	function fetchElement($name, $value, &$node, $control_name)
	{

        $options[] = JHTML::_('select.option',  'NO_CHANGE', '- '. JText::_( 'No Change' ) .' -' );

		return JHTML::_('zoo.authorList', $options, $control_name.'['.$name.']',  null, 'value', 'text', $value, false, false, false);
	}
}