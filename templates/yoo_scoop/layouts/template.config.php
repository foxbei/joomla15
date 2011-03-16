<?php
/**
* @package   yoo_scoop Template
* @file      template.config.php
* @version   5.5.3 October 2010
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
$this->warp->stylesheets->add('css:reset.css');
$this->warp->stylesheets->add('css:layout.css');
$this->warp->stylesheets->add('css:typography.css');
$this->warp->stylesheets->add('css:menus.css');
$this->warp->stylesheets->add('css:modules.css');
$this->warp->stylesheets->add('css:system.css');
$this->warp->stylesheets->add('css:extensions.css');

if ($config->get('direction') == 'rtl') { $this->warp->stylesheets->add('template:css/rtl.css'); }

if ($color != '' && $color != 'default') {
    $color_url = "template:css/$color/";
    $this->warp->stylesheets->add($color_url.$color.'-layout.css');
}

$this->warp->stylesheets->add('template:css/custom.css');

$itemColors = array(
	'item1' => $config->get('item1Color', ''),
	'item2' => $config->get('item2Color', ''),
	'item3' => $config->get('item3Color', ''),
	'item4' => $config->get('item4Color', ''),
	'item5' => $config->get('item5Color', ''),
	'item6' => $config->get('item6Color', ''),
	'item7' => $config->get('item7Color', ''),
	'item8' => $config->get('item8Color', ''),
	'item9' => $config->get('item9Color', ''),
	'item10'=> $config->get('item10Color', '')
);

// javascripts
$this->warp->javascripts->addDeclaration('Warp.Settings = '.json_encode(array('color' => $config->get('color'), 'itemColor' => $config->get('itemcolor'), 'itemColors' => $itemColors)).';');
$this->warp->javascripts->add('js:warp.js');
$this->warp->javascripts->add('js:accordionmenu.js');
$this->warp->javascripts->add('js:menu.js');
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