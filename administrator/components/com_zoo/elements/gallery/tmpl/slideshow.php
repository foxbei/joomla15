<?php
/**
* @package   ZOO Component
* @file      slideshow.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add javascript and css
JHTML::script('slideshow_packed.js', 'administrator/components/com_zoo/elements/gallery/assets/slideshow/');
JHTML::stylesheet('slideshow.css', 'administrator/components/com_zoo/elements/gallery/assets/slideshow/');

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
  	window.addEvent('domready', function(){
		<?php if ($spotlight) echo "var fx = new YOOgalleryfx('$gallery_id');"; ?>
		var show = new slideShow('<?php echo $container_id; ?>', '<?php echo $thumb_class; ?>', { wait: 5000, effect: '<?php echo $effect; ?>', duration: 1000, loop: true, thumbnails: true });
		show.play();
	});
</script>