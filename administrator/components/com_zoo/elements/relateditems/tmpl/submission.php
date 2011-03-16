<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="<?php echo $element; ?>" class="select-relateditems">
	<ul>

	<?php foreach ($data as $item) : ?>

		<li>
			<div class="item-name"><?php echo $item->name; ?></div>
			<div class="item-sort" title="<?php echo JText::_('Sort Item'); ?>"></div>
			<div class="item-delete" title="<?php echo JText::_('Delete Item'); ?>"></div>
			<input type="hidden" name="elements[<?php echo $element; ?>][item][]" value="<?php echo $item->id; ?>"/>
		</li>

	<?php endforeach; ?>
	</ul>
	<a class="item-add modal" rel="{handler: 'iframe', size: {x: 850, y: 500}}" title="<?php echo JText::_('Add Item'); ?>" href="<?php echo JRoute::_($link); ?>" ><?php echo JText::_('Add Item'); ?></a>
</div>

<script type="text/javascript">
	window.relateditems.<?php echo $id; ?> = new ElementRelateditems('<?php echo $element; ?>', { variable: 'elements[<?php echo $element; ?>][item][]', msgDeleteItem: '<?php echo JText::_('Delete Item'); ?>', msgSortItem: '<?php echo JText::_('Sort Item'); ?>'});
</script>