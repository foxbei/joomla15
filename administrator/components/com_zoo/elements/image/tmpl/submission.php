<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="<?php echo $element; ?>">

	<div class="image-select">

		<div class="upload">
			<input type="text" id="filename<?php echo $element; ?>" readonly="readonly" />
			<div class="button-container">
				<button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
				<input type="file" name="elements_<?php echo $element; ?>" onchange="javascript: document.getElementById('filename<?php echo $element; ?>').value = this.value" />
			</div>
		</div>

		<?php if (isset($lists['image_select'])) : ?>

			<span class="select"><?php echo JText::_('Already uploaded?'); ?></span><?php echo $lists['image_select']; ?>

		<?php else : ?>

			<input type="hidden" class="image" name="elements[<?php echo $element; ?>][image]" value="<?php echo $image ? 1 : ''; ?>">

		<?php endif; ?>

	</div>

	<div class="image-preview">
		<img src="<?php echo $image; ?>" alt="preview">
		<span class="image-cancel" title="<?php JText::_('Remove image'); ?>"></span>
	</div>

</div>

<script type="text/javascript">
	jQuery(function($){
		$('#yoo-zoo .<?php echo $element; ?>').ImageSubmission({ uri: '<?php echo JURI::root(); ?>' });
	});
</script>