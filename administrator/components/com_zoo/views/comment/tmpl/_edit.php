<?php
/**
* @package   ZOO Component
* @file      _edit.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// filter content
JFilterOutput::objectHTMLSafe($this->comment->content);

?>

<tr id="edit-comment-editor">
	<td colspan="4">
		<div class="head">
			<label for="author">Name</label>
			<input id="author" type="text" name="author" value="<?php echo $this->comment->author; ?>" />
			<label for="email">E-mail</label>
			<input id="email" type="text" name="email" value="<?php echo $this->comment->email; ?>" />
			<label for="url">URL</label>
			<input id="url" type="text" name="url" value="<?php echo $this->comment->url; ?>" />
		</div>
		<div class="content">
			<textarea name="content" cols="" rows=""><?php echo $this->comment->content; ?></textarea>
		</div>
		<div class="actions">
			<button class="save" type="button"><?php echo JText::_('Update Comment'); ?></button>
			<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
		</div>
		<input type="hidden" name="cid" value="<?php echo $this->comment->id; ?>" />
	</td>
</tr>