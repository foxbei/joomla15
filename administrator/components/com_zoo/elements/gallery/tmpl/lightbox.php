<?php
/**
* @package   ZOO Component
* @file      lightbox.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$a_attribs  = ($rel != '') ? 'rel="'.$rel.'"' : 'rel="lightbox['.$gallery_id.']"';
$thumb_tmpl = sprintf('%s/_thumbnail_%s.php', dirname(__FILE__), $thumb);

?>
<div id="<?php echo $gallery_id; ?>" class="yoo-zoo yoo-gallery <?php echo $mode; ?> <?php echo $thumb; ?>">

	<div class="thumbnails">
	<?php 
		for ($j=0; $j < count($thumbs); $j++) :
			$thumb = $thumbs[$j];
			include($thumb_tmpl);
		endfor;
	?>
	</div>
	
</div>
<?php if ($spotlight) : ?>
<script type="text/javascript">
	jQuery(function($){	$('#<?php echo $gallery_id; ?>').YOOgalleryfx(); });
</script>
<?php endif; ?>