<?php
/**
* @package   ZOO Component
* @file      submission.php
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

// add page title
$page_title = sprintf(($this->item->id ? JText::_('Edit %s') : JText::_('Add %s')), $this->type->name);;
JFactory::getDocument()->setTitle($page_title);

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->submission->alias; ?>">

	<div class="submission">

		<h1 class="headline"><?php echo $page_title;?></h1>
		
		<?php 
		
			$fields = $this->renderer->render($this->layout_path, array('form' => $this->form));
			echo $this->partial('submission', compact('fields'));
		
		?>

	</div>

</div>