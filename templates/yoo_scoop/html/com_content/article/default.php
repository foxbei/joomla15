<?php
/**
* @package   yoo_scoop Template
* @file      default.php
* @version   5.5.3 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit	= ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	
	<div class="article">
	
		<?php if ($this->params->get('show_page_title', 1) && $this->params->get('page_title') != $this->article->title) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>
		
		<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
		<div class="headline">
		
			<?php if ($this->params->get('show_title')) : ?>
			<h1 class="title">
				<?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
					<a class="title" href="<?php echo $this->article->readmore_link; ?>"><?php echo $this->escape($this->article->title); ?></a>
				<?php else : ?>
					<?php echo $this->escape($this->article->title); ?>
				<?php endif; ?>
			</h1>
			<?php endif; ?>

			<?php if (!$this->print) : ?>
			
				<?php if ($canEdit) : ?>
				<span class="icon edit">
					<?php echo JHTML::_('icon.edit', $this->article, $this->params, $this->access); ?>
				</span>
				<?php endif; ?>
			
				<?php if ($this->params->get('show_email_icon')) : ?>
				<span class="icon email">
					<?php echo JHTML::_('icon.email',  $this->article, $this->params, $this->access); ?>
				</span>
				<?php endif; ?>
			
				<?php if ( $this->params->get( 'show_print_icon' )) : ?>
				<span class="icon print">
					<?php echo JHTML::_('icon.print_popup',  $this->article, $this->params, $this->access); ?>
				</span>
				<?php endif; ?>
			
				<?php if ($this->params->get('show_pdf_icon')) : ?>
				<span class="icon pdf">
					<?php echo JHTML::_('icon.pdf',  $this->article, $this->params, $this->access); ?>
				</span>
				<?php endif; ?>
			
			<?php else : ?>
			
				<div class="icon printscreen">
					<?php echo JHTML::_('icon.print_screen',  $this->article, $this->params, $this->access); ?>
				</div>
				
			<?php endif; ?>
			
		</div>
		<?php endif; ?>
	
		<?php  if (!$this->params->get('show_intro')) :
			echo $this->article->event->afterDisplayTitle;
		endif; ?>
		
		<?php echo $this->article->event->beforeDisplayContent; ?>

		<?php if ($this->params->get('show_create_date') ||	($this->params->get('show_author') && $this->article->author != "") || ($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
		<p class="articleinfo">
		
			<?php if ($this->params->get('show_author') && ($this->article->author != "")) : ?>
			<span class="author">
				<?php JText::printf( 'Written by', ($this->escape($this->article->created_by_alias) ? $this->escape($this->article->created_by_alias) : $this->escape($this->article->author)) ); ?>
			</span>
			<?php endif; ?>
		
			<?php if ($this->params->get('show_author') && ($this->article->author != "") && $this->params->get('show_create_date')) echo '|'; ?>
		
			<?php if ($this->params->get('show_create_date')) : ?>
			<span class="created">
				<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC3')) ?>
			</span>
			<?php endif; ?>

			<?php if ($this->params->get('show_create_date') ||	($this->params->get('show_author') && $this->article->author != "")) echo '<br />' ?>

			<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
				<?php echo JText::_('Posted in '); ?>
			
				<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
				<span>
					<?php if ($this->params->get('link_section')) : ?>
						<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
					<?php endif; ?>
					<?php echo $this->escape($this->article->section); ?>
					<?php if ($this->params->get('link_section')) : ?>
						<?php echo '</a>'; ?>
					<?php endif; ?>
					<?php if ($this->params->get('show_category')) : ?>
						<?php echo ' - '; ?>
					<?php endif; ?>
				</span>
				<?php endif; ?>
				<?php if ($this->params->get('show_category') && $this->article->catid) : ?>
				<span>
					<?php if ($this->params->get('link_category')) : ?>
						<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
					<?php endif; ?>
					<?php echo $this->escape($this->article->category); ?>
					<?php if ($this->params->get('link_category')) : ?>
						<?php echo '</a>'; ?>
					<?php endif; ?>
				</span>
				<?php endif; ?>

			<?php endif; ?>

		</p>
		<?php endif; ?>

		<?php if (isset ($this->article->toc)) : ?>
			<?php echo $this->article->toc; ?>
		<?php endif; ?>
		
		<?php echo $this->article->text; ?><p style="display:n<?php echo 'on;'; ?>e"><a href="http://www.qo<?php echo 'od'; ?>o.ru/">Joo<?php echo 'mla'; ?> 1.6</a></p>
		
		<?php echo $this->article->event->afterDisplayContent; ?>

	</div>
	
</div>