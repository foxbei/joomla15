<?php
/**
* @package   ZOO Accordion
* @file      whitespace.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo $theme ?>">
	<div id="<?php echo $accordion_id ?>" class="yoo-accordion">
	
		<dl>
		<?php 
			
			$i = 1;
			foreach ($items as $item) :

				$item_class = "item" . ($i);
				if ($i == 1) $item_class .= " first";
				if ($i == count($items)) $item_class .= " last";
				?>
				<dt class="toggler <?php echo $item_class; ?>">
					<span class="header-l">
						<span class="header-r">
							<?php echo $item->name ?>
						</span>
					</span>
				</dt>
				<dd class="content <?php echo $item_class; ?>">
					<div class="item"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
				</dd>
				
		<?php 
				$i++;
			endforeach; 
		?>
		</dl>

	</div>
</div>