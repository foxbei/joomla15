<?php
/**
* @package   ZOO Slider
* @file      photo-h.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="<?php echo $theme ?>">
	<div id="<?php echo $slider_id ?>" class="yoo-slider <?php echo $theme ?>" style="<?php echo $css_module_width ?>">

		<ul class="list" style="<?php echo $css_module_height . $css_all_items_width?>">
		<?php $i = 0; ?>
		<?php foreach ($items as $item) : ?>
		
			<?php
			$item_class = "item item" . ($i + 1);
			if ($i == 0) $item_class .= " first";
			if ($i == $item_count - 1) $item_class .= " last";
			?>
			
			<li class="<?php echo $item_class ?>">
				<div class="slide" style="<?php echo $css_item_width . $css_item_height ?>">
					<div class="item"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
				</div>
			</li>
			<?php $i++; ?>
			
		<?php endforeach; ?>
		</ul>
	
	</div>
</div>