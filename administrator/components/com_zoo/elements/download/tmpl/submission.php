<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$id = 'elements'.$element;

?>

<div id="<?php echo $id; ?>">

	<div class="download-select">
	
		<div class="upload">
			<input type="text" id="filename<?php echo $element; ?>" readonly="readonly" />
			<div class="button-container">
				<button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
				<input type="file" name="elements_<?php echo $element; ?>" onchange="javascript: document.getElementById('filename<?php echo $element; ?>').value = this.value" />
			</div>
		</div>

		<?php if (isset($lists['upload_select'])) : ?>

			<span class="select"><?php echo JText::_('Already uploaded?'); ?></span><?php echo $lists['upload_select']; ?>

		<?php else : ?>

			<input type="hidden" class="upload" name="elements[<?php echo $element; ?>][upload]" value="<?php echo $upload ? 1 : ''; ?>" />

        <?php endif; ?>

    </div>

    <div class="download-preview">
        <span class="preview"><?php echo $upload; ?></span>
        <span class="download-cancel" title="<?php JText::_('Remove file'); ?>"></span>
    </div>

    <?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">

			<div class="row short download-limit">
				<?php echo JHTML::_('control.text', 'elements['.$element.'][download_limit]', ($upload ? $download_limit : ''), 'maxlength="255" title="'.JText::_('Download limit').'" placeholder="'.JText::_('Download limit').'"'); ?>
			</div>

		</div>
	</div>
    <?php endif; ?>

    <script type="text/javascript">
		jQuery(function($){
			$('#<?php echo $id; ?>').DownloadSubmission();
		});
    </script>

</div>