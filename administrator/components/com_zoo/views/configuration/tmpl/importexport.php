<?php 
/**
* @package   ZOO Component
* @file      importexport.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
JHTML::script('import.js', 'administrator/components/com_zoo/assets/js/');

$app_category_count = (int) $this->application->getCategoryCount();
$app_item_count 	= (int) $this->application->getItemCount();

?>

<form id="configuration-importexport" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="uploadbox importbox">
		<div>
			<h3><?php echo JText::_('Import from XML:'); ?></h3>
			<input type="text" class="filename" readonly="readonly" />
			<div class="button-container">
			  <button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
			  <input type="file" accept="application/xml" name="import-xml" />
			</div>
			<button class="button-green upload" type="button"><?php echo JText::_('Upload'); ?></button>
		</div>
	</div>

	<div class="importbox">
		<div>
			<h3><?php echo JText::_('EXPORT_APP_INSTANCE'); ?></h3>
			

			<?php if ($app_category_count || $app_item_count) : ?>

				<button class="button-grey export" type="button">
					<span><?php echo JText::_('Start Export'); ?></span>
					<?php echo $app_category_count; ?> <?php echo $app_category_count == 1 ? JText::_('Category') : JText::_('Categories');?>
					<?php echo JText::_('and'); ?>
					<?php echo $app_item_count; ?> <?php echo $app_item_count == 1 ? JText::_('Item') : JText::_('Items'); ?>
				</button>
			
			<?php else : ?>
			
				<?php echo JText::_('NO_CATEGORIES_NO_ITEMS')?>
			
			<?php endif; ?>
		
		</div>
	</div>

	<div class="uploadbox importbox">
		<div>
			<h3><?php echo JText::_('Import from CSV:'); ?></h3>
			<input type="text" class="filename" readonly="readonly" />
			<div class="button-container">
			  <button class="button-grey search" type="button"><?php echo JText::_('Search'); ?></button>
			  <input type="file" accept="text/x-comma-separated-values" name="import-csv" />
			</div>
			<button class="button-green upload" type="button"><?php echo JText::_('Upload'); ?></button>
		</div>
	</div>

	<div class="importbox">
		<div>
			<h3><?php echo JText::_('IMPORT_INSTALLED_SOFTWARE'); ?></h3>
			
			<?php foreach ($this->exporter as $exporter) : ?>
				<?php if ($exporter->isEnabled()) : ?>
					<a id="<?php echo $exporter->getType();?>" class="button-grey exporter-link" href="javascript:void(0);">
						<?php echo $exporter->getName(); ?>
					</a>
				<?php else : ?>
					<a id="<?php echo $exporter->getType();?>" class="button-disabled" href="javascript:void(0);">
						<?php echo $exporter->getName(); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		
		</div>
	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="format" value="html" />
<input type="hidden" name="exporter" value="" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($){
		$('#configuration-importexport').ImportExport({ msgImportWarning: '<?php echo JText::_('SELECT_FILE_FIRST');?>'});
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>