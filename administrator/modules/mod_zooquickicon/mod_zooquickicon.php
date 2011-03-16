<?php
/**
* @package   ZOO Component
* @file      mod_zooquickicon.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
jimport('joomla.filesystem.file');
if (!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php')) {
	return;
}

require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

$applications = YTable::getInstance('application')->all(array('order' => 'name'));

if (empty($applications)) {
	return;
}

$float = JFactory::getLanguage()->isRTL() ? 'right' : 'left';

?>

<div id="cpanel">
	<?php foreach ($applications as $application) : ?>
	<div style="float:<?php echo $float; ?>;">
		<div class="icon">
			<a href="index.php?option=com_zoo&changeapp=<?php echo $application->id; ?>">
				<img style="width:48px; height:48px;" alt="<?php echo $application->name; ?>" src="<?php echo $application->getIcon(); ?>" />
				<span><?php echo $application->name; ?></span>
			</a>
		</div>
	</div>
	<?php endforeach; ?>
</div>