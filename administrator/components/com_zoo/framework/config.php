<?php
/**
* @package   ZOO Component
* @file      config.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$path = dirname(__FILE__);

// load imports
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');
jimport('joomla.application.component.model');
jimport('joomla.application.component.view');
jimport('joomla.application.component.controller');

// register framework
JLoader::register('YAlphaindex', $path.'/alphaindex.php');
JLoader::register('YArray', $path.'/array.php');
JLoader::register('YCache', $path.'/cache.php');
JLoader::register('YController', $path.'/controller.php');
JLoader::register('YDatabase', $path.'/database.php');
JLoader::register('YException', $path.'/exception.php');
JLoader::register('YFile', $path.'/file.php');
JLoader::register('YImageThumbnail', $path.'/imagethumbnail.php');
JLoader::register('YMenu', $path.'/menu.php');
JLoader::register('YMenuItem', $path.'/menu.php');
JLoader::register('YObject', $path.'/object.php');
JLoader::register('YPagination', $path.'/pagination.php');
JLoader::register('YParameter', $path.'/parameter/parameter.php');
JLoader::register('YParameterException', $path.'/parameter/exception.php');
JLoader::register('YParameterForm', $path.'/parameter/form.php');
JLoader::register('YParameterFormXml', $path.'/parameter/form/xml.php');
JLoader::register('YParameterFormDefault', $path.'/parameter/form/default.php');
JLoader::register('YParameterFormat', $path.'/parameter/format.php');
JLoader::register('YRenderer', $path.'/renderer.php');
JLoader::register('YRequest', $path.'/request.php');
JLoader::register('YSimpleXML', $path.'/simplexml.php');
JLoader::register('YSimpleXMLElement', $path.'/simplexml.php');
JLoader::register('YString', $path.'/string.php');
JLoader::register('YForm', $path.'/form.php');
JLoader::register('YTable', $path.'/table.php');
JLoader::register('YTree', $path.'/tree.php');
JLoader::register('YUtility', $path.'/utility.php');
JLoader::register('YValidator', $path.'/validator.php');
JLoader::register('YValidatorDate', $path.'/validator.php');
JLoader::register('YValidatorEmail', $path.'/validator.php');
JLoader::register('YValidatorException', $path.'/validator.php');
JLoader::register('YValidatorFile', $path.'/validator.php');
JLoader::register('YValidatorForeach', $path.'/validator.php');
JLoader::register('YValidatorInteger', $path.'/validator.php');
JLoader::register('YValidatorNumber', $path.'/validator.php');
JLoader::register('YValidatorPass', $path.'/validator.php');
JLoader::register('YValidatorRegex', $path.'/validator.php');
JLoader::register('YValidatorString', $path.'/validator.php');
JLoader::register('YValidatorUrl', $path.'/validator.php');
JLoader::register('YView', $path.'/view.php');
JLoader::register('YXML', $path.'/xml.php');
JLoader::register('YXMLElement', $path.'/xml.php');