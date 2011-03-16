<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$hide_path = $hide_path ? ' style="display: none;"' : '';

$id = 'elements'.$element;

?>

<div id="<?php echo $id; ?>">

    <div class="row"<?php echo $hide_path; ?>>
        <?php echo JHTML::_('control.input', 'text', 'elements[' . $element . '][file]', $file, 'readonly="readonly" size="60" title="'.JText::_('Path').'"'); ?>
    </div>

	<?php $style = $hide_path ? ' style="margin-top: 0"' : ''; ?>
    <div class="row"<?php echo $style; ?>>
        <?php echo JHTML::_('control.selectfile', JPATH_ROOT.DS.$this->_config->get('directory'), false, '', $trimmed_file); ?>
		<?php echo $info; ?>
		<?php if ($hits) : ?>
			<input name="reset-hits" type="button" class="button" value="<?php echo JText::_('Reset'); ?>"/>
		<?php endif; ?>
    </div>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">
			<div class="row short download-limit">
				<label for="elements[<?php echo $element; ?>][download_limit]"><?php echo JText::_('Download limit'); ?></label>
				<?php echo JHTML::_('control.text', 'elements[' . $this->identifier . '][download_limit]', $this->_data->get('download_limit'), 'size="6" maxlength="255" title="'.JText::_('Download limit').'" placeholder="'.JText::_('Download Limit').'"'); ?>
			</div>
		</div>
	</div>

    <script type="text/javascript">
		jQuery(function($){
			$('#<?php echo $id; ?>').ElementDownload( {url: "<?php echo JRoute::_('index.php?option=com_zoo&controller=item&format=raw&type='.$this->getType()->id.'&elm_id='.$element.'&item_id='.$this->getItem()->id, false); ?>", directory: "<?php echo $directory; ?>"} );
		});
    </script>

</div>