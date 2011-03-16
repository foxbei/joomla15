<?php
/**
* @package   yoo_scoop Template
* @file      presets.php
* @version   5.5.3 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
 * Presets
 */

$default_preset = array();

$warp->config->addPreset('default', 'White', array_merge($default_preset,array(
	'color' => 'default'
)));

$warp->config->addPreset('color', 'Color',  array_merge($default_preset,array(
	'color' => 'color'
)));

$warp->config->addPreset('wood', 'Wood',  array_merge($default_preset,array(
	'color' => 'wood'
)));

$warp->config->addPreset('stripeslight', 'Stripes Light',  array_merge($default_preset,array(
	'color' => 'stripeslight'
)));

$warp->config->addPreset('stripesdark', 'Stripes Dark',  array_merge($default_preset,array(
	'color' => 'stripesdark'
)));

$warp->config->addPreset('dotted', 'Dotted', array_merge($default_preset,array(
	'color' => 'dotted'
)));


$warp->config->applyPreset();