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

$id = 'elements['.$element.']';
?>

<div id="<?php echo $id; ?>">
    <div class="row">
        <?php echo JHTML::_('control.text', 'elements['.$element.'][location]', $location, 'maxlength="255" title="'.JText::_('Location').'" placeholder="'.JText::_('Location').'"'); ?>
    </div>
</div>