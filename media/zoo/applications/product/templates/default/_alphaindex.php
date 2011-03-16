<?php
/**
* @package   ZOO Component
* @file      _alphaindex.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="alpha-index">
	<div class="alpha-index-2">
		<div class="alpha-index-3">
			<?php echo $this->alpha_index->render(RouteHelper::getAlphaIndexRoute($this->application->id)); ?>
		</div>
	</div>
</div>