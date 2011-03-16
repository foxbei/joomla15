<?php
/**
* @package   ZOO Component
* @file      docman.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class ExportHelperDocman extends ExportHelper{

	public function __construct() {
		parent::__construct();
		$this->_name = 'Docman';
	}

	public function isEnabled() {
		$path_to_xml = JPATH_ADMINISTRATOR . '/components/com_docman/manifest.xml';
		if (JFile::exists($path_to_xml) && ($data = JApplicationHelper::parseXMLInstallFile($path_to_xml))) {
            return (version_compare($data['version'], '1.5.8') >= 0);
		}

		return false;
	}

	public function export() {

		$db = YDatabase::getInstance();

		// get docman categories
	    $query = "SELECT  c.*, c.parent_id as parent"
			 . " FROM #__categories AS c"
			 . " WHERE c.section = 'com_docman'"
			 . " AND c.published != -2"
			 . " ORDER BY c.parent_id, c.ordering" ;
	    $categories = $db->queryObjectList($query, 'id');

    	// get category table
		$category_table = YTable::getInstance('category');

		// get item table
		$item_table = YTable::getInstance('item');

		// sanatize category aliases
		$aliases = array();
		foreach ($categories as $category) {

			$i = 2;
			$alias = YString::sluggify($category->alias);
			if (empty($alias)) {
				$alias = YString::sluggify($category->title);
			}
			while (in_array($alias, $aliases)) {
				$alias = $category->alias . '-' . $i++;
			}
			$category->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $category->alias;
		}

		// get image path
		$this->image_path = JComponentHelper::getParams('com_media')->get('image_path');
		$this->image_path = trim($this->image_path, '\/') . '/';
		
		// export categories
		foreach ($categories as $category) {

			// assign attributes
			$attributes = array();
			foreach (self::$category_attributes as $attribute) {
				if (isset($category->$attribute)) {
					$attributes[$attribute] = $category->$attribute;
				}
			}

			// sanatize parent
			if ($category->parent && isset($categories[$category->parent])) {
				$attributes['parent'] = $categories[$category->parent]->alias;
			}

			// add category
			$category_xml = $this->_buildCategory($category->alias, $category->name, $attributes);
			if ($category->image) {
				$this->_attachCategoryImage($category_xml, $this->image_path.$category->image, 'Image');
			}
			$this->_addCategory($category_xml);
		}

		// get docman items
	    $query = "SELECT * FROM #__docman";
	    $items = $db->queryObjectList($query, 'id');

		// sanatize item aliases
		$aliases = array();
		foreach ($items as $item) {
			$i = 2;
			$alias = YString::sluggify($item->dmname);
			while (in_array($alias, $aliases)) {
				$alias = YString::sluggify($item->dmname) . '-' . $i++;
			}
			$item->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $item->alias;

		}

		require_once(JPATH_ADMINISTRATOR.'/components/com_docman/docman.config.php');
		$config = new dmConfig();
		$document_path = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $config->dmpath)), '/') . '/';
		
		// export items
		foreach ($items as $item) {

			if (preg_match('/^Link:/', $item->dmfilename)) {
				$type = 'Linked File';
				$item->dmfilename = preg_replace('/^Link:/', '', $item->dmfilename);
			} else {
				$type = 'File';
				$item->dmfilename = $document_path . $item->dmfilename;
			}

			$this->_addItem($type, $this->_itemToXML($item, $categories, $type));
		}

		return parent::export();

	}

	protected function _itemToXML($item, $categories = array(), $type = '') {

		$attributes = array();
		foreach (self::$item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$attributes[$attribute] = $item->$attribute;
			}
		}
		// add author
		$attributes['author'] = JFactory::getUser($item->dmsubmitedby)->username;

		// add state
		$attributes['state'] = $item->published;

		// add created
		$attributes['created'] = $item->dmdate_published;

		// add modified
		$attributes['modified'] = $item->dmlastupdateon;

		// add hits
		$attributes['hits'] = $item->dmcounter;

		// build item xml
		$item_xml = $this->_buildItem($item->alias, $item->dmname, $attributes);

		// add category
		$this->_addItemCategory($item_xml, $categories[$item->catid]->alias);

		// add item content
		$i = 0;
		$this->_addItemData($item_xml, $this->_buildElement('textarea', $i, 'Description', array('value' => $item->dmdescription)));
		$this->_addItemData($item_xml, $this->_buildElement('image', $i++, 'Image', array('file' => $this->image_path . $item->dmthumbnail)));
		$this->_addItemData($item_xml, $this->_buildElement('link', $i++, 'Homepage', array('text' => 'Homepage', 'value' => $item->dmurl)));

		switch ($type) {
			case 'File':
				$this->_addItemData($item_xml, $this->_buildElement('download', $i++, 'File', array('file' => $item->dmfilename)));
				break;
			case 'Linked File':
				$this->_addItemData($item_xml, $this->_buildElement('link', $i++, 'Linked File', array('text' => $item->dmfilename, 'value' => $item->dmfilename)));
				break;
		}

		return $item_xml;
	}

}