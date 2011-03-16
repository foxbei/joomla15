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

// load framework
require_once(dirname(__FILE__).'/framework/config.php');

// set defines
define('ZOO_VERSION', '2.0.0');
define('ZOO_COPYRIGHT', '<div class="copyright"><a target="_blank" href="http://zoo.yootheme.com">ZOO</a> is developed by <a target="_blank" href="http://www.yootheme.com">YOOtheme</a>. All Rights Reserved.</div>');
define('ZOO_ICON', 'zoo.png');
define('ZOO_TOOLBAR_TITLE', 'Zoo - ');
define('ZOO_CACHE_PATH', JPATH_ROOT.'/cache/com_zoo');
define('ZOO_SITE_PATH', JPATH_ROOT.'/components/com_zoo');
define('ZOO_SITE_URI', JURI::root().'components/com_zoo/');
define('ZOO_ADMIN_PATH', dirname(__FILE__));
define('ZOO_ADMIN_URI', JURI::root().'administrator/components/com_zoo/');
define('ZOO_APPLICATION_PATH', JPATH_ROOT.'/media/zoo/applications');
define('ZOO_APPLICATION_URI', JURI::root().'media/zoo/applications');
define('ZOO_TABLE_APPLICATION', '#__zoo_application');
define('ZOO_TABLE_CATEGORY', '#__zoo_category');
define('ZOO_TABLE_CATEGORY_ITEM', '#__zoo_category_item');
define('ZOO_TABLE_COMMENT', '#__zoo_comment');
define('ZOO_TABLE_ITEM', '#__zoo_item');
define('ZOO_TABLE_RATING', '#__zoo_rating');
define('ZOO_TABLE_SEARCH', '#__zoo_search_index');
define('ZOO_TABLE_SUBMISSION', '#__zoo_submission');
define('ZOO_TABLE_TAG', '#__zoo_tag');

// register classes
JLoader::register('Application', ZOO_ADMIN_PATH.'/classes/application.php');
JLoader::register('Category', ZOO_ADMIN_PATH.'/classes/category.php');
JLoader::register('Comment', ZOO_ADMIN_PATH.'/classes/comment.php');
JLoader::register('CommentAuthor', ZOO_ADMIN_PATH.'/classes/commentauthor.php');
JLoader::register('CommentAuthorJoomla', ZOO_ADMIN_PATH.'/classes/commentauthor.php');
JLoader::register('CommentAuthorFacebook', ZOO_ADMIN_PATH.'/classes/commentauthor.php');
JLoader::register('CommentAuthorTwitter', ZOO_ADMIN_PATH.'/classes/commentauthor.php');
JLoader::register('Item', ZOO_ADMIN_PATH.'/classes/item.php');
JLoader::register('ItemForm', ZOO_ADMIN_PATH.'/classes/itemform.php');
JLoader::register('ItemRenderer', ZOO_ADMIN_PATH.'/classes/itemrenderer.php');
JLoader::register('Submission', ZOO_ADMIN_PATH.'/classes/submission.php');
JLoader::register('Template', ZOO_ADMIN_PATH.'/classes/template.php');
JLoader::register('Type', ZOO_ADMIN_PATH.'/classes/type.php');
JLoader::register('Zoo', ZOO_ADMIN_PATH.'/classes/zoo.php');

// register tables
JLoader::register('ApplicationTable', ZOO_ADMIN_PATH.'/tables/application.php');
JLoader::register('CategoryTable', ZOO_ADMIN_PATH.'/tables/category.php');
JLoader::register('CommentTable', ZOO_ADMIN_PATH.'/tables/comment.php');
JLoader::register('ItemTable', ZOO_ADMIN_PATH.'/tables/item.php');
JLoader::register('SubmissionTable', ZOO_ADMIN_PATH.'/tables/submission.php');
JLoader::register('TagTable', ZOO_ADMIN_PATH.'/tables/tag.php');

// register helpers
JLoader::register('ApplicationHelper', ZOO_ADMIN_PATH.'/helpers/application.php');
JLoader::register('CategoryHelper', ZOO_ADMIN_PATH.'/helpers/category.php');
JLoader::register('CommentHelper', ZOO_ADMIN_PATH.'/helpers/comment.php');
JLoader::register('ElementHelper', ZOO_ADMIN_PATH.'/helpers/element.php');
JLoader::register('ExportHelper', ZOO_ADMIN_PATH.'/helpers/export.php');
JLoader::register('HttpHelper', ZOO_ADMIN_PATH.'/helpers/http.php');
JLoader::register('ImportHelper', ZOO_ADMIN_PATH.'/helpers/import.php');
JLoader::register('InstallHelper', ZOO_ADMIN_PATH.'/helpers/install.php');
JLoader::register('ItemHelper', ZOO_ADMIN_PATH.'/helpers/item.php');
JLoader::register('RouteHelper', ZOO_ADMIN_PATH.'/helpers/route.php');
JLoader::register('SubmissionHelper', ZOO_ADMIN_PATH.'/helpers/submission.php');
JLoader::register('TypeHelper', ZOO_ADMIN_PATH.'/helpers/type.php');
JLoader::register('UserHelper', ZOO_ADMIN_PATH.'/helpers/user.php');
JLoader::register('ZooHelper', ZOO_ADMIN_PATH.'/helpers/zoo.php');
JLoader::register('ZooModuleHelper', ZOO_ADMIN_PATH.'/helpers/zoomodule.php');

// register elements
JLoader::register('Element', ZOO_ADMIN_PATH.'/elements/element/element.php');

// add jhtml path
JHTML::addIncludePath(ZOO_ADMIN_PATH.'/helpers');

// load jQuery, if not loaded before
if (!JFactory::getApplication()->get('jquery')) {
	JFactory::getApplication()->set('jquery', true);
	JHTML::script('jquery.js', ZOO_ADMIN_URI.'libraries/jquery/');
}