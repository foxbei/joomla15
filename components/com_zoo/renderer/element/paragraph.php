<?php
/**
* @package   ZOO Component
* @file      paragraph.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// create label
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
	$label .= '<h3>';
	$label .= ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
	$label .= '</h3>';
}

// create class attribute
$class = 'element element-'.$element->getElementType().' '.($params['first'] ? ' first' : '').($params['last'] ? ' last' : '');

?>
<p class="<?php echo $class; ?>">
	<?php echo $label.$element->render($params); ?>
</p>