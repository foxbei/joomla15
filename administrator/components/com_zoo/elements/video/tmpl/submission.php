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

$id = 'elements['.$element.']';

?>

<div id="<?php echo $id; ?>">

    <div class="row">
        <?php echo JHTML::_('control.text', 'elements['.$element.'][url]', $url, 'id="elements['.$element.'][url]" class="url" size="50" maxlength="255" title="'.JText::_('URL').'" placeholder="'.JText::_('URL').'"'); ?>
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
			<div class="row short">
				<?php echo JHTML::_('control.text', 'elements['.$element.'][width]', $width, 'id="elements['.$element.'][width]" maxlength="4" title="'.JText::_('Width').'" placeholder="'.JText::_('Width').'"'); ?>
			</div>

			<div class="row short">
				<?php echo JHTML::_('control.text', 'elements['.$element.'][height]', $height, 'id="elements['.$element.'][height]" maxlength="4" title="'.JText::_('Height').'" placeholder="'.JText::_('Height').'"'); ?>
			</div>

			<div class="row">
				<strong><?php echo JText::_('AutoPlay'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'elements['.$element.'][autoplay]', '', $autoplay) ?>
			</div>
		</div>
	</div>
    <?php endif; ?>
	
</div>