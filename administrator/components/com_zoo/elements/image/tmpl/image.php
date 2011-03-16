<?php
/**
* @package   ZOO Component
* @file      image.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$target = ($target) ? 'target="_blank"' : '';
$rel	= ($rel) ? 'rel="' . $rel .'"' : '';

?>

<?php if ($file && JFile::exists($file)) : ?>

	<?php if ($link_enabled) : ?>
	<a href="<?php echo JRoute::_($url); ?>" <?php echo $target;?> <?php echo $rel;?> title="<?php echo $title; ?>">
	<?php endif ?>

	<?php $info = getimagesize($file); ?>
	
	<img src="<?php echo $link; ?>" title="<?php echo $title; ?>" alt="<?php echo $title; ?>" <?php echo $info[3]; ?> />
		
	<?php if ($link_enabled) : ?>
	</a>
	<?php endif ?>
	
<?php else : ?>

	<?php echo JText::_('No file selected.'); ?>
	
<?php endif; ?>