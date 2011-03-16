<?php
/**
* @package   ZOO Component
* @file      block.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = new YArray($params);

// add tooltip
$tooltip = '';
if ($params->get('show_tooltip') && ($description = $element->getConfig()->get('description'))) {
	$tooltip = ' class="hasTip" title="'.$description.'"';
}

// create label
$label  = '<strong'.$tooltip.'>';
$label .= $params->get('altlabel') ? $params->get('altlabel') : $element->getConfig()->get('name');
$label .= '</strong>';

// create error
$error = '';
if ($field->hasError()) {
    $error = '<p class="error-message">'.(string) $field->getError().'</p>';
}

// create class attribute
$class = 'element element-'.$element->getElementType().($params->get('first') ? ' first' : '').($params->get('last') ? ' last' : '').($params->get('required') ? ' required' : '').($field->hasError() ? ' error' : '');

$element->loadAssets();

?>
<div class="<?php echo $class; ?>">
	<?php echo $label.$element->renderSubmission($params).$error; ?>
</div>