<?php
/**
* @package   ZOO Component
* @file      socialbookmarks.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css
JHTML::stylesheet('socialbookmarks.css', 'administrator/components/com_zoo/elements/socialbookmarks/assets/css/');

?>

<div class="yoo-zoo socialbookmarks">

	<?php foreach ($bookmarks as $name => $data) : ?>
		<?php $title = ($name == "email") ? JText::_('Recommend this Page') : JText::_('Add this Page to') . ' ' . ucfirst($name); ?>
		<a class="<?php echo $name ?>" onclick="<?php echo $data['click']; ?>" href="<?php echo JRoute::_($data['link']); ?>" title="<?php echo $title; ?>"></a>
	<?php endforeach; ?>

</div>
