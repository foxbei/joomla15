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

<div class="alpha-index <?php if ($this->params->get('template.alignment') == 'center') echo 'alpha-index-center'; ?>">
	<div class="alpha-index-1">
		<?php echo $this->alpha_index->render($this->link_base.'&task=alphaindex&app_id='.$this->application->id); ?>
	</div>		
</div>