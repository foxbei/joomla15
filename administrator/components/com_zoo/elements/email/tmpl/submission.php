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

$id = 'elements['.$element.']['.$index.']';

?>

<div id="<?php echo $id; ?>">

	<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][value]', $email, 'size="60" title="'.JText::_('Email').'"'); ?>
	
	<?php if ($trusted_mode) : ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][text]', $text, 'size="60" title="'.JText::_('Text').'" placeholder="'.JText::_('Text').'"'); ?>
			</div>

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][subject]', $subject, 'size="60" title="'.JText::_('Subject').'" placeholder="'.JText::_('Subject').'"'); ?>
			</div>

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][body]', $body, 'size="60" title="'.JText::_('Body').'" placeholder="'.JText::_('Body').'"'); ?>
			</div>

		</div>
		
	</div>
	<?php endif; ?>

</div>