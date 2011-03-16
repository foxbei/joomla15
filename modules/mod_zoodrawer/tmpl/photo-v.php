<?php
/**
* @package   ZOO Drawer
* @file      photo-v.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/
/**
* YOOdrawer Joomla! Module
*
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo $theme ?>">
	<div id="<?php echo $drawer_id ?>" class="yoo-drawer <?php echo $theme ?>">

		<ul class="list" style="<?php echo $css_module_height ?>">
		<?php $i = 0; ?>
		<?php foreach ($items as $item) : ?>
		
			<?php
			$item_class = "item item" . ($i + 1);
			if ($i == 0) $item_class .= " first active";
			if ($i == count($items) - 1) $item_class .= " last";
			$item_style = "position: absolute; top: " . ($item_minimized * $i) . "px; z-index: " . (count($items) - $i) . ";";
			?>

			<li class="<?php echo $item_class ?>" style="<?php echo $item_style ?>">
				<div style="<?php echo $css_item_height ?>">
					<div class="zooitem"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
				</div>
			</li>
			<?php $i++; ?>
		<?php endforeach; ?>
		</ul>
		
	</div>
</div>