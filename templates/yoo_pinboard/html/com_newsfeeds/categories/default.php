<?php
/**
* @package   yoo_pinboard Template
* @file      default.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="newsfeeds">

		<?php if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<?php if ( ($this->params->get('image') != -1) || $this->params->get('show_comp_description') ) : ?>
		<div class="description">
			<?php
				if ( isset($this->image) ) :  echo $this->image; endif;
				echo $this->escape($this->params->get('comp_description'));
			?>
		</div>
		<?php endif; ?>

		<ul>
			<?php foreach ( $this->categories as $category ) : ?>
			<li>
				<a href="<?php echo $category->link ?>"><?php echo $this->escape($category->title);?></a>
				
				<?php if ( $this->params->get( 'show_cat_items' ) ) : ?>
					<span class="number">
						(<?php echo $category->numlinks;?>)
					</span>
				<?php endif; ?>
				
				<?php if ( $this->params->get( 'show_cat_description' ) && $category->description ) : ?>
					<br /><?php echo $category->description; ?>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>

	</div>
</div>