<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$id = 'elements['.$element.']['.$index.']';

?>

<div id="<?php echo $id; ?>">

	<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][value]', $link, 'size="60" maxlength="255" title="'.JText::_('Link').'"'); ?>

	<div class="more-options">
		<div class="trigger">
			<div>
				<div class="advanced button hide"><?php echo JText::_('Hide Options'); ?></div>
				<div class="advanced button"><?php echo JText::_('Show Options'); ?></div>
			</div>
		</div>

		<div class="advanced options">
			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][text]', $text, 'size="60" maxlength="255" title="'.JText::_('Text').'" placeholder="'.JText::_('Text').'"'); ?>
			</div>

			<div class="row">
				<strong><?php echo JText::_('New window'); ?></strong>
				<?php echo JHTML::_('select.booleanlist', 'elements['.$element.']['.$index.'][target]', $target, $target); ?>
			</div>

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][custom_title]', $title, 'size="60" maxlength="255" title="'.JText::_('Title').'" placeholder="'.JText::_('Title').'"'); ?>
			</div>

			<div class="row">
				<?php echo JHTML::_('control.text', 'elements['.$element.']['.$index.'][rel]', $rel, 'size="60" maxlength="255" title="'.JText::_('Rel').'" placeholder="'.JText::_('Rel').'"'); ?>
			</div>
		</div>
	</div>
</div>