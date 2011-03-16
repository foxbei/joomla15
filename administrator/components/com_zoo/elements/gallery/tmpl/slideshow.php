<?php
/**
* @package   ZOO Component
* @file      slideshow.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add javascript and css
if (JPluginHelper::isEnabled('system', 'mtupgrade')) {
	JHTML::script('slideshow_packed.js', 'administrator/components/com_zoo/elements/gallery/assets/slideshow/');
	JHTML::stylesheet('slideshow.css', 'administrator/components/com_zoo/elements/gallery/assets/slideshow/');
} else {
	JHTML::script('slideshow_packed.js', 'administrator/components/com_zoo/elements/gallery/assets/mootools_old/slideshow/');
	JHTML::stylesheet('slideshow.css', 'administrator/components/com_zoo/elements/gallery/assets/mootools_old/slideshow/');
}

// init vars
$container_id = $gallery_id.'-con';
$thumb_class  = $gallery_id.'-thumb';
$thumb_tmpl   = sprintf('%s/_thumbnail_%s.php', dirname(__FILE__), $thumb);
$a_attribs    = 'class="'.$thumb_class.'"';
list($width, $height) = @getimagesize($thumbs[0]['img_file']);

?>
<div id="<?php echo $gallery_id; ?>" class="yoo-zoo yoo-gallery <?php echo $mode; ?> <?php echo $thumb; ?>">

	<div id="<?php echo $container_id; ?>" class="slideshow-bg" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;"></div>
	<div class="thumbnails">
	<?php 
		for ($j=0; $j < count($thumbs); $j++) :
			$thumb = $thumbs[$j];
			include($thumb_tmpl);
		endfor;
	?>
	</div>
	
</div>
<script type="text/javascript">
	
	<?php if ($spotlight): ?>
		jQuery(function($){	$('#<?php echo $gallery_id; ?>').YOOgalleryfx(); });
	<?php endif; ?>

	window.addEvent('domready', function() {
		<?php if (JPluginHelper::isEnabled('system', 'mtupgrade')) : ?>
			var show = new slideShow('<?php echo $container_id; ?>', '<?php echo $thumb_class; ?>', { wait: 5000, effect: '<?php echo $effect; ?>', duration: 1000, loop: true, thumbnails: true });
		<?php else: ?>
			var show = new SlideShow('<?php echo $container_id; ?>', '<?php echo $thumb_class; ?>', { wait: 5000, effect: '<?php echo $effect; ?>', duration: 1000, loop: true, thumbnails: true });
		<?php endif; ?>
		show.play();
	});
	
</script>