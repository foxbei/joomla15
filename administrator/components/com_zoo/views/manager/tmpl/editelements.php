<?php defined('_JEXEC') or die('Restricted access'); 

// add script
JHTML::script('type.js', 'administrator/components/com_zoo/assets/js/');

JHTML::_('behavior.tooltip');

?>

<form id="manager-editelements" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-50">
		<fieldset>
			<legend><?php echo $this->type->name; ?></legend>
			<ul id="element-list" class="element-list">
			<?php
				$elements = $this->type->getElements();
				if ($elements !== false) {
					foreach ($elements as $element) {
						echo '<li class="element hideconfig">'.$this->partial('editelement', array('element' => $element, 'name' => $element->getConfig()->get('name'), 'var' => 'elements['.$element->identifier.']')).'</li>';
					}
				} 
			?>
			</ul>
		</fieldset>
	</div>
	
	<div id="add-element" class="col col-right width-50">
		<fieldset>
			<legend><?php echo JText::_('ELEMENT_LIBRARY'); ?></legend>
			<?php
				if (count($this->elements)) {
					$i = 0;
					echo '<div class="groups">';
					foreach ($this->elements as $group => $elements) {
						if ($i == round(count($this->elements)/2)) { echo '</div><div class="groups">'; }
						echo '<div class="elements-group-name">'.JText::_($group).'</div>';
						echo '<ul class="elements">';
						foreach ($elements as $element) {
							$element->loadConfigAssets();
							$metadata = $element->getMetaData();
							echo '<li class="'.$element->getElementType().'" title="'.JText::_('Add element').'">'.JText::_($metadata['name']).'</li>';
						}
						echo '</ul>';
						$i++;
					}
					echo '</div>';
				}			
			?>
		</fieldset>
	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->type->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	window.addEvent('domready', function() {
		new Zoo.EditElements({ url: '<?php echo JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&group='.$this->application->getGroup(), false); ?>', msgNoElements: '<?php echo JText::_('NO_ELEMENTS_DEFINED'); ?>', msgDeletelog: '<?php echo JText::_('DELETE_ELEMENT'); ?>' });
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>