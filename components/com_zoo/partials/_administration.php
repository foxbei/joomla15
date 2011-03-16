<?php
/**
* @package   ZOO Component
* @file      _administration.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::script('observer.js', ZOO_ADMIN_URI . 'assets/js/');
JHTML::script('autocompleter.js', ZOO_ADMIN_URI . 'assets/js/');
JHTML::script('autocompleter.request.js', ZOO_ADMIN_URI . 'assets/js/');
JHTML::script('tag.js', ZOO_ADMIN_URI . 'assets/js/');

$tags = $this->form->hasError('tags') ? $this->form->getTaintedValue('tags') : $this->form->getValue('tags');
$tags = $tags ? $tags : array();

?>

<fieldset class="administration creation-form">
	<legend>Administration</legend>

	<div class="floatbox">

		<div class="width50">
	
			<div class="element">
				<strong><?php echo JText::_('Published'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'state', '', $this->form->getTaintedValue('state')); ?>
				<?php if ($this->form->hasError('state')) : ?><div class="error-message"><?php echo $this->form->getError('state'); ?></div><?php endif; ?>
			</div>
		
			<div class="element">
				<strong><?php echo JText::_('Searchable'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'searchable', '', $this->form->getTaintedValue('searchable')); ?>
				<?php if ($this->form->hasError('searchable')) : ?><div class="error-message"><?php echo $this->form->getError('searchable'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Comments'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'enable_comments', '', $this->form->getTaintedValue('enable_comments')); ?>
				<?php if ($this->form->hasError('enable_comments')) : ?><div class="error-message"><?php echo $this->form->getError('enable_comments'); ?></div><?php endif; ?>
			</div>

			<div class="element">
				<strong><?php echo JText::_('Frontpage'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'frontpage', '', $this->form->getTaintedValue('frontpage')); ?>
				<?php if ($this->form->hasError('frontpage')) : ?><div class="error-message"><?php echo $this->form->getError('frontpage'); ?></div><?php endif; ?>
			</div>

			<div class="element element-publish_up<?php echo ($this->form->hasError('publish_up') ? ' error' : ''); ?>">
				<strong><?php echo JText::_('Start Publishing'); ?></strong>
				<?php echo JHTML::_('element.calendar', $this->form->getTaintedValue('publish_up'), 'publish_up', 'publish_up', SubmissionController::CALENDAR_DATE_FORMAT, array('class' => 'calendar-element')); ?>
				<?php if ($this->form->hasError('publish_up')) : ?><div class="error-message"><?php echo $this->form->getError('publish_up'); ?></div><?php endif; ?>
			</div>

			<div class="element element-publish_down<?php echo ($this->form->hasError('publish_down') ? ' error' : ''); ?>">
				<?php
					if (!($publish_down = $this->form->getTaintedValue('publish_down')) || $publish_down == YDatabase::getInstance()->getNullDate()) {
						$publish_down  = JText::_('Never');
					}
				?>
				<strong><?php echo JText::_('Finish Publishing'); ?></strong>
				<?php echo JHTML::_('element.calendar', $publish_down, 'publish_down', 'publish_down', SubmissionController::CALENDAR_DATE_FORMAT, array('class' => 'calendar-element')); ?>
				<?php if ($this->form->hasError('publish_down')) : ?><div class="error-message"><?php echo $this->form->getError('publish_down'); ?></div><?php endif; ?>
			</div>

		</div>
		
		<div class="width50">

			<div class="element">
				<strong><?php echo JText::_('Categories'); ?></strong>
				<div><?php echo JHTML::_('zoo.categorylist', $this->application, array(), 'categories[]', 'size="15" multiple="multiple"', 'value', 'text', $this->form->getTaintedValue('categories')); ?></div>
				<?php if ($this->form->hasError('categories')) : ?><div class="error-message"><?php echo $this->form->getError('categories'); ?></div><?php endif; ?>
			</div>
		
		</div>
		
	</div>

	<div id="tag-area">
		<div class="input">
			<div class="hint"><?php echo JText::_('Add new tag');?></div>
			<input type="text" value="" autocomplete="off" />
			<button class="button-grey" type="button"><?php echo JText::_('Add tag')?></button>
			<div class="icon"></div>
		</div>
		<p><?php echo JText::_('Seperate multiple tags through commas'); ?>.</p>
		<div class="tag-list">
		<?php foreach ($tags as $tag) :?>
			<div>
				<a></a>
				<?php echo $tag; ?>
				<input type="hidden" value="<?php echo $tag; ?>" name="tags[]" />
			</div>
		<?php endforeach;?>
		</div>
		<?php if (count($this->lists['most_used_tags'])) : ?>
		<p><?php echo JText::_('Choose from the most used tags');?>.</p>
		<div class="tag-cloud">
			<?php foreach ($this->lists['most_used_tags'] as $tag) :?>
				<a class="button-grey" title="<?php echo $tag->items . ' ' . ($tag->items == 1 ? JText::_('submission') : JText::_('submissions')); ?>"><?php echo $tag->name; ?></a>
			<?php endforeach;?>
		</div>
		<?php endif; ?>
	</div>

</fieldset>

<script type="text/javascript">
    window.addEvent('domready', function() {
        new Zoo.Tag({url: '<?php echo JRoute::_($this->link_base.'&controller=submission', false); ?>'});
    });
</script>