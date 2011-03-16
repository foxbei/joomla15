<?php
/**
* @package   ZOO Item
* @file      list-h.php
* @version   2.3.2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
JHTML::stylesheet('style.css', JURI::base().'modules/mod_zooitem/tmpl/list-h/');

// include js
JHTML::script('mod_zooitem.js', JURI::base().'modules/mod_zooitem/');

$count = count($items);

?>

<div class="zoo-item list-h">

	<?php if ($count) : ?>

		<ul>
			<?php $i = 0; foreach ($items as $item) : ?>
			<li class="width<?php echo intval(100 / $count);?> <?php if ($i % 2 == 0) { echo 'odd'; } else { echo 'even'; } ?>">
				<div class="match-height"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
			</li>
			<?php $i++; endforeach; ?>
		</ul>
		
	<?php else : ?>
		<?php echo JText::_('No items found'); ?>
	<?php endif; ?>
		
</div>

<script type="text/javascript">
	jQuery(function($){
		$('div.zoo-item.list-h').each(function() { $(this).find('.match-height').matchHeight(); });
	});
</script>