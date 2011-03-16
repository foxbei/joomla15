<?php
/**
* @package   ZOO Item
* @file      list-v.php
* @version   2.3.2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
JHTML::stylesheet('style.css', JURI::base().'modules/mod_zooitem/tmpl/list-v/');

?>

<div class="zoo-item list-v">

	<?php if (!empty($items)) : ?>

		<ul>
			<?php $i = 0; foreach ($items as $item) : ?>
			<li class="<?php if ($i % 2 == 0) { echo 'odd'; } else { echo 'even'; } ?>">
				<?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?>
			</li>
			<?php $i++; endforeach; ?>
		</ul>
	
	<?php else : ?>
		<?php echo JText::_('No items found'); ?>
	<?php endif; ?>
	
</div>