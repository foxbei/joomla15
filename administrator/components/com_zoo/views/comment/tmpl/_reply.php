<?php
/**
* @package   ZOO Component
* @file      _reply.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="head">Reply to Comment</div>
<div class="content">
	<textarea name="content"></textarea>
</div>
<div class="actions">
	<button class="save"><?php echo JText::_('Submit Reply'); ?></button>
	<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
</div>
<input type="hidden" name="cid" value="0" />
<input type="hidden" name="parent_id" value="<?php echo $this->cid; ?>" />