<?php
/**
* @package   ZOO Component
* @file      alphaindex.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JError::raiseWarning(0, JText::_('Error Displaying Layout').' (The Pages App does not support a "'.$this->getLayout().'" view. It should display static content only. Please use another app.)');