<?php
/**
* @package   ZOO Component
* @file      editoption.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

	defined('_JEXEC') or die('Restricted access');
	
?>
	
	<div class="name-input">
		<label for="name">Name</label>
		<input type="text" name="<?php echo $var.'[options]['.$num.'][name]'; ?>" value="<?php echo $name; ?>"></input>
	</div>
	<div class="value-input">
		<label for="value">Value</label>
		<a class="trigger" href="#" title="<?php echo JText::_('Edit Option Value');?>"><?php echo $value; ?></a>
		<div class="panel">
			<input type="text" name="<?php echo $var.'[options]['.$num.'][value]'; ?>" value="<?php echo $value; ?>"></input>
			<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>">
			<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
		</div>
	</div>
	<div class="delete" title="<?php echo JText::_('Delete option'); ?>">
		<img src="<?php echo ZOO_ADMIN_URI.'assets/images/delete.png'; ?>"/>
	</div>
	<div class="sort-handle" title="<?php echo JText::_('Sort option'); ?>">
		<img src="<?php echo ZOO_ADMIN_URI.'assets/images/sort.png'; ?>"/>
	</div>