<?php
/**
* @package   yoo_expo Template
* @file      default_item.php
* @version   5.5.2 December 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));

?>

<div class="item">
	
	<?php if ($this->item->params->get('show_email_icon')) : ?>
	<div class="icon email"><?php echo JHTML::_('icon.email', $this->item, $this->item->params, $this->access); ?></div>
	<?php endif; ?>

	<?php if ( $this->item->params->get( 'show_print_icon' )) : ?>
	<div class="icon print"><?php echo JHTML::_('icon.print_popup', $this->item, $this->item->params, $this->access); ?></div>
	<?php endif; ?>

	<?php if ($this->item->params->get('show_pdf_icon')) : ?>
	<div class="icon pdf"><?php echo JHTML::_('icon.pdf', $this->item, $this->item->params, $this->access); ?></div>
	<?php endif; ?>
	
	<?php if ($this->item->params->get('show_create_date')) : ?>
	<div class="date">
		<div class="day"><?php echo JHTML::_('date', $this->item->created, JText::_('%d')); ?></div>
		<div class="month"><?php echo JHTML::_('date', $this->item->created, JText::_('%B')); ?></div>
		<div class="year"><?php echo JHTML::_('date', $this->item->created, JText::_('%Y')); ?></div>
	</div>
	<?php endif; ?>
	
	<?php if ($this->item->params->get('show_title')) : ?>
	<h1 class="title">

		<?php if ($this->item->params->get('link_titles') && $this->item->readmore_link != '') : ?>
			<a href="<?php echo $this->item->readmore_link; ?>" title="<?php echo $this->escape($this->item->title); ?>"><?php echo $this->escape($this->item->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->item->title); ?>
		<?php endif; ?>

	</h1>
	<?php endif; ?>
	
	<?php
	
		if (!$this->item->params->get('show_intro')) {
			echo $this->item->event->afterDisplayTitle;
		}
	
		echo $this->item->event->beforeDisplayContent;
		
		if (isset ($this->item->toc)) {
			echo $this->item->toc;
		}
		
	?>
	
	<div class="content"><?php echo $this->item->text; ?></div>
	
	<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
	<p class="links">
		<a class="readmore" href="<?php echo $this->item->readmore_link; ?>" title="<?php echo $this->escape($this->item->title); ?>">
			<?php
				
				if ($this->item->readmore_register) {
					echo JText::_('Register to read more');
				} elseif ($readmore = $this->item->params->get('readmore')) {
					echo $readmore;
				} else {
					echo JText::sprintf('Continue Reading');
				}
				
			?>
		</a>
	</p>
	<?php endif; ?>
	
	<?php if ($canEdit) : ?>
	<p class="edit"><?php echo JHTML::_('icon.edit', $this->item, $this->item->params, $this->access); ?> <?php echo JText::_('Edit this article.'); ?></p>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>

</div>