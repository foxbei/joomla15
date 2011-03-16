<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<fieldset>
	<legend><?php echo JText::_('Select a Type and a Textarea to import article to'); ?></legend>
	<table class="admintable" width="100%">
		<?php foreach ($this->types as $type) : ?>
		<tr valign="top">
			<td width="110" class="key">
				<label for="name">
					<?php echo $type->name; ?>
				</label>
			</td>
			<td>
				<?php foreach ($type->getElements() as $element) : ?>
					<?php if ($element->getElementType() == "textarea") : ?>
						<a href="<?php echo JRoute::_($this->baseurl.'&task=doJoomlaImport&element='.$element->identifier.'&type='.$type->id); ?>"><?php echo $element->getConfig()->get('name'); ?></a>
						<br>
					<?php endif; ?>
				<?php endforeach; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	</fieldset>
	
</div>
	
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT; ?>