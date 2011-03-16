<?php
/**
* @package   ZOO Component
* @file      mtree.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class ExportHelperMtree extends ExportHelper{

	public function __construct() {
		parent::__construct();
		$this->_name = 'Mosets Tree';
	}

	public function isEnabled() {
		$path_to_xml = JPATH_ADMINISTRATOR . '/components/com_mtree/mtree.xml';
		if (JFile::exists($path_to_xml) && ($data = JApplicationHelper::parseXMLInstallFile($path_to_xml))) {
            return (version_compare($data['version'], '2.1.3') >= 0);
		}

		return false;
	}

	public function export() {

		$db = YDatabase::getInstance();
		$user = JFactory::getUser();

		// get mtree categories
	    $query = "SELECT *"
			 .", cat_parent as parent"
			 .", cat_created as created"
			 .", cat_published as published"
			 .", cat_name as name"
			 .", cat_desc as description"
			 .", cat_image as image"
			 ." FROM #__mt_cats"
			 ." WHERE cat_id != 0"
			 ." ORDER BY cat_parent, ordering" ;
	    $categories = $db->queryObjectList($query, 'cat_id');

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
				$alias = YString::sluggify($category->name);
			}
			$category->alias = $alias;
			while (in_array($alias, $aliases)) {
				$alias = $category->alias . '-' . $i++;
			}
			$category->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $category->alias;
		}

		// get image and file path
		$this->image_path = JComponentHelper::getParams('com_media')->get('image_path');
		$this->image_path = trim($this->image_path, '\/') . '/';
		$this->file_path = JComponentHelper::getParams('com_media')->get('file_path');
		$this->file_path = trim($this->file_path, '\/') . '/';
		
		require_once(JPATH_ADMINISTRATOR . '/components/com_mtree/config.mtree.class.php');
		$mtconf = new mtConfig($db);
		$this->category_image_path = $mtconf->get('relative_path_to_cat_original_image');
		$this->category_image_path = trim($this->category_image_path, '\/') . '/';
		$this->listing_image_path = $mtconf->get('relative_path_to_listing_original_image');
		$this->listing_image_path = trim($this->listing_image_path, '\/') . '/';
		$this->attachement_path = $mtconf->get('relative_path_to_attachments');
		$this->attachement_path = trim($this->attachement_path, '\/') . '/';
		$this->import_path_category_images = JPATH_ROOT .'/'. $this->image_path .'zoo/mtree_import/cats/';
		$this->import_path_item_images = JPATH_ROOT .'/'. $this->image_path .'zoo/mtree_import/items/';
		$this->import_path_attachments = JPATH_ROOT .'/'. $this->image_path .'zoo/mtree_import/attachments/';
		$this->import_path_gallery = JPATH_ROOT .'/'. $this->image_path .'zoo/mtree_import/gallery/';

		// create import folders
		if (!JFolder::exists($this->import_path_category_images)) {
			JFolder::create($this->import_path_category_images);
		}

		if (!JFolder::exists($this->import_path_item_images)) {
			JFolder::create($this->import_path_item_images);
		}

		if (!JFolder::exists($this->import_path_attachments)) {
			JFolder::create($this->import_path_attachments);
		}

		if (!JFolder::exists($this->import_path_gallery)) {
			JFolder::create($this->import_path_gallery);
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
				$old_file_name = JPATH_ROOT .'/'. $this->category_image_path.$category->image;

				$file_info = pathinfo($category->image);
				$file_name = $this->import_path_category_images. $category->image;
				$i = 2;
				while (JFile::exists($file_name)) {
					$file_name = $this->import_path_category_images. $file_info['filename'] . '-' . $i++ . '.' . $file_info['extension'];
				}

				if (JFile::copy($old_file_name, $file_name)) {
					$image = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file_name)), '/');
					$this->_attachCategoryImage($category_xml, $image, 'Image');
				}
			}
			$this->_addCategory($category_xml);
		}

		// get mtree items
	    $query = "SELECT *, link_id as id"
			 .", link_name as name"
			 .", link_desc as description"
			 .", user_id as created_by"
			 .", link_hits as hits"
			 .", link_published as published"
			 .", link_created as created"
			 .", link_modified as modified"
			 ." FROM #__mt_links";
	    $items = $db->queryObjectList($query, 'link_id');

		// sanatize item aliases
		$aliases = array();
		foreach ($items as $item) {
			$i = 2;
			$alias = YString::sluggify($item->alias);
			while (in_array($alias, $aliases)) {
				$alias = YString::sluggify($item->alias) . '-' . $i++;
			}
			$item->alias = $alias;

			// remember used aliases to ensure unique aliases
			$aliases[] = $item->alias;

		}

		// export items
		foreach ($items as $item) {
			$this->_addItem('mtree', $this->_itemToXML($item, $categories));
		}

		return parent::export();

	}

	protected function _itemToXML($item, $categories = array()) {

		$attributes = array();
		foreach (self::$item_attributes as $attribute) {
			if (isset($item->$attribute)) {
				$attributes[$attribute] = $item->$attribute;
			}
		}

		// add attributes
		$attributes['state'] = $item->published;
		$attributes['author'] = JFactory::getUser($item->user_id)->username;

		// build item xml
		$item_xml = $this->_buildItem($item->alias, $item->name, $attributes);

		// add category
		$db = YDatabase::getInstance();

		// get mtree categories
	    $query = "SELECT cat_id"
			 ." FROM #__mt_cl"
			 ." WHERE link_id = " . (int) $item->id;
	    $related_categories = $db->queryResultArray($query);

		if (!empty($related_categories)) {
			foreach ($related_categories as $category_id) {
				if (empty($category_id)) {
					$alias = '_root';
				} else {
					$alias = $categories[$category_id]->alias;
				}
				$this->_addItemCategory($item_xml, $alias);
			}
		}

		// get mtree content
	    $query = "SELECT *"
			 ." FROM #__mt_customfields as cf"
			 ." LEFT JOIN #__mt_cfvalues as v on cf.cf_id = v.cf_id AND v.link_id = " . (int) $item->id
			 ." LEFT JOIN #__mt_cfvalues_att as va on va.cf_id = v.cf_id AND va.link_id = v.link_id";
	    $custom_fields = $db->queryObjectList($query);

		// add item content
		$i = 0;

		// Load images
		$query = 'SELECT img_id, filename FROM #__mt_images WHERE link_id = ' . $db->quote($item->id) . ' ORDER BY ordering ASC';
		$images = $db->queryObjectList($query);

		$folder = preg_replace('/[^a-z0-9]*/i', '', $item->alias);
		$path = $this->import_path_gallery . $folder . '/';
		if (!JFolder::exists($path)) {
			JFolder::create($path);
		}

		if (!empty($images)) {			
			foreach($images as $image) {
				$old_file_name = JPATH_ROOT .'/'. $this->listing_image_path . $image->filename;

				$file_info = pathinfo($image->filename);

				$file_name = $path . $image->filename;
				$j = 2;
				while (JFile::exists($file_name)) {
					$file_name = $path. $file_info['filename'] . '-' . $j++ . '.' . $file_info['extension'];
				}

				$file = '';
				if (!empty($image->filename)) {
					JFile::copy($old_file_name, $file_name);
				}
			}
		}
		$path = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT.'/'.$this->file_path, '/').'/i', '', $path)), '/');
		$this->_addItemData($item_xml, $this->_buildElement('gallery', $i++, 'Gallery', array('value' => $path)));
		
		if (!empty($custom_fields)) {			
			foreach ($custom_fields as $field) {
				switch ($field->field_type) {
					case 'mtags':
						$tags = explode(',', $field->value);
						// add tags
						foreach ($tags as $tag) {
							$this->_addItemTag($item_xml, trim($tag));
						}

						break;
					case 'weblinknewwin':
						$this->_addItemData($item_xml, $this->_buildElement('link', $i++, $field->caption, array('value' => $field->value)));
						break;
					case 'image':
						$old_file_name = JPATH_ROOT .'/'. $this->attachement_path . $field->raw_filename;

						$file_info = pathinfo($field->filename);

						$file_name = $this->import_path_item_images. $field->filename;
						$j = 2;
						while (JFile::exists($file_name)) {
							$file_name = $this->import_path_item_images. $file_info['filename'] . '-' . $j++ . '.' . $file_info['extension'];
						}					

						$file = '';
						if (!empty($field->filename)) {
							if (JFile::copy($old_file_name, $file_name)) {
								$file = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file_name)), '/');
							}
						}

						$this->_addItemData($item_xml, $this->_buildElement('image', $i++, $field->caption, array('file' => $file)));
						break;
					case 'mfile':
						$old_file_name = JPATH_ROOT .'/'. $this->attachement_path . $field->raw_filename;

						$file_info = pathinfo($field->filename);

						$file_name = $this->import_path_attachments. $field->filename;
						$j = 2;
						while (JFile::exists($file_name)) {
							$file_name = $this->import_path_attachments. $file_info['filename'] . '-' . $j++ . '.' . $file_info['extension'];
						}

						$file = '';
						if (!empty($field->filename)) {
							if (JFile::copy($old_file_name, $file_name)) {
								$file = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file_name)), '/');
							}
						}

						$this->_addItemData($item_xml, $this->_buildElement('download', $i++, $field->caption, array('file' => $file)));
						break;
					case 'videoplayer':
					case 'audioplayer':
						$old_file_name = JPATH_ROOT .'/'. $this->attachement_path . $field->raw_filename;

						$file_info = pathinfo($field->filename);

						$file_name = $this->import_path_attachments. $field->filename;
						$j = 2;
						while (JFile::exists($file_name)) {
							$file_name = $this->import_path_attachments. $file_info['filename'] . '-' . $j++ . '.' . $file_info['extension'];
						}

						$file = '';
						if (!empty($field->filename)) {
							if (JFile::copy($old_file_name, $file_name)) {
								$file = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file_name)), '/');
							}
						}

						$this->_addItemData($item_xml, $this->_buildElement('video', $i++, $field->caption, array('file' => $file)));
						break;
					case 'memail':
						$this->_addItemData($item_xml, $this->_buildElement('email', $i++, $field->caption, array('value' => $field->value)));
						break;
					case 'mnumber':
					case 'year':
						$this->_addItemData($item_xml, $this->_buildElement('text', $i++, $field->caption, array('value' => $field->value)));
						break;
					case 'mdate':
						$this->_addItemData($item_xml, $this->_buildElement('date', $i++, $field->caption, array('value' => $field->value)));
						break;
					case 'onlinevideo':
						$this->_addItemData($item_xml, $this->_buildElement('video', $i++, $field->caption, array('url' => $field->value)));
						break;
					case 'coredesc':
						$this->_addItemData($item_xml, $this->_buildElement('textarea', $i++, $field->caption, array('value' => $item->link_desc)));
						break;
					case 'coreaddress':
					case 'corecity':
					case 'corestate':
					case 'corecountry':
					case 'corepostcode':
					case 'coretelephone':
					case 'corefax':
					case 'coreprice':
						$attribute = substr($field->field_type, 4);
						$this->_addItemData($item_xml, $this->_buildElement('text', $i++, $field->caption, array('value' => $item->$attribute)));
						break;
					case 'coreemail':
						$this->_addItemData($item_xml, $this->_buildElement('email', $i++, $field->caption, array('value' => $item->email)));
						break;
					case 'corewebsite':
						$this->_addItemData($item_xml, $this->_buildElement('link', $i++, $field->caption, array('value' => $item->website)));
						break;
				}
			}
		}

		return $item_xml;
	}

}