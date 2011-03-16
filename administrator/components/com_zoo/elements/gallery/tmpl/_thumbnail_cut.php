<?php
/**
* @package   ZOO Component
* @file      _thumbnail_cut.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="thumbnail">
	<div class="thumbnail-bg">
		<div class="corner-tl"></div>
		<div class="corner-tr"></div>
		<div class="corner-bl"></div>
		<div class="corner-br"></div>
				
		<a href="<?php echo $thumb['img']; ?>" title="<?php echo $thumb['name']; ?>" <?php echo $a_attribs; ?>>
			<img src="<?php echo $thumb['thumb']; ?>" alt="<?php echo $thumb['name']; ?>" width="<?php echo $thumb['thumb_width']; ?>" height="<?php echo $thumb['thumb_height']; ?>" />
		</a>

	</div>
</div>