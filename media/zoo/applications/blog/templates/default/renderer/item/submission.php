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

<fieldset class="pos-content creation-form">
	<legend><?php echo $form->getItem()->getType()->name; ?></legend>
	
	<div class="element element-name required <?php echo ($form->hasError('name') ? 'error' : ''); ?>">
		<strong><?php echo JText::_('Title'); ?></strong>
		<input type="text" name="name" size="60" value="<?php echo $form->getTaintedValue('name'); ?>" />
		<?php if ($form->hasError('name')) : ?>
			<div class="error-message"><?php echo $form->getError('name'); ?></div>
		<?php endif; ?>
	</div>
	
	<?php if ($this->checkSubmissionPosition('content')) : ?>
	<?php echo $this->renderSubmissionPosition('content', array('style' => 'submission.block')); ?>
	<?php endif; ?>
	
</fieldset>

<?php if ($this->checkSubmissionPosition('media')) : ?>
<fieldset class="pos-media creation-form">
	<legend><?php echo JText::_('Media'); ?></legend>
	
	<?php echo $this->renderSubmissionPosition('media', array('style' => 'submission.block')); ?>
	
</fieldset>
<?php endif; ?>

<?php if ($this->checkSubmissionPosition('meta')) : ?>
<fieldset class="pos-meta creation-form">
	<legend><?php echo JText::_('Meta'); ?></legend>
	
	<?php echo $this->renderSubmissionPosition('meta', array('style' => 'submission.block')); ?>
	
</fieldset>
<?php endif; ?>