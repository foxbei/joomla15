<?php 
/**
* @package   ZOO Component
* @file      addelement.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// output elements html
if ($this->element) {
	echo $this->partial('editelement', array('element' => $this->element, 'name' => 'New', 'var' => $this->var));
}

?>