<?php
/**
* @package   ZOO Component
* @file      install.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// try to set time limit
@set_time_limit(0);

// try to increase memory limit
if((int) ini_get('memory_limit') < 32) {
    @ini_set('memory_limit', '32M');
}

$GLOBALS['ZOO_COMPONENT_INSTALLER'] = $this;

function com_install() {

	$installer = $GLOBALS['ZOO_COMPONENT_INSTALLER'];
	
	// check requirements
	require_once($installer->parent->getPath('source').'/admin/installation/requirements.php');
	
	$requirements = new YRequirements();

	$fulfilled = $requirements->checkRequirements();
	
	$requirements->displayResults();
	
	if (!$fulfilled) {
		$installer->parent->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Minimum requirements not fulfilled.'));
		return false;
	}
	
	// requirements fulfilled, install the ZOO
	require_once($installer->parent->getPath('source').'/admin/installation/zooinstall.php');
	
	return ZooInstall::doInstall($installer);

}