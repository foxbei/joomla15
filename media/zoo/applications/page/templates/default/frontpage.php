<?php
/**
* @package   ZOO Component
* @file      frontpage.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JError::raiseWarning(0, JText::_('Error Displaying Layout').' (Layout "'.$this->getLayout().'" not found)');