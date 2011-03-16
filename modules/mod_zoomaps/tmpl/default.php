<?php
/**
* @package   ZOO Maps
* @file      default.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
JHTML::stylesheet('style.css', JURI::base().'modules/mod_zoomaps/tmpl/default/');

?>

<div class="yoo-maps" style="<?php echo $css_module_width ?>">

	<div id="<?php echo $maps_id ?>" style="<?php echo $css_module_width . $css_module_height ?>"></div>
	
	<?php foreach ($messages as $message) : ?>
	<div class="alert"><strong><?php echo $message; ?></strong></div>
	<?php endforeach; ?>
	
</div>