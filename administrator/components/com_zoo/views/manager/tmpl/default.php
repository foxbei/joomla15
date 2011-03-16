<?php defined('_JEXEC') or die('Restricted access'); ?>

<form id="manager-default" action="index.php" method="post" name="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="application-list">
	
		<h2><?php echo JText::_('SELECT_APP_CONFIGURE'); ?></h2>

		<?php foreach ($this->applications as $application) : ?>
			<a href="<?php echo JRoute::_($this->baseurl.'&task=types&group='.$application->getGroup()); ?>">
				<span>
					<img src="<?php echo $application->getIcon();?>">					
					<?php $metadata = $application->getMetaData(); ?>
					<?php echo $metadata['name']; ?>
				</span>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="importbox uploadbox">
		<div>
			<h3><?php echo JText::_('Install a new App'); ?></h3>
			<input type="text" id="filename" readonly="readonly" />
			<div class="button-container">
			  <button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
			  <input type="file" name="install_package" onchange="javascript: document.getElementById('filename').value = this.value" />
			</div>
			<button class="button-green upload" type="button"><?php echo JText::_('Upload'); ?></button>
		</div>
	</div>
	
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="installtype" value="upload" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	window.addEvent('domready', function(){

		$E('button.upload', 'manager-default').addEvent('click', function () {
			if ($('filename').getValue() == '') {
				alert('<?php echo JText::_('SELECT_FILE_FIRST');?>');
			} else {
				submitbutton('installapplication');
			}
		});
		
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>