<?php
/**
* @package   Warp Theme Framework
* @file      vert.php
* @version   5.5.8
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright  2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if (count($list) > 0) : ?>
	<ul class="newsflash line">
		<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
		<li class="item <?php if ($i == $n - 1) echo 'last'; ?>">
			<?php modNewsFlashHelper::renderItem($list[$i], $params, $access); ?>
		</li>
		<?php endfor; ?>
	</ul>
<?php endif; ?>
