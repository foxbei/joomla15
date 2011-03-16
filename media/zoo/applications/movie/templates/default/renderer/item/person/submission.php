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

<fieldset class="pos-content creation-form">
	<legend><?php echo $form->getItem()->getType()->name; ?></legend>
	
	<div class="element element-name required <?php echo ($form->hasError('name') ? 'error' : ''); ?>">
		<strong><?php echo JText::_('Name'); ?></strong>
		<input type="text" name="name" size="60" value="<?php echo $form->getTaintedValue('name'); ?>" />
		<?php if ($form->hasError('name')) : ?>
			<div class="error-message"><?php echo $form->getError('name'); ?></div>
		<?php endif; ?>
	</div>
	
	<?php if ($this->checkSubmissionPosition('content')) : ?>
	<?php echo $this->renderSubmissionPosition('content', array('style' => 'submission.block')); ?>
	<?php endif; ?>
	
</fieldset>