<?php
/**
* @package   ZOO Component
* @file      full.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="box">
	<div>

		<?php if ($this->checkPosition('media')) : ?>
		<div class="pos-media">
			<?php echo $this->renderPosition('media'); ?>
		</div>
		<?php endif; ?>
		
		<?php if ($this->checkPosition('title')) : ?>
		<h4 class="pos-title">
			<?php echo $this->renderPosition('title'); ?>
		</h4>
		<?php endif; ?>
		
		<?php if ($this->checkPosition('description')) : ?>
		<div class="pos-description">
			<?php echo $this->renderPosition('description', array('style' => 'block')); ?>
		</div>
		<?php endif; ?>
		
		<?php if ($this->checkPosition('links')) : ?>
		<p class="pos-links">
			<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
		</p>
		<?php endif; ?>

	</div>
</div>

<?php if ($this->checkPosition('article')) : ?>
<div class="items">
	<?php echo $this->renderPosition('article', array('style' => 'block')); ?>
</div>
<?php endif; ?>
		
