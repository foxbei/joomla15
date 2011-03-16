<?php
/**
* @package   ZOO Component
* @file      _mysubmissions.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('submission.css', 'media/zoo/assets/css/');
JHTML::script('submission.js', 'media/zoo/assets/js/');

$mysubmissions_link = RouteHelper::getMySubmissionsRoute($this->submission);

?>

<div class="toolbar">

	<div class="submission-add">
		<div class="trigger" title="<?php echo JText::_('Add Item'); ?>"><?php echo JText::_('Add Item'); ?></div>
		<div class="links">
		<?php foreach($this->types as $id => $type) : ?>
			<?php $add_hash = SubmissionHelper::getSubmissionHash($this->submission->id, $id); ?>
			<?php $add_link = RouteHelper::getSubmissionRoute($this->submission, $id, $add_hash, null, 'mysubmissions'); ?>
			<div class="add-link">
				<a href="<?php echo JRoute::_($add_link); ?>" title="<?php echo sprintf(JText::_('Add %s'), $type->name); ?>"><?php echo $type->name; ?></a>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
	
	<?php if (isset($this->lists['select_type'])) : ?>
	<form id="submission-filter" action="<?php echo JRoute::_($mysubmissions_link); ?>" method="post" name="adminForm" accept-charset="utf-8">
		<?php echo $this->lists['select_type']; ?>
	</form>
	<?php endif; ?>
	
</div>

<?php if (count($this->items)) : ?>
<ul class="submissions">

	<?php foreach ($this->items as $id => $item) : ?>
	<li>
		
		<div class="header">
			<?php $edit_hash = SubmissionHelper::getSubmissionHash($this->submission->id, $item->type, $id); ?>
			<?php $edit_link = RouteHelper::getSubmissionRoute($this->submission, $item->type, $edit_hash, $id, 'mysubmissions'); ?>
			<?php if ($this->submission->isInTrustedMode()) : ?>
				<a href="<?php echo JRoute::_($this->link_base.'&controller=submission&submission_id='.$this->submission->id.'&task=remove&item_id='.$id); ?>" title="<?php echo JText::_('Delete Item'); ?>" class="item-icon delete-item"></a>
			<?php endif; ?>
			<a href="<?php echo JRoute::_($edit_link); ?>" title="<?php echo JText::_('Edit Item'); ?>" class="item-icon edit-item"></a>
			<h3 class="toggler"><?php echo $item->name; ?> <span>(<?php echo $item->getType()->name; ?>)</span></h3>
		 </div>

		<?php $this->params = $item->getParams('site'); ?>
		<?php $type = ($this->renderer->pathExists('item/'.$item->type)) ? $item->type : 'item'; ?>
		<div class="preview hidden <?php echo $type; ?>">
			<?php				
				$layout  = 'item.'.($type != 'item' ? $item->type . '.' : '');
				echo $this->renderer->render($layout.'full', array('view' => $this, 'item' => $item));
			?>
		</div>
			
	</li>
	<?php endforeach; ?>
	
</ul>
<?php else : ?>

	<?php $type = $this->filter_type; ?>
	<p class="no-submissions"><?php echo sprintf(JText::_('You have not submitted any %s items yet.'), $this->filter_type); ?></p>

<?php endif; ?>

<div class="pagination">
	<?php $pagination_link = !empty($this->filter_type) ? $mysubmissions_link . '&filter_type=' . $this->filter_type : $mysubmissions_link; ?>
	<?php echo $this->pagination->render($pagination_link); ?>
</div>

<script type="text/javascript">
	jQuery(function($){
		$('#yoo-zoo').SubmissionMysubmissions({ msgDelete: '<?php echo JText::_('Are you sure you want to delete this submission?'); ?>' });
	});
</script>