<?php
/**
* @package   ZOO Component
* @file      _alphaindex.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="alpha-index">
	<?php echo $this->alpha_index->render(RouteHelper::getAlphaIndexRoute($this->application->id)); ?>
</div>