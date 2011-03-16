<?php
/**
* @package   ZOO Component
* @file      alphaindex.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
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

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-alphaindex'; ?>">

	<?php if ($this->params->get('template.show_alpha_index')) : ?>
		<?php echo $this->partial('alphaindex'); ?>
	<?php endif; ?>

	<div class="details">

		<h1 class="title">
			<?php echo JText::_('Categories starting with').' '.strtoupper($this->alpha_char); ?>
		</h1>

	</div>

	<?php

		// render categories
		if (!empty($this->selected_categories)) {
			echo $this->partial('categories');
		}

	?>
	
	<?php

		// render items
		if (count($this->items)) {
			$title = JText::_('Items starting with').' '.strtoupper($this->alpha_char);
			$subtitle = '';
			echo $this->partial('items', compact('title', 'subtitle'));
		}
		
	?>

</div>