<?php
/**
* @package   yoo_pinboard Template
* @file      template.config.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// init vars
$config =& $this->warp->getHelper('config');

// set template css
$color = $config->get('color');
$css   = '.wrapper { width: '.intval($config->get('template_width'))."px; }\n";

$this->warp->stylesheets->addDeclaration($css);
$this->warp->stylesheets->add('css:template.css');

if ($config->get('direction') == 'rtl') { $this->warp->stylesheets->add('template:css/rtl.css'); }

$colorHeader = $config->get('colorHeader', '');
if ($colorHeader != '' && $colorHeader != 'default') {
	$this->warp->stylesheets->add("template:css/header/$colorHeader.css");
}

$colorMenubar = $config->get('colorMenubar', '');
if ($colorMenubar != '' && $colorMenubar != 'default') {
	$this->warp->stylesheets->add("template:css/menubar/$colorMenubar.css");
}

$colorBody = $config->get('colorBody', '');
if ($colorBody != '' && $colorBody != 'default') {
	$this->warp->stylesheets->add("template:css/body/$colorBody.css");
}

if ($color != '' && $color != 'default') {
    $color_url = "template:css/$color/";
    $this->warp->stylesheets->add($color_url.$color.'-layout.css');
}

$this->warp->stylesheets->add('template:css/custom.css');

// javascripts
$this->warp->javascripts->addDeclaration('Warp.Settings = '.json_encode(array('color' => $config->get('color'), 'itemColor' => $config->get('itemcolor'))).';');
$this->warp->javascripts->add('js:warp.js');
$this->warp->javascripts->add('js:accordionmenu.js');
$this->warp->javascripts->add('js:menu.js');
$this->warp->javascripts->add('js:fancymenu.js');
$this->warp->javascripts->add('js:template.js');

// ie7 hacks
if ($this->warp->useragent->browser() == 'msie' && $this->warp->useragent->version() == '7.0') {
	$css = '<link rel="stylesheet" href="%s" type="text/css" />';
	$ie7[] = sprintf($css, $this->warp->path->url('css:ie7hacks.css'));
	$head[] = '<!--[if IE 7]>'.implode("\n", $ie7).'<![endif]-->';
}

// ie6 hacks
if ($this->warp->useragent->browser() == 'msie' && $this->warp->useragent->version() == '6.0') {
	$css = '<link rel="stylesheet" href="%s" type="text/css" />';
	$js = '<script type="text/javascript" src="%s"></script>';
	$ie6[] = sprintf($css, $this->warp->path->url('css:ie6hacks.css'));
	$ie6[] = sprintf($js, $this->warp->path->url('js:ie6fix.js'));
	$ie6[] = sprintf($js, $this->warp->path->url('js:ie6png.js'));
	$ie6[] = sprintf($js, $this->warp->path->url('js:ie6.js'));
	$head[] = '<!--[if IE 6]>'.implode("\n", $ie6).'<![endif]-->';
}

// add $head
if (isset($head)) {
	$this->warp->template->set('head', implode("\n", $head));
}

// set css class for specific columns
$columns = null;
if ($this->warp->modules->count('left')) $columns .= 'column-left';
if ($this->warp->modules->count('right')) $columns .= ' column-right';
if ($this->warp->modules->count('contentleft')) $columns .= ' column-contentleft';
if ($this->warp->modules->count('contentright')) $columns .= ' column-contentright';

$config->set('columns', $columns);

// set css class for specific columns
if ($this->warp->modules->count('left')) {
	if ($config->get('layout') == 'left') {
		$config->set('leftcolumn', 'left');
	} else {
		$config->set('leftcolumn', 'right');
	}
}

// set css-class for rightbackground
if ($this->warp->modules->count('right')) {
	$config->set('rightcolumn', 'showright');
}

if (JRequest::getCmd('option') == 'com_content') {
	if (JRequest::getCmd('view') == 'frontpage' || (in_array(JRequest::getCmd('view'), array('section', 'category')) && JRequest::getCmd('layout') == 'blog')) {
		$config->set('blog', 'blog');
	}
}
