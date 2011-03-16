<?php 
/**
* @package   ZOO Component
* @file      _assignsubmittableelement.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get elements meta data
$metadata = $element->getMetaData();
$name = 'positions['.$position.']['.$index.']';
$form = $element->getConfigForm();

?>
<li class="element hideconfig" role="<?php echo $element->identifier; ?>">
	<div class="element-icon edit-element edit-event" title="<?php echo JText::_('Edit element'); ?>"></div>
	<div class="element-icon delete-element delete-event" title="<?php echo JText::_('Delete element'); ?>"></div>
	<div class="name sort-event" title="<?php echo JText::_('Drag to sort'); ?>"><?php echo $element->getConfig()->get('name'); ?> 
		<span>(<?php echo $metadata['name']; ?>)</span>
	</div>
	<div class="config">
		<?php echo $form->setValues($data)->render($name, 'submission'); ?>
		<input type="hidden" name="<?php echo $name;?>[element]" value="<?php echo $element->identifier; ?>" />
	</div>
</li>
