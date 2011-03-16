<?php
/**
* @package   ZOO Component
* @file      googlemaps.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
$document = JFactory::getDocument();
$document->addScript('http://maps.google.com/maps/api/js?sensor=false&language='.$locale);
$document->addScript(ZOO_ADMIN_URI.'elements/googlemaps/googlemaps.js');

?>
<div class="googlemaps" style="<?php echo $css_module_width ?>">

	<?php if ($information) : ?>
	<p class="mapinfo"><?php echo $information; ?></p>
	<?php endif; ?>
	
	<div id="<?php echo $maps_id ?>" style="<?php echo $css_module_width . $css_module_height ?>"></div>
	
</div>
<?php echo "<script type=\"text/javascript\" defer=\"defer\">\n// <!--\n$javascript\n// -->\n</script>\n"; ?>