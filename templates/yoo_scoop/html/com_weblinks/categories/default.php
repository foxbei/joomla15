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
defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="weblinks">

		<?php if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<?php if ( ($this->params->def('image', -1) != -1) || $this->params->def('show_comp_description', 1) ) : ?>
		<div class="description">
			<?php
				if ( isset($this->image) ) :  echo $this->image; endif;
				echo $this->params->get('comp_description');
			?>
		</div>
		<?php endif; ?>

		<ul>
			<?php foreach ( $this->categories as $category ) : ?>
			<li>
				<a href="<?php echo $category->link; ?>" class="category<?php echo $this->params->get( 'pageclass_sfx' ); ?>"><?php echo $this->escape($category->title);?></a>
				<span class="number">
					(<?php echo $category->numlinks;?>)
				</span>
			</li>
			<?php endforeach; ?>
		</ul>

	</div>
</div>