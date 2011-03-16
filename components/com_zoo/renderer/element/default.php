<?php
/**
* @package   ZOO Component
* @file      default.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// create label
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
	$label = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
}

// render element
echo $label.' '.$element->render($params).' ';