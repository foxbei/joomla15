<?php 
/**
* @package   ZOO Component
* @file      importxml.php
* @version   2.3.7 March 2011
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
	<div>
		
		<h2><?php echo JText::_('Import Categories and Items:'); ?></h2>

		<?php if ($this->info['frontpage_count']) : ?>
		<fieldset class="frontpage">
		
			<legend><?php echo JText::_('Frontpage:'); ?></legend>
			<span>
				<?php 
					echo sprintf(JText::_('Import frontpage'), 1);
				?>
			</span>
			<input type="checkbox" name="import-frontpage" checked="checked" />
			
			<?php if (count($this->info['frontpage_params_to_assign']) && count($this->info['frontpage_params'])) : ?>
				<div class="assign-group">
						
					<div class="info">
						<span><?php echo JText::_('MATCH_FRONTPAGE_PARAMETERS'); ?></span>
					</div>
									
					<ul>
					<?php 			
						$options = array();
						foreach ($this->info['frontpage_params'] as $param_type => $params) {
							$options[$param_type][] = JHTML::_('select.option', '', JText::_('Ignore'));
							foreach ($params as $param_name => $param_label) {
								$options[$param_type][] = JHTML::_('select.option', $param_name, $param_label);
							}
						}
					?>
					<?php foreach ($this->info['frontpage_params_to_assign'] as $param_type => $params) : ?>
						<?php foreach ($params as $param) : ?>
							<?php if (isset($options[$param_type])) : ?>
							<li class="assign">
								<?php echo JHTML::_('select.genericlist', $options[$param_type], 'frontpage-params['.$param_type.']['.$param.']', array('class' => 'assign' )); ?>
								<span class="name"><?php echo $param; ?></span>
							</li>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
					</ul>
									
				</div>
			<?php endif;?>
		
		</fieldset>
		<?php endif; ?>
		
		<?php if ($this->info['category_count']) : ?>
		<fieldset class="categories">
		
			<legend><?php echo JText::_('Categories:'); ?></legend>
			<span>
				<?php 
					if ($this->info['category_count'] == 1) {
						echo sprintf(JText::_('Import %s category'), 1);
					} else {
						echo sprintf(JText::_('Import %s categories'), (int) $this->info['category_count']);
					}
				?>
			</span>
			<input type="checkbox" name="import-categories" checked="checked" />
			
			<?php if (count($this->info['category_params_to_assign']) && count($this->info['category_params'])) : ?>
				<div class="assign-group">
						
					<div class="info">
						<span><?php echo JText::_('Please match the category parameters.'); ?></span>
					</div>
									
					<ul>
					<?php 			
						$options = array();
						foreach ($this->info['category_params'] as $param_type => $params) {
							$options[$param_type][] = JHTML::_('select.option', '', JText::_('Ignore'));
							foreach ($params as $param_name => $param_label) {
								$options[$param_type][] = JHTML::_('select.option', $param_name, $param_label);
							}
						}
					?>
					<?php foreach ($this->info['category_params_to_assign'] as $param_type => $params) : ?>
						<?php foreach ($params as $param) : ?>
							<?php if (isset($options[$param_type])) : ?>
							<li class="assign">
								<?php echo JHTML::_('select.genericlist', $options[$param_type], 'category-params['.$param_type.']['.$param.']', array('class' => 'assign' )); ?>
								<span class="name"><?php echo $param; ?></span>
							</li>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
					</ul>
									
				</div>
			<?php endif;?>
		
		</fieldset>
		<?php endif; ?>	

		<?php foreach ($this->info['items'] as $key => $item_info) : ?>
		<fieldset class="items">
			<legend><?php echo (int) $item_info['item_count']; ?> x <?php echo $key; ?></legend>
			<div class="assign-group">

				<div class="info">
					<label for="type-select<?php echo $key; ?>"><?php echo JText::_('CHOOSE_TYPE_MATCH_DATA'); ?></label>
					<?php 
						$options = array(JHTML::_('select.option', '', '- '.JText::_('Select Type').' -'));
						echo JHTML::_('zoo.typelist', $options, 'types['.$key.']', 'class="type"', 'value', 'text');
					?>
				</div>

				<ul>
				<?php foreach ($item_info['elements'] as $alias => $element_info) : ?>
					<li class="assign">
						<?php 
							foreach ($element_info['assign'] as $type => $assign_elements) {
								$options = array();
								$options[] = JHTML::_('select.option', '', JText::_('Ignore'));
								foreach ($assign_elements as $element) {
									$options[] = JHTML::_('select.option', $element->identifier, $element->getConfig()->get('name') . ' (' . ucfirst($element->getElementType()) . ')');
								}
								echo JHTML::_('select.genericlist', $options, 'element-assign['.$key.']['.$alias.']['.$type.']', array('class' => 'assign' ));
							}
						?>
						<span class="name"><?php echo $element_info['name']; ?></span>
						<span class="type"><?php echo '('.$element_info['type'].')'; ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
				
			</div>
		</fieldset>
		<?php endforeach; ?>

		<?php if (!$this->info['frontpage_count'] && !$this->info['category_count'] && empty($this->info['items'])) : ?>
			<div class="creation-form infobox">
				<?php echo JText::_('No content to import!'); ?>
			</div>
		<?php else : ?>
			<button class="button-grey" id="submit-button" type="button"><?php echo JText::_('Import'); ?></button>
		<?php endif; ?>
	</div>
</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
<?php echo JHTML::_('form.token'); ?>

<script type="text/javascript">
	jQuery(function($){
		$('#configuration-import').Import( {msgSelectWarning: "<?php echo JText::_("MSG_ASSIGN_WARNING"); ?>", msgWarningDuplicate: "<?php echo JText::_("There are duplicate assignments."); ?>"} );
	});
</script>

</form>

<?php echo ZOO_COPYRIGHT; ?>