<?php
/**
* @package   ZOO Component
* @file      update.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 *  UPGRADE PREVIOUS -> ZOO 2.2
 */

$file = $component->parent->getPath('extension_administrator').'/assets/js/Autocompleter.Request.js';
$new_file = $component->parent->getPath('extension_administrator').'/assets/js/autocompleter.request.js';
if (JFile::exists($file)) {
	JFile::move($file, $new_file);
}

/*
 *  UPGRADE PREVIOUS -> ZOO 2.0.3
 */

if (version_compare($version, '2.0.3', '<')) {
	// try to delete old menu.php file
	$file = $component->parent->getPath('extension_administrator').'/helpers/menu.php';
	if (JFile::exists($file)) {
		JFile::delete($file);
	}
}

/*
 *  UPGRADE PREVIOUS -> ZOO 2.1 BETA3
 */

// add application group field
$db = YDatabase::getInstance();
$fields = $db->getTableFields(ZOO_TABLE_APPLICATION);
if (isset($fields[ZOO_TABLE_APPLICATION]) && !array_key_exists('alias', $fields[ZOO_TABLE_APPLICATION])) {
	$db->query('ALTER TABLE '.ZOO_TABLE_APPLICATION.' ADD alias VARCHAR(255) AFTER name');
}

// sanatize alias fields of the application
$table = YTable::getInstance('application');
$apps = $table->all();
$apps = empty($apps) ? array() : $apps;
foreach ($apps as $app) {
	if (empty($app->alias)) {
		$app->alias = ApplicationHelper::getUniqueAlias($app->id, YString::sluggify($app->name));
		try {
			$table->save($app);
		} catch (ApplicationTableException $e) {}
	}
}

if (version_compare($version, '2.1.0 BETA3', '<')) {
	$table = YTable::getInstance('item');
	$items = $table->all();

	foreach ($items as $item) {
		$found = false;
		foreach ($item->getElements() as $element) {
			if ($element->getElementType() == 'download') {
				$file = $element->getElementData()->get('file');
				$directory = $element->getConfig()->get('directory');
				$directory = trim($directory, '\/').'/';
				$element->getElementData()->set('file', $directory.$file);
				$found = true;
			}
		}
		if ($found) {
			try {
				$table->save($item);
			} catch (ItemTableException $e) {}
		}
	}

	// set primary category for each item
	$table = YTable::getInstance('item');
	$items = $table->all();
	foreach($items as $item) {

		if ($item->getPrimaryCategoryId() != null) {
			continue;
		}

		$relatedCategoriesIds = $item->getRelatedCategoryIds();
		$relatedCategoriesIds = array_filter($relatedCategoriesIds, create_function('$id', 'return !empty($id);'));
		if (!empty($relatedCategoriesIds)) {
			// set params
			$item->params = $item
				->getParams()
				->set('config.primary_category', array_shift($relatedCategoriesIds))
				->toString();

			$item->alias = YString::sluggify($item->alias);

			if (empty($item->alias)) {
				ItemHelper::getUniqueAlias($item->id, YString::sluggify($item->name));
			}

			// save item
			try {
				$table->save($item);
			} catch (ItemTableException $e) {}
		}
	}
}