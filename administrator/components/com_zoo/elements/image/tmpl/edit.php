<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$id = 'elements['.$element.']';
?>

<div id="<?php echo $id; ?>">

	<div class="row">
        <?php echo JHTML::_('control.text', 'elements['.$element.'][file]', $this->_data->get('file'), 'class="image-select" size="60" style="width:200px;margin-right:5px;" title="'.JText::_('File').'"'); ?>
    </div>

	<div class="more-options">

		<div class="trigger">
			<div>
				<div class="lightbox button"><?php echo JText::_('Lightbox'); ?></div>
				<div class="link button"><?php echo JText::_('Link'); ?></div>
				<div class="title button"><?php echo JText::_('Title'); ?></div>
			</div>
		</div>

		<div class="title options">

			<div class="row">
				<label for="elements[<?php echo $element; ?>][title]"><?php echo JText::_('Title'); ?></label>
				<?php echo JHTML::_('control.text', 'elements['.$this->identifier.'][title]', $title, 'maxlength="255" title="'.JText::_('Title').'"'); ?>
			</div>

		</div>

		<div class="link options">

			<div class="row">
				<label for="elements[<?php echo $element; ?>][link]"><?php echo JText::_('Link'); ?></label>
				<?php echo JHTML::_('control.text', 'elements['.$this->identifier.'][link]', $link, 'size="60" maxlength="255" title="'.JText::_('Link').'"'); ?>
			</div>

			<div class="row">
				<strong><?php echo JText::_('New window'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'elements['.$this->identifier.'][target]', $target, $target); ?>
			</div>

			<div class="row">
				<label for="elements[<?php echo $element; ?>][rel]"><?php echo JText::_('Rel'); ?></label>
				<?php echo JHTML::_('control.text', 'elements['.$this->identifier.'][rel]', $rel, 'size="60" maxlength="255" title="'.JText::_('Rel').'"'); ?>
			</div>
		</div>

		<div class="lightbox options">

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.'][lightbox_image]', $lightbox_image, 'class="image-select" size="60" style="width:200px;margin-right:5px;" title="'.JText::_('Lightbox image').'"'); ?>
			</div>

		</div>

	</div>

    <script type="text/javascript">
        window.addEvent('domready', function(){
			new Zoo.EditElement({element: '<?php echo $id; ?>'});
        });
    </script>

</div>