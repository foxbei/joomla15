<?php 
/**
* @package   ZOO Component
* @file      importcsv.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
JHTML::script('import.js', 'administrator/components/com_zoo/assets/js/');

?>

<form id="configuration-import" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8" enctype="multipart/form-data">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-60">
		
		<h2><?php echo JText::_('CSV Import'); ?>:</h2>
		<fieldset class="items">
			<legend><?php echo (int) $this->info['item_count']; ?> x <?php echo JText::_('Items'); ?></legend>
			<div class="assign-group">

				<div class="info">
					<label for="type-select"><?php echo JText::_('CHOOSE_TYPE_MATCH_DATA'); ?></label>
					<?php
						$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Type').' -'));
						echo JHTML::_('zoo.typelist', $options, 'type', 'class="type"', 'value', 'text');
					?>
				</div>

				<ul>
				<?php foreach ($this->info['columns'] as $key => $column) : ?>
					<li class="assign">
						<?php
							foreach ($this->info['types'] as $type => $element_types) {
								$options = array();
								$options[] = JHTML::_('select.option', '', JText::_('Ignore'));
								$options[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_('Core Atributes') );
								$options[] = JHTML::_('select.option', '_name', JText::_('Name'));
								$options[] = JHTML::_('select.option', '_category', JText::_('Category'));
								$options[] = JHTML::_('select.option', '_created_by_alias', JText::_('Author Alias'));
								$options[] = JHTML::_('select.option', '_created', JText::_('Created Date'));
								$options[] = JHTML::_('select.option',  '</OPTGROUP>' );

								$options[] = JHTML::_('select.option',  '<OPTGROUP>', JText::_('Elements') );
								foreach ($element_types as $element_type => $elements) {
									foreach ($elements as $element) {
										$options[] = JHTML::_('select.option', $element->identifier, $element->getConfig()->get('name') . ' (' . ucfirst($element->getElementType()) . ')');
									}
								}
								$options[] = JHTML::_('select.option',  '</OPTGROUP>' );
								echo JHTML::_('select.genericlist', $options, 'element-assign['.$key.']['.$type.']', array('class' => 'assign' ));
							}
						?>
						<span class="name"><?php echo empty($column) ? JText::_('Column') . ' ' . ($key + 1) : $column; ?></span>
					</li>
				<?php endforeach; ?>
				</ul>

			</div>
		</fieldset>
		
		<button class="button-grey" id="submit-button" type="button"><?php echo JText::_('Import'); ?></button>
	
	</div>

	<h2><?php echo JText::_('Information'); ?>:</h2>
	<div class="col col-right width-40">
		<div class="creation-form infobox">
			<p><?php echo JText::_("CSV-IMPORT-INFO-1"); ?></p>
			<p><?php echo JText::_("CSV-IMPORT-INFO-2"); ?></p>
			<p><?php echo JText::_("CSV-IMPORT-INFO-3"); ?></p>
		</div>
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="contains-headers" value="<?php echo $this->contains_headers; ?>" />
<input type="hidden" name="field-separator" value="<?php echo htmlentities($this->field_separator); ?>" />
<input type="hidden" name="field-enclosure" value="<?php echo htmlentities($this->field_enclosure); ?>" />
<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
<?php echo JHTML::_('form.token'); ?>

<script type="text/javascript">
	jQuery(function($){
		$('#configuration-import').Import({ msgNameWarning: "<?php echo JText::_("Please choose a name column."); ?>", msgSelectWarning: "<?php echo JText::_("MSG_ASSIGN_WARNING"); ?>", msgWarningDuplicate: "<?php echo JText::_("There are duplicate assignments."); ?>", task: "doimportcsv" });
	});
</script>

</form>

<?php echo ZOO_COPYRIGHT; ?>