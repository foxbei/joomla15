<?php 
/**
* @package   ZOO Carousel
* @file      mod_zoocarousel.css.php
* @version   2.3.1
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) @ob_start('ob_gzhandler');
header('Content-type: text/css; charset=UTF-8');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

define('DS', DIRECTORY_SEPARATOR);
define('PATH_ROOT', dirname(__FILE__) . DS);

/* ie browser */
$is_ie7 = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 7') !== false;	
$is_ie6 = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6') !== false;

/* general styling */
include(PATH_ROOT . 'tmpl/style.css');

/* default styling */
include(PATH_ROOT . 'tmpl/default/style.css');

include(PATH_ROOT . 'tmpl/default/black/style.css');

/* basic styling */
include(PATH_ROOT . 'tmpl/basic/style.css');

include(PATH_ROOT . 'tmpl/basic/black/style.css');

if ($is_ie6 && !$is_ie7) include(PATH_ROOT . 'tmpl/basic/ie6hacks.css');

/* button styling */
include(PATH_ROOT . 'tmpl/button/style.css');

/* plain styling */
include(PATH_ROOT . 'tmpl/plain/style.css');

include(PATH_ROOT . 'tmpl/plain/black/style.css');

/* list styling */
include(PATH_ROOT . 'tmpl/list/style.css');

include(PATH_ROOT . 'tmpl/list/black/style.css');

if ($is_ie6 && !$is_ie7) include(PATH_ROOT . 'tmpl/list/ie6hacks.css');

/* slideshow styling */
include(PATH_ROOT . 'tmpl/slideshow/style.css');

include(PATH_ROOT . 'tmpl/slideshow/black/style.css');

/* basic list styling */
include(PATH_ROOT . 'tmpl/basiclist/style.css');

include(PATH_ROOT . 'tmpl/basiclist/black/style.css');

if ($is_ie6 && !$is_ie7) include(PATH_ROOT . 'tmpl/basiclist/ie6hacks.css');

/* plain list styling */
include(PATH_ROOT . 'tmpl/plainlist/style.css');

include(PATH_ROOT . 'tmpl/plainlist/black/style.css');

?>