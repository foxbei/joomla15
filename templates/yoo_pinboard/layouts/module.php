<?php
/**
* @package   yoo_pinboard Template
* @file      module.php
* @version   5.5.1 October 2010
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
foreach (array('suffix', 'style', 'badge', 'color', 'header', 'dropdownwidth') as $var) {
	$$var = isset($params[$var]) ? $params[$var] : null;
}

// create title
$pos = mb_strpos($title, ' ');
if ($pos !== false) {
	$title = mb_substr($title, 0, $pos).'<span class="color">'.mb_substr($title, $pos).'</span>';
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
	if ($module->position == 'header') $style = 'headerbar';
	if ($module->position == 'topblock') $style = 'note';
	if ($module->position == 'top') $style = 'note';
	if ($module->position == 'left') $style = 'postit';
	
	if ($module->position == 'right') {
		$style = 'dotted';
		if (JRequest::getCmd('option') == 'com_content') {
			if (JRequest::getCmd('view') == 'frontpage' || (in_array(JRequest::getCmd('view'), array('section', 'category')) && JRequest::getCmd('layout') == 'blog')) {
				$style = 'postit';
			}
		}
	}

	if ($module->position == 'maintop') $style = 'postit';
	if ($module->position == 'contenttop') $style = 'postit';
	if ($module->position == 'contentbottom') $style = 'postit';
	if ($module->position == 'mainbottom') $style = 'postit';
	if ($module->position == 'bottom') $style = 'postit';
	if ($module->position == 'bottomblock') $style = 'note';
}

// to test a module set the style, color, badge and icon here
//$style = '';
//$color = '';
//$badge = '';
//$icon = '';
//$header = '';

// force module style
if (in_array($module->position,array('absolute' ,'breadcrumbs','logo','banner','search','footer','debug'))) $style = 'raw';
if ($module->position == 'toolbar') { $style = 'blank'; }
if ($module->position == 'menu' || $module->position == 'topmenu') {
	$style = 'raw';
}

// set badge if exists
if ($badge) {
	$badge = '<div class="badge badge-'.$badge.'"></div>';
}

// set dropdownwidth if exists
if ($dropdownwidth) {
	$dropdownwidth = 'style="width: '.$dropdownwidth.'px;"';
}

$suffix = $style;
$extra_badge = '';

// set module template using the style
switch ($style) {

	case 'headerbar':
		$skeleton = '0-3n-0';
		$suffix   = 'mod-' . $suffix;
		$extra_badge = '<div class="badge-soldier"></div>	';
		break;

	case 'polaroid':
		$skeleton = '2n-4n-hb-3n';
		$suffix   = 'mod-' . $suffix . '2';
		$extra_badge = '<div class="badge-tape"></div>	';
		break;

	case 'postit':
		$skeleton = '2n-2n-3n';
		$suffix   = 'mod-' . $suffix . '2';
		break;

	case 'tile':
		$skeleton = '3n-3n-3n';
		$suffix   = 'mod-' . $suffix;
		break;
		
	case 'note':
		$skeleton = '2n-2n-3n';
		$suffix   = 'mod-' . $suffix;
		break;
		
	case 'print':
		$skeleton = '2n-3n-3n';
		$suffix   = 'mod-' . $suffix;
		break;
		
	case 'cardboard':
		$skeleton = '3n-3n-3n';
		$suffix   = 'mod-' . $suffix;
		break;

	case 'clip':
		$skeleton = '2n-2n-3n';
		$suffix   = 'mod-' . $suffix;
		$extra_badge = '<div class="badge-paperclip"></div>	';
		break;

	case 'dotted':
		$skeleton = '0-1-0';
		$suffix   = 'mod-' . $suffix;
		break;

	case 'menu':
		if ($module->position == 'right') {
			$suffix = "mod-dotted";
			$skeleton = '0-1-0';
		} else {
			$suffix = "mod-tile";
			$skeleton = '3n-3n-3n';
		}
		$suffix   = $suffix . ' mod-menu';
		break;

	case 'raw':
		$skeleton  = 'raw';
		break;

	case 'blank':
	default:
		$skeleton = 'default';
		$suffix   = 'mod-' . $style;
}

$style= $suffix;

// render menu template
if ($params['menu']) {
    if ($params['menu']=='accordion') {
		$content = $this->warp->menu->process($module,array('pre','default','accordion','post'));
	} else {
		$content = $this->warp->menu->process($module,array('pre','default','post'));
	}
}

// render module template
echo $this->render("modules/{$skeleton}", compact('style', 'color', 'first', 'last', 'badge', 'showtitle', 'title', 'content', 'dropdownwidth', 'suffix', 'extra_badge'));