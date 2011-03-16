<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.3.7 March 2011
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
        <?php echo JHTML::_('control.text', 'elements['.$element.'][value]', $tags, 'maxlength="255" title="'.JText::_('Tags').'" placeholder="'.JText::_('Tags').'"'); ?>
    </div>

    <div class="row">
        <?php echo JHTML::_('control.text', 'elements['.$element.'][flickrid]', $flickrid, 'maxlength="255" title="'.JText::_('Flickr ID').'" placeholder="'.JText::_('Flickr ID').'"'); ?>
    </div>

</div>