<?php
/**
* @package   yoo_expo Template
* @file      module.php
* @version   5.5.2 December 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// init vars
$id        = $module->id;
$position  = $module->position;
$title     = $module->title;
$showtitle = $module->showtitle;
$content   = $module->content;

// init params
$first = $params['first'] ? 'first' : null;
$last  = $params['last'] ? 'last' : null;
foreach (array('suffix', 'style', 'badge', 'color', 'icon', 'yootools', 'header', 'dropdownwidth') as $var) {
	$$var = isset($params[$var]) ? $params[$var] : null;
}

// create title
$pos = mb_strpos($title, ' ');
if ($pos !== false) {
	$title = '<span class="color">'.mb_substr($title, 0, $pos).'</span>'.mb_substr($title, $pos);
}

// create subtitle
$pos = mb_strpos($title, '||');
if ($pos !== false) {
	$title = '<span class="title">'.mb_substr($title, 0, $pos).'</span><span class="subtitle">'.mb_substr($title, $pos + 2).'</span>';
}

// legacy compatibility
if ($suffix == 'blank' || $suffix == '-blank') $style = 'blank';
if ($suffix == 'menu' || $suffix == '_menu') $style = 'menu';

// set default module types
if ($style == '') {
	if ($module->position == 'headerleft') $style = 'separator';
	if ($module->position == 'headerright') $style = 'separator';
	if ($module->position == 'topblock') $style = 'box';
	if ($module->position == 'top') $style = 'box';
	if ($module->position == 'left') $style = 'line';
	if ($module->position == 'right') $style = 'line';
	if ($module->position == 'maintop') $style = 'box';
	if ($module->position == 'contenttop') $style = 'box';
	if ($module->position == 'contentleft') $style = 'line';
	if ($module->position == 'contentright') $style = 'line';
	if ($module->position == 'contentbottom') $style = 'box';
	if ($module->position == 'mainbottom') $style = 'box';
	if ($module->position == 'bottom') $style = 'box';
	if ($module->position == 'bottomblock') $style = 'box';
}

// to test a module set the style, color, badge and icon here
//$style = '';
//$color = '';
//$badge = '';
//$icon = '';
//$header = '';

// force module style
if (in_array($module->position,array('absolute' ,'breadcrumbs','logo','banner','search','footer','debug'))) $style = 'raw';
if ($module->position == 'toolbarleft' || $module->position == 'toolbarright')  $style = 'blank';
if (($module->position == 'headerleft' || $module->position == 'headerright') && $style != 'separator') $style = 'blank';
if ($module->position == 'menu' || $module->position == 'menuright') {
	$style = ($style == 'menu') ? 'raw' : 'dropdown';
}

// set badge if exists
if ($badge) {
	$badge = '<div class="badge badge-'.$badge.'"></div>';
}

// set icon if exists
if ($icon) {
	$title = '<span class="icon icon-'.$icon.'"></span>'.$title.'';
}

// set yootools color if exists
if ($yootools == 'black') {
	$yootools = 'yootools-black';
}

// set dropdownwidth if exists
if ($dropdownwidth) {
	$dropdownwidth = 'style="width: '.$dropdownwidth.'px;"';
}

// set module template using the style
switch ($style) {

	case 'box':
		$template  = '3-3-3';
		$style     = 'mod-'.$style;
		break;

	case 'headline':
		$template  = '0-1-0_headline';
		$style     = 'mod-'.$style;
		break;

	case 'line':
		$template  = '0-1-0';
		$style     = 'mod-'.$style;
		$color     = ($color) ? $style.'-'.$color : '';
		break;
		
	case 'gradient':
		$template  = '0-1-0';
		$style     = 'mod-'.$style;
		$color     = ($color) ? $style.'-'.$color : '';
		break;
		
	case 'header':
		$template  = '0-1-0_h';
		$style     = 'mod-'.$style;
		break;
		
	case 'frame':
		$template  = '0-3-0';
		$style     = 'mod-'.$style;
		break;
		
	case 'separator':
		$template  = '0-1-0';
		$style     = 'mod-'.$style;
		break;

	case 'menu':
		$template  = '3-3-3';
		$style     = 'mod-box mod-menu mod-menu-box';
		break;
		
	case 'polaroid':
		$template  = '0-3-3_polaroid';
		$style     = 'mod-'.$style;
		$badge	  .= '<div class="badge-tape"></div>';
		break;
		
	case 'postit':
		$template  = '0-2-3';
		$style     = 'mod-'.$style;
		break;
		
	case 'dropdown':
		$template  = 'dropdown';
		$style     = 'mod-'.$style;
		break;

	case 'blank':
		$template  = 'default';
		$style     = 'mod-'.$style;
		break;

	case 'raw':
		$template  = 'raw';
		break;

	default:
		$template  = 'default';
		$style     = $suffix;
}

// render menu template
if ($params['menu']) {
	$content = $this->warp->menu->process($module,array('pre','default',$params['menu'],'post'));
}

// render module template
echo $this->render("modules/{$template}", compact('style', 'color', 'yootools', 'first', 'last', 'badge', 'showtitle', 'title', 'content', 'dropdownwidth'));