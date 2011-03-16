<?php
/**
* @package   ZOO Component
* @file      tag.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css/js
if (strtolower(substr($GLOBALS['mainframe']->getTemplate(), 0, 3)) != 'yoo') {
	JHTML::stylesheet('reset.css', 'media/zoo/assets/css/');
}
JHTML::stylesheet('zoo.css', $this->template->getURI().'/assets/css/');

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-tag'; ?>">

	<?php if ($this->params->get('template.show_alpha_index')) : ?>
		<?php echo $this->partial('alphaindex'); ?>
	<?php endif; ?>
	
	<?php if ($this->params->get('template.show_title')) : ?>
	<div class="details <?php echo 'align-'.$this->params->get('template.alignment'); ?>">
		<h1 class="title"><?php echo JText::_('Movies tagged with').': '.$this->tag; ?></h1>
	</div>
	<?php endif; ?>
	
	<?php

		// render items
		if (count($this->items)) {
			$has_categories = false;
			echo $this->partial('items', compact('has_categories'));
		}
		
	?>

</div>