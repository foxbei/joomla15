<?php
/**
* @package   ZOO Component
* @file      full.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($this->checkPosition('top')) : ?>
<div class="pos-top">
	<?php echo $this->renderPosition('top', array('style' => 'block')); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h1 class="pos-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<div class="box">

	<?php if ($this->checkPosition('media')) : ?>
	<div class="pos-media">
		<?php echo $this->renderPosition('media'); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('right')) : ?>
	<div class="pos-right">
		<?php echo $this->renderPosition('right'); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->checkPosition('specification')) : ?>
	<ul class="pos-specification">
		<?php echo $this->renderPosition('specification', array('style' => 'list')); ?>
	</ul>
	<?php endif; ?>
	
	<?php if ($this->checkPosition('button')) : ?>
	<div class="pos-button">
		<?php echo $this->renderPosition('button'); ?>
	</div>
	<?php endif; ?>
	
</div>

<?php if ($this->checkPosition('bottom')) : ?>
<div class="pos-bottom">
	<?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
</div>
<?php endif; ?>