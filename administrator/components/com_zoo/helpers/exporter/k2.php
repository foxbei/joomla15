<?php
/**
* @package   ZOO Component
* @file      k2.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class ExportHelperK2 extends ExportHelper{

	public function __construct() {
		parent::__construct();
		$this->_name = 'K2';
	}

	public function isEnabled() {
		$path_to_xml = JPATH_ADMINISTRATOR . '/components/com_k2/manifest.xml';
		if (JFile::exists($path_to_xml) && ($data = JApplicationHelper::parseXMLInstallFile($path_to_xml))) {
            return (version_compare($data['version'], '2.1') >= 0);
		}

		return false;
	}

	public function export() {

		$db = YDatabase::getInstance();

		// get k2 categories
	    $query = "SELECT a.*, b.name AS extra_field_group_name "
	    		." FROM #__k2_categories AS a"
	       		." LEFT JOIN #__k2_extra_fields_groups AS b ON b.id = a.extraFieldsGroup";
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
			while (in_array($alias, $aliases)) {
				$alias = $category->alias . '-' . $i++;
			}
			$category->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $category->alias;
		}

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
				$this->_attachCategoryImage($category_xml, '/media/k2/categories/'.$category->image, 'Image');
			}
			$this->_addCategory($category_xml);
		}

		// get k2 items
	    $query = "SELECT * FROM #__k2_items";
	    $items = $db->queryObjectList($query, 'id');

	    // get k2 extra fields
	    $query 		  = "SELECT * FROM #__k2_extra_fields";
	    $extra_fields = $db->queryObjectList($query, 'id');

	    // get k2 tags
	    $query = "SELECT a.itemID, b.name"
	    		." FROM #__k2_tags_xref as a"
	    		." JOIN #__k2_tags AS b ON a.tagID = b.id";
	    $tag_result = $db->queryObjectList($query);
	    $tags = array();
	    foreach ($tag_result as $tag) {
	    	$tags[$tag->itemID][] = $tag->name;
	    }

		// sanatize item aliases
		$aliases = array();
		foreach ($items as $item) {
			$i = 2;
			$alias = YString::sluggify($item->alias);
			while (in_array($alias, $aliases)) {
				$alias = $item->alias . '-' . $i++;
			}
			$item->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $item->alias;

		}

		// export items
		foreach ($items as $item) {
            if (!$item->trash) {
                if (!$type = $categories[$item->catid]->extra_field_group_name) {
                    $type = JText::_('K2-Unassigned');
                }

                $this->_addItem($type, $this->_itemToXML($item, $categories, $tags, $extra_fields));
            }
		}

		return parent::export();

	}

	protected function _itemToXML($item, $categories = array(), $tags = array(), $extra_fields) {

		$attributes = array();
		foreach (self::$item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$attributes[$attribute] = $item->$attribute;
			}
		}
		// add author
		$attributes['author'] = JFactory::getUser($item->created_by)->username;

		// add state
		$attributes['state'] = $item->published;

		// build item xml
		$item_xml = $this->_buildItem($item->alias, $item->title, $attributes);

		// add category
		$this->_addItemCategory($item_xml, $categories[$item->catid]->alias);

		// add tags
		if (isset($tags[$item->id])) {
			foreach ($tags[$item->id] as $tag) {
				$this->_addItemTag($item_xml, $tag);
			}
		}

		// add item content
		$i = 0;
		$this->_addItemData($item_xml, $this->_buildElement('textarea', $i,   'content', array('value' => $item->introtext)));
		$this->_addItemData($item_xml, $this->_buildElement('textarea', $i++, 'content', array('value' => $item->fulltext)));

		$this->_addItemData($item_xml, $this->_buildElement('image', $i++, 'image', array('file' => 'media/k2/items/src/'.md5("Image".$item->id).'.jpg')));

		// add extra fields
        if (isset($item->extra_fields)) {
            foreach (json_decode($item->extra_fields) as $element) {

                $extrafield = $extra_fields[$element->id];

                switch ($extrafield->type) {
                    case 'textfield':
                        $this->_addItemData($item_xml, $this->_buildElement('text', $i++, $extrafield->name, array('value' => $element->value)));
                        break;
                    case 'textarea':
                        $this->_addItemData($item_xml, $this->_buildElement('textarea', $i++, $extrafield->name, array('value' => $element->value)));
                        break;
                    case 'select':
                    case 'multipleSelect':
                        $this->_addItemData($item_xml, $this->_buildElement('select', $i++, $extrafield->name, array('option' => $element->value)));
                        break;
                    case 'radio':
                        $this->_addItemData($item_xml, $this->_buildElement('radio', $i++, $extrafield->name, array('value' => $element->value)));
                        break;
                    case 'link':
                        $this->_addItemData($item_xml, $this->_buildElement('link', $i++, $extrafield->name, array('text' => $element->value[0], 'value' => $element->value[1])));
                        break;
                }

            }
        }

		return $item_xml;
	}

}