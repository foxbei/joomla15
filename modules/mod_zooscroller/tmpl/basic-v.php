<?php
/**
* @package   ZOO Scroller
* @file      basic-v.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="<?php echo $theme ?>">
	<div id="<?php echo $scroller_id ?>" class="yoo-scroller" style="<?php echo $css_module_width . $css_module_height ?>">

		<div class="panel" style="<?php echo $css_panel_width . $css_panel_height ?>">
			<?php foreach ($items as $item) : ?>
				<div class="slide" style="<?php echo $css_slide_width . $css_slide_height ?>">
					<div class="item"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ($scrollbar) : ?>
		<div class="scrollarea" style="<?php echo $css_scrollarea_width . $css_scrollarea_height ?>">
			<div class="back"></div>
			<div class="scrollbar" style="<?php echo $css_scrollbar_width . $css_scrollbar_height ?>">
				<div class="scrollknob">
					<div class="scrollknob-t">
						<div class="scrollknob-b scrollknob-size">
						</div>
					</div>
				</div>
			</div>
			<div class="forward"></div>
		</div>
		<?php endif; ?>

	</div>
</div>