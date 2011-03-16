<?php
/**
* @package   ZOO Carousel
* @file      default.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo $theme ?>">
	<div id="<?php echo $carousel_id ?>" class="yoo-carousel" style="<?php echo $css_module_width . $css_module_height ?>">

		<div class="<?php echo $control_panel ?>" style="overflow: hidden; <?php echo $css_module_height /* needed for IE6 */?>">

			<?php if ($control_panel == 'top') : ?>
			<ul class="tabs" style="<?php echo $css_panel_width ?>">
				<?php $i = 0; ?>
				<?php foreach ($items as $item) : ?>
					<li class="button item<?php echo $i + 1 ?>">
						<a href="javascript:void(0)" title="<?php echo $item->name; ?>">
							<span><span><?php echo $item->name; ?></span></span>
						</a>
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
	
			<div class="frame" style="<?php echo $css_module_width ?>">
			
				<?php if ($buttons) : ?>
				<div class="prev">
					<a class="button-prev" href="javascript:void(0)" title="Previous slide">
						<span></span>
					</a>
				</div>
				<?php endif; ?>
			
				<div class="panel-container" style="<?php echo $css_panel_width ?>">
					<div class="panel-container-bl">
						<div class="panel-container-br" style="<?php echo $css_panel_height ?>">
							
							<div class="panel" style="<?php echo $css_panel_width ?>">
								<div style="<?php echo $css_total_panel_width ?>">
								<?php foreach ($items as $item) : ?>
									<div class="slide" style="<?php echo $css_panel_width ?><?php echo $css_slide_position ?>">
										<div class="item"><?php echo $renderer->render('item.'.$layout, compact('item', 'params')); ?></div>
									</div>
								<?php endforeach; ?>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			
				<?php if ($buttons) : ?>
				<div class="next">
					<a class="button-next" href="javascript:void(0)" title="Next slide">
						<span></span>
					</a>
				</div>
				<?php endif; ?>
	
			</div>
	
			<?php if ($control_panel == 'bottom') : ?>
			<ul class="tabs" style="<?php echo $css_panel_width ?>">
				<?php $i = 0; ?>
				<?php foreach ($items as $item) : ?>
					<li class="button item<?php echo $i + 1 ?>">
						<a href="javascript:void(0)" title="<?php echo $item->name; ?>">
							<span><span><?php echo $item->name; ?></span></span>
						</a>
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
	
		</div>
		
	</div>
</div>