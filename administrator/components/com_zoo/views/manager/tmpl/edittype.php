<?php 
/**
* @package   ZOO Component
* @file      edittype.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

defined('_JEXEC') or die('Restricted access'); 

JHTML::script('alias.js', 'administrator/components/com_zoo/assets/js/');
JHTML::script('type.js', 'administrator/components/com_zoo/assets/js/');

// filter output
JFilterOutput::objectHTMLSafe($this->type, ENT_QUOTES); 
?>

<form id="manager-typeedit" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<fieldset class="creation-form">
		<legend><?php echo JText::_('Details'); ?></legend>
		<div class="element element-name">
			<strong><?php echo JText::_('Name'); ?></strong>
			<div id="name-edit">
				<div class="row">
					<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->type->name; ?>" />
					<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
				</div>
				<div class="slug">
					<span><?php echo JText::_('Slug'); ?>:</span>
					<a class="trigger" href="#" title="<?php echo JText::_('Edit Type Slug');?>"><?php echo $this->type->id; ?></a>
					<div class="panel">
						<input type="text" name="identifier" value="<?php echo $this->type->id; ?>" />
						<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>"/>
						<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->type->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($){
		$('#manager-typeedit').EditType();
		$('#name-edit').AliasEdit({ edit: <?php echo (int)$this->edit; ?>, edit_field_name: 'identifier' });
		$('#name-edit').find('input[name="name"]').focus();
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>