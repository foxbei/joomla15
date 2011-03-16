<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$id = 'elements['.$element.']';
?>

<div id="<?php echo $id; ?>">

    <div class="row">
        <label for="elements[<?php echo $element; ?>][location]"><?php echo JText::_('Location'); ?></label>
        <?php echo JHTML::_('control.text', 'elements['.$element.'][location]', $location, 'maxlength="255" title="'.JText::_('Location').'"'); ?>
    </div>

    <script type="text/javascript">
        window.addEvent('domready', function(){
			new Zoo.EditElement({element: '<?php echo $id; ?>'});
        });
    </script>

</div>