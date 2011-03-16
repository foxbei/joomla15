<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

	defined('_JEXEC') or die('Restricted access');

	// add script
	JHTML::script('alias.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('submission.js', 'administrator/components/com_zoo/assets/js/');

	// filter output
	JFilterOutput::objectHTMLSafe($this->submission, ENT_QUOTES, array('params'));

?>

<form id="submission-edit" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">
		<div class="col col-left width-60">

			<fieldset class="creation-form">
				<legend><?php echo JText::_('Details'); ?></legend>
				<div class="element element-name">
					<strong><?php echo JText::_('Name'); ?></strong>
					<div id="name-edit">
						<div class="row">
							<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->submission->name; ?>" />
							<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
						</div>
						<div class="slug">
							<span><?php echo JText::_('Slug'); ?>:</span>
							<a class="trigger" href="#" title="<?php echo JText::_('Edit Submission Slug');?>"><?php echo $this->submission->alias; ?></a>
							<div class="panel">
								<input type="text" name="alias" value="<?php echo $this->submission->alias; ?>" />
								<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>"/>
								<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="element element-published">
					<strong><?php echo JText::_('Published'); ?></strong>
					<?php echo $this->lists['select_published']; ?>
				</div>
				<div class="element element-tooltip">
					<strong><?php echo JText::_('Tooltip'); ?></strong>
					<?php echo $this->lists['select_tooltip']; ?>
				</div>
			</fieldset>
		   <fieldset class="creation-form">
			   <legend><?php echo JText::_('Security'); ?></legend>
				<div class="element element-access-level">
					<strong><?php echo JText::_('Access level'); ?></strong>
					<?php echo JHTML::_('list.accesslevel', $this->submission); ?>
				</div>
				<div class="element element-trusted-mode">
					<strong><?php echo JText::_('Trusted Mode'); ?></strong>
					<input type="checkbox" name="params[trusted_mode]" class="trusted" <?php echo $this->submission->isInTrustedMode() ? 'checked="checked"' : ''; ?> />
					<span><?php echo JText::_('TRUSTED_MODE_DESCRIPTION'); ?></span>
				</div>
		   </fieldset>
		   <fieldset>
				<legend><?php echo JText::_('Types'); ?></legend>
				<?php if (count($this->types)) : ?>
				<table class="admintable">
					<thead>
						<tr>
							<th class="type">
								<?php echo JText::_('Type'); ?>
							</th>
							<th class="layout">
								<?php echo JText::_('Layout'); ?>
							</th>
							<th class="category">
								<?php echo JText::_('Sort into Category (only in none trusted mode)'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->types as $type) : ?>
						<tr>
							<td class="name">
								<?php echo $type['name'];?>
							</td>
							<td class="layout">
								<?php echo $type['select_layouts'];?>
							</td>
							<td class="category">
								<?php echo $type['select_categories']?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else: ?>
					<span class="no-types"><?php echo JText::_('No submission layouts available'); ?></span>
				<?php endif; ?>
		   </fieldset>

		</div>
	</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid[]" value="<?php echo $this->submission->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($){
		$('#submission-edit').EditSubmission();
		$('#name-edit').AliasEdit({ edit: <?php echo (int) $this->submission->id; ?> });
		$('#name-edit').find('input[name="name"]').focus();
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>