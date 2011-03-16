<?php
/**
* @package   ZOO Component
* @file      item.php
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

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->item->alias; ?>">

	<div class="item">
		<?php echo $this->renderer->render('item.full', array('view' => $this, 'item' => $this->item)); ?>
				
		<?php if ($this->item->getApplication()->isCommentsEnabled() && ($this->item->isCommentsEnabled() || $this->item->getCommentsCount(1))) : ?>
		<div class="feedback">
		
			<div class="box-t1">
				<div class="box-t2">
					<div class="box-t3"></div>
				</div>
			</div>
			
			<div class="box-1">
				<?php echo CommentHelper::renderComments($this, $this->item); ?>
			</div>
			
			<div class="box-b1">
				<div class="box-b2">
					<div class="box-b3"></div>
				</div>
			</div>
		
		</div>
		<?php endif; ?>

	</div>

</div>