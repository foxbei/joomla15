<?php
/**
* @package   ZOO Component
* @file      teaser.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($this->checkPosition('title')) : ?>
<h2 class="pos-title">
	<?php echo $this->renderPosition('title'); ?>
</h2>
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