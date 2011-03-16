<?php
/**
* @package   ZOO Category
* @file      list.php
* @version   2.1.0
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
JHTML::stylesheet('style.css', JURI::base().'modules/mod_zoocategory/tmpl/list/');

$count = count($categories);

?>

<div class="zoo-category list">
	
	<?php if ($count) : ?>

		<ul class="level1">
			<?php foreach ($categories as $category) : ?>
				<?php echo CategoryRenderer::render($category, $params, 2); ?>
			<?php endforeach; ?>
		</ul>
		
	<?php else : ?>
		<?php echo JText::_('No categories found'); ?>
	<?php endif; ?>
		
</div>