<?php
/**
* @package   yoo_pinboard Template
* @file      vert.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php if (count($list) == 1) : ?>
	<?php $item = $list[0]; ?>
	<div class="module-newsflash">
		<?php modNewsFlashHelper::renderItem($item, $params, $access); ?>
	</div>
<?php elseif (count($list) > 1) : ?>
	<div class="module-newsflash">
		<div class="vertical <?php echo $params->get('moduleclass_sfx'); ?>">
			<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
			<div class="item <?php if ($i == $n - 1) echo 'last'; ?>">
				<?php modNewsFlashHelper::renderItem($list[$i], $params, $access); ?>
			</div>
			<?php endfor; ?>
		</div>
	</div>
<?php endif; ?>
