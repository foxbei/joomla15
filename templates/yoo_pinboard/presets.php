<?php
/**
* @package   yoo_pinboard Template
* @file      presets.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
 * Presets
 */

$default_preset = array(
	'colorHeader' => 'default',
	'colorMenubar' => 'default',
	'colorBody' => 'default',
);

$warp->config->addPreset('combo1', 'Combo 1', array_merge($default_preset,array(
	'colorBody' => 'fabricboard',
)));

$warp->config->addPreset('combo2', 'Combo 2',  array_merge($default_preset,array(
	'colorHeader' => 'cherry',
	'colorMenubar' => 'varnish',
	'colorBody' => 'pressboard',
)));

$warp->config->addPreset('combo3', 'Combo 3',  array_merge($default_preset,array(
	'colorHeader' => 'lemon',
	'colorMenubar' => 'varnish',
	'colorBody' => 'chalkboard',
)));

$warp->config->addPreset('combo4', 'Combo 4',  array_merge($default_preset,array(
	'colorHeader' => 'stripes2',
	'colorMenubar' => 'plastic',
)));

$warp->config->addPreset('combo5', 'Combo 5',  array_merge($default_preset,array(
	'colorHeader' => 'retro',
	'colorMenubar' => 'plastic',
	'colorBody' => 'steelboard',
)));

$warp->config->addPreset('combo6', 'Combo 6',  array_merge($default_preset,array(
	'colorHeader' => 'blue',
	'colorMenubar' => 'varnish',
	'colorBody' => 'fabricboard',
)));

$warp->config->addPreset('combo7', 'Combo 7',  array_merge($default_preset,array(
	'colorHeader' => 'fabricstripes',
	'colorMenubar' => 'plastic',
	'colorBody' => 'pressboard',
)));

$warp->config->addPreset('combo8', 'Combo 8',  array_merge($default_preset,array(
	'colorHeader' => 'concrete',
	'colorMenubar' => 'default',
	'colorBody' => 'chalkboard',
)));

$warp->config->addPreset('combo9', 'Combo 9',  array_merge($default_preset,array(
	'colorHeader' => 'green',
)));


$warp->config->applyPreset();