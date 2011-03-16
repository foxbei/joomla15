<?php defined('_JEXEC') or die('Restricted access'); ?>

<form id="manager-info" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<fieldset>
		<legend><?php echo JText::_('Information'); ?></legend>

		<?php $metadata = $this->application->getMetaData(); ?>
		
		<?php if($info_image = $this->application->getInfoImage()) : ?>
		<img class="application-image" src="<?php echo $info_image;?>">
		<?php endif; ?>

		<div class="application-details">
			<h1><?php echo $metadata['name']; ?></h1>
			<h2>Version <?php echo $metadata['version']; ?> - <?php echo $metadata['creationdate']; ?></h2>
			<ul>
				<li><strong>Author:</strong> <?php echo $metadata['author']; ?></li>
				<li><strong>E-Mail:</strong> <a href="mailto:<?php echo $metadata['authorEmail']; ?>" target="_blank"><?php echo $metadata['authorEmail']; ?></a></li>
				<li><strong>Website:</strong> <a href="<?php echo $metadata['authorUrl']; ?>" target="_blank"><?php echo $metadata['authorUrl']; ?></a></li>
				<li><strong>Copyright:</strong> <?php echo $metadata['copyright']; ?></li>
				<li><strong>License:</strong> <?php echo $metadata['license']; ?></li>
			</ul>
			<div><?php echo $metadata['description']; ?></div>
		</div>
		
	</fieldset>

	<table class="list stripe">
	<thead>
		<tr>
			<th class="checkbox">
				<input type="checkbox" class="check-all" />
			</th>
			<th class="name" colspan="2">
				<?php echo JText::_('Name'); ?>
			</th>
			<th class="items">
				<?php echo JText::_('Items'); ?>
			</th>
			<th class="categories">
				<?php echo JText::_('Categories'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->applications as $application) : ?>
		<tr>
			<td class="checkbox">
				<input type="checkbox" name="cid[]" value="<?php echo $application->id; ?>" />
			</td>
			<td class="icon">
				<img src="<?php echo ZOO_ADMIN_URI; ?>assets/images/application.png" border="0" />
			</td>
			<td class="name">
				<?php echo $application->name; ?>
			</td>
			<td class="items">
				<?php echo $application->getItemCount(); ?>
			</td>
			<td class="categories">
				<?php echo $application->getCategoryCount(); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
</div>
	
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT; ?>