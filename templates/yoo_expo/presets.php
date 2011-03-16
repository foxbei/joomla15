<?php
/**
* @package   yoo_expo Template
* @file      presets.php
* @version   5.5.2 December 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
 * Presets
 */

$default_preset = array();

$warp->config->addPreset('preset01', 'Wooden Board', array_merge($default_preset,array(
	'style' => 'default',
	'wrapper' => 'wood',
	'background' => 'default',
	'font' => 'default'
)));

$warp->config->addPreset('preset02', 'Plain Brown', array_merge($default_preset,array(
	'style' => 'brown',
	'wrapper' => 'default',
	'background' => 'default',
	'font' => 'default'
)));

$warp->config->addPreset('preset03', 'Blue Glow', array_merge($default_preset,array(
	'style' => 'default',
	'wrapper' => 'glass',
	'background' => 'lights',
	'font' => 'default'
)));

$warp->config->addPreset('preset04', 'Red Glow', array_merge($default_preset,array(
	'style' => 'red',
	'wrapper' => 'black',
	'background' => 'lights',
	'font' => 'default'
)));

$warp->config->addPreset('preset05', 'Green Glow', array_merge($default_preset,array(
	'style' => 'green',
	'wrapper' => 'default',
	'background' => 'lights',
	'font' => 'default'
)));

$warp->config->addPreset('preset06', 'Blue Stripes', array_merge($default_preset,array(
	'style' => 'default',
	'wrapper' => 'glass',
	'background' => 'stripes',
	'font' => 'default'
)));

$warp->config->addPreset('preset07', 'Fire Plasma', array_merge($default_preset,array(
	'style' => 'red',
	'wrapper' => 'black',
	'background' => 'plasma',
	'font' => 'default'
)));

$warp->config->addPreset('preset08', 'Toxic Plasma', array_merge($default_preset,array(
	'style' => 'green',
	'wrapper' => 'glass',
	'background' => 'plasma',
	'font' => 'default'
)));

$warp->config->addPreset('preset09', 'Illustration', array_merge($default_preset,array(
	'style' => 'default',
	'wrapper' => 'default',
	'background' => 'landscape',
	'font' => 'default'
)));

$warp->config->addPreset('preset10', 'Ocean', array_merge($default_preset,array(
	'style' => 'default',
	'wrapper' => 'glass',
	'background' => 'ocean',
	'font' => 'default'
)));

$warp->config->applyPreset();