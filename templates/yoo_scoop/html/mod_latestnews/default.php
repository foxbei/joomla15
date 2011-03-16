<?php
/**
* @package   yoo_scoop Template
* @file      default.php
* @version   5.5.3 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php if (count($list) > 0) : ?>
<div class="module-latestnews">

	<ul class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
		<li class="item <?php if ($i % 2) { echo 'even'; } else { echo 'odd'; }; ?>">
			<a href="<?php echo $list[$i]->link; ?>"><?php echo $list[$i]->text; ?></a>
		</li>
	<?php endfor; ?>
	</ul>

</div>
<?php endif; ?>