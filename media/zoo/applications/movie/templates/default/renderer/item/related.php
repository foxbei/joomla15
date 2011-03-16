<?php
/**
* @package   ZOO Component
* @file      related.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($this->checkPosition('title')) : ?>
	<?php echo $this->renderPosition('title', array('style' => 'hyphen')); ?>
<?php endif; ?>