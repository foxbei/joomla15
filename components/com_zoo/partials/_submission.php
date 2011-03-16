<?php
/**
* @package   ZOO Component
* @file      _submission.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('submission.css', 'media/zoo/assets/css/');
JHTML::script('observer.js', 'media/zoo/assets/js/');
JHTML::script('submission.js', 'media/zoo/assets/js/');
JHTML::script('observer.js', 'administrator/components/com_zoo/assets/js/');
JHTML::script('element.js', 'administrator/components/com_zoo/assets/js/');

if ($this->submission->showTooltip()) {
	JHTML::_('behavior.tooltip');
}

?>

<?php if ($this->form->isBound() && !$this->form->isValid()): ?>
	<?php $msg = count($this->form->getErrors()) > 1 ? JText::_('Oops. There were errors in your submission.') : JText::_('Oops. There was an error in your submission.'); ?>
	<?php $msg .= ' '.JText::_('Please take a look at all highlighted fields, correct your data and try again.'); ?>
	<p class="message"><?php echo $msg; ?></p>
<?php endif; ?>

<form id="item-submission" action="<?php echo JRoute::_('index.php'); ?>" method="post" name="submissionForm" accept-charset="utf-8" enctype="multipart/form-data">

	<?php

		echo $fields;

		if ($this->submission->isInTrustedMode()) {
			echo $this->partial('administration');
		}

	?>

	<p class="info"><?php echo JText::_('REQUIRED_INFO'); ?></p>

	<div class="submit">
		<button type="submit" id="submit-button" class="button-green"><?php echo JText::_('Submit Item'); ?></button>
		<?php if (!empty($this->cancel_url)) : ?>
			<a href="<?php echo JRoute::_($this->cancel_url); ?>" id="cancel-button"><?php echo JText::_('Cancel'); ?></a>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="com_zoo" />
	<input type="hidden" name="controller" value="submission" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="submission_id" value="<?php echo $this->submission->id; ?>" />
	<input type="hidden" name="type_id" value="<?php echo $this->type->id; ?>" />
	<input type="hidden" name="item_id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="submission_hash" value="<?php echo $this->hash; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />

	<?php echo JHTML::_('form.token'); ?>

</form>