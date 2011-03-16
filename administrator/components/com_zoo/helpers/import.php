<?php
/**
* @package   ZOO Component
* @file      import.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: ImportHelper
   The Helper Class for import
*/
class ImportHelper {

	/*
		Function: import
			Import from xml file.

		Parameters:
			$xml_file 			- The xml file
			$import_frontpage 	- if true, frontpage will be imported
			$frontpage_params 	- frontpage params to import
			$import_categories 	- if true, categories will be imported
			$category_params 	- category params to import
			$element_assignment - the element assignment
			$type_select 		- the selected types

		Returns:
			Boolean - true on success
	*/
	public static function import(
		$xml_file, 
		$import_frontpage = true, 
		$frontpage_params = array(), 
		$import_categories = true, 
		$category_params = array(), 
		$element_assignment = array(), 
		$types = array()) {
		
		if ($xml = YXML::loadFile($xml_file)) {
			
			// get application
			if ($application = Zoo::getApplication()) {
				
				// import frontpage
				if ($import_frontpage) {
					self::_importFrontpage($application, $xml->getElementByPath('categories/category[@id="_root"]'), $frontpage_params);
				}
				
				// import categories		
				if ($import_categories) {
					$categories = self::_importCategories($application, $xml->getElementsByPath('categories/category[not(@id="_root")]'), $category_params);
				}
				
				// import items
				$items = self::_importItems($application, $xml->items, $element_assignment, $types);
												
				// save item -> category relationship
				if ($import_categories) {
					$values = array();
					foreach($items as $item) {
						foreach ($item->categories as $category_alias) {
							if (isset($categories[$category_alias]) || $category_alias == '_root') {
								$values[] = '(' . ($category_alias == '_root' ? $category_id = 0 : (int)$categories[$category_alias]->id) . ',' . $item->id . ')';
							}
						}
					}
	
					if (!empty($values)) {	
						YDatabase::getInstance()->query('INSERT INTO ' . ZOO_TABLE_CATEGORY_ITEM . ' VALUES ' . implode(',', $values));	
					}
				}		
				
				return true;
				
			}
			
			throw new ImportHelperException('No application to import too.');
			
		} 

		throw new ImportHelperException('No valid xml file.');

	}

	private static function _importFrontpage(Application $application, YXMLElement $frontpage_xml = null, $frontpage_params = array()) {
		if (!empty($frontpage_xml)) {
			$table = YTable::getInstance('application');
			
			$application->description = (string) $frontpage_xml->description;
			
			// set frontpage params
			$params = $application->getParams();
			foreach ($frontpage_params as $params_type => $assigned_params) {
				foreach ($assigned_params as $assigned_frontpage_param => $param) {
					$param_xml = $frontpage_xml->getElementByPath('content/*[@name="'.$assigned_frontpage_param.'"]');
					if ($param && $param_xml) {		
						switch ($params_type) {
							case 'zooimage':
								if ($param_xml->path) {
									$params->set('content.'.$param, (string) $param_xml->path);
								}
								if ($param_xml->width) {
									$params->set('content.'.$param . '_width', (string) $param_xml->width);
								}
								if ($param_xml->height) {
									$params->set('content.'.$param . '_height', (string) $param_xml->height);
								}
								break;
							case 'text':
							case 'textarea':
								$params->set('content.'.$param, (string) $param_xml);
								break;
						}
					}
				}
			}
			$application->params = $params->toString();
	
			// save application
			try {
				$table->save($application);
			} catch (YException $e) {
				JError::raiseNotice(0, JText::_('Error Importing Frontpage').' ('.$e.')');
			}
		}
	}
	
	private static function _importCategories(Application $application, $categoriy_xml_array = array(), $category_params = array()) {

		$table = YTable::getInstance('category');

		$categories = array();
		// first iteration: save category vars
		foreach ($categoriy_xml_array as $category_xml) {

			$category 		 = new Category();

            // store old alias
			$category->old_alias = (string) $category_xml->attributes()->id;

			$category->alias = YString::sluggify($category->old_alias);
			
			// store old parent alias
			if ($category_xml->parent) {
				$category->old_parent_alias = (string) $category_xml->parent;							
			}
									
			// set a valid category alias
			while (CategoryHelper::checkAliasExists($category->alias)) {
				$category->alias .= '-2';
			}

			// set category values
			$vars = get_object_vars($category);
			foreach ($category_xml->children() as $child) {
				$name = $child->getName();
				if (in_array($name, $vars)) {
					$category->$name = (string) $child;
				}
			}
			$category->parent = 0;
			$category->application_id = $application->id;
							
			// set category params
			$params = $category->getParams();
			foreach ($category_params as $params_type => $assigned_params) {
				foreach ($assigned_params as $assigned_category_param => $param) {
					$param_xml = $category_xml->getElementByPath('content/*[@name="'.$assigned_category_param.'"]');
					if ($param && $param_xml) {		
						switch ($params_type) {
							case 'zooimage':
								if ($param_xml->path) {
									$params->set('content.'.$param, (string) $param_xml->path);
								}
								if ($param_xml->width) {
									$params->set('content.'.$param . '_width', (string) $param_xml->width);
								}
								if ($param_xml->height) {
									$params->set('content.'.$param . '_height', (string) $param_xml->height);
								}
								break;
							case 'text':
							case 'textarea':
								$params->set('content.'.$param, (string) $param_xml);
								break;
						}
					}
				}
			}
			$category->params = $params->toString();

			// save category, to get id
			try {
				
				$table->save($category);
				
			} catch (YException $e) {
				
				JError::raiseNotice(0, JText::_('Error Importing Category').' ('.$e.')');
				
			}						
			// store category for second iteration 
			$categories[$category->old_alias] = $category;						

		}
		
		// second iteration: set parent relationship
		foreach ($categories as $category) {
			// only save if parent is set
			if (isset($category->old_parent_alias) && (!empty($category->old_parent_alias) && $category->old_parent_alias != '_root')) {
				$category->parent = $categories[$category->old_parent_alias]->id;
				try {
					
					$table->save($category);
					
				} catch (YException $e) {
					
					JError::raiseNotice(0, JText::_('Error Importing Category').' ('.$e.')');
					
				}							
			}
		}

		return $categories;
	}

	private static function _importItems(Application $application, $items_xml_array = array(), $element_assignment = array(), $types = array()) {
		
		$table = YTable::getInstance('item');
		
		// get application types
		$app_types = $application->getTypes();
			
		$items = array();
		foreach ($items_xml_array as $items_xml) {
			
			$index = (string) $items_xml->attributes()->name;
			if (isset($types[$index]) && !empty($types[$index]) && ($type = $app_types[$types[$index]])) {

				foreach ($items_xml->item as $item_xml) {
															
					$item 		 	 = new Item();
                    $item->old_alias = (string) $item_xml->attributes()->id;
					$item->alias 	 = YString::sluggify($item->old_alias);
					$item->type  	 = $type->id;
													
					// set a valid category alias
					while (ItemHelper::checkAliasExists($item->alias)) {
						$item->alias .= '-2';
					}
					
					// set item values
					$vars = get_object_vars($item);
					foreach ($item_xml->children() as $child) {
						$name = $child->getName();
						if (in_array($name, $vars)) {
							$item->$name = (string) $child;
						}
					}
					
					// store application id
					$item->application_id = $application->id;

					// store categories
					$item->categories = array();
					foreach ($item_xml->getElementsByPath('categories/category') as $category_xml) {
						$item->categories[] = (string) $category_xml;
					}						
					
					// store tags
					$tags = array();
					foreach ($item_xml->getElementsByPath('tags/tag') as $tag_xml) {
						$tags[] = (string) $tag_xml;
					}
                    $item->setTags($tags);

					// store author
					$item->created_by_alias = "";
					$user_id = JFactory::getUser()->get('id');
					if ($item_xml->author) {
						$author = (string) $item_xml->author;
						$query = 'SELECT id'
								.' FROM #__users'
								.' WHERE username=' . YDatabase::getInstance()->quote($author);
								
						if ($author_id = (int) YDatabase::getInstance()->queryResult($query)) {
							$item->created_by = $author_id;		
						} else {
							$item->created_by_alias = $author;
						}
					}
					// if author is unknown set current user as author
					if (!$item->created_by) {
						$item->created_by = $user_id;
					}
					
					// store modified_by
					$item->modified_by = $user_id;
					
					// store element_data
					if ($data = $item_xml->data) {
						$elements = $type->getElements();
													
						foreach ($data->children() as $element_xml) {
							
							$old_element_alias = (string) $element_xml->attributes()->identifier;

							if (isset($element_assignment[$index][$old_element_alias][$type->id])) {
								if ($element_alias = $element_assignment[$index][$old_element_alias][$type->id]) {
									$element_xml->addAttribute('identifier', $element_alias);
									$elements[$element_alias]->setData($data->asXML(true, true));
								}
							}
						}
						
						$elements_xml = YXMLElement::create('elements');
						foreach ($elements as $id => $element) {
							$xml = YXML::loadString('<wrapper>'.$element->toXML().'</wrapper>');
							foreach ($xml->children() as $element_xml) {
								$elements_xml->appendChild($element_xml);
							}
						}
													
						$item->elements = $elements_xml->asXML(true, true);								
					}
					
					// store metadata
					$params = $item->getParams();
					if ($metadata = $item_xml->metadata) {
						foreach ($metadata->children() as $metadata_xml) {
							$params->set('metadata.' . $metadata_xml->getName(), (string) $metadata_xml);
						}
					}
					$item->params = $params->toString();
					
					// save item
					try {
						$table->save($item);
					} catch (YException $e) {
						JError::raiseNotice(0, JText::_('Error Importing Item').' ('.$e.')');
					}						
					$items[$item->old_alias] = $item;
				}
			}
		}

		// sanatize relateditems elements, save tags
		$tag_table = YTable::getInstance('tag');
		foreach ($items as $item) {
			foreach ($item->getElements() as $element) {
				if ($element->getElementType() == 'relateditems') {
					$relateditems = $element->getElementData()->get('item', array());
					$new_related_items = array();
					foreach ($relateditems as $key => $relateditem) {
						if (isset($items[$relateditem])) {
							$new_related_items[] = $items[$relateditem]->id;
						}
					}
					$element->getElementData()->set('item', $new_related_items);
				}
			}
			try {
				$table->save($item);
			} catch (YException $e) {
				JError::raiseNotice(0, JText::_('Error Importing Item').' ('.$e.')');
			}
		}

		return $items;
		
	}
	
	/*
		Function: getImportInfo
			Builds the assign element info from xml.

		Parameters:
			$export_xml - YXMLElement: the export xml

		Returns:
			Array - Assign element info
	*/	
	public static function getImportInfo(YXMLElement $export_xml) {

		$info = array();

		$application = Zoo::getApplication();

		// get frontpage count
		$info['frontpage_count'] = count($export_xml->getElementsByPath('categories/category[@id="_root"]'));
		
		// get category count
		$info['category_count'] = count($export_xml->getElementsByPath('categories/category[not(@id="_root")]'));

		// get frontpage params
		$info['frontpage_params'] = array();
		foreach ($application->getMetaXML()->getElementsByPath('params[@group="application-content"]/param') as $param) {
			$info['frontpage_params'][(string) $param->attributes()->type][(string) $param->attributes()->name] = (string) $param->attributes()->label;
		}		

		$info['frontpage_params_to_assign'] = array();
		foreach ($export_xml->getElementsByPath('categories/category[@id="_root"]/content') as $content) {	
			foreach ($content->children() as $param) {
				$name = (string) $param->attributes()->name;
				if (!isset($info['frontpage_params_to_assign'][$param->getName()]) || !in_array($name, $info['frontpage_params_to_assign'][$param->getName()])) {
					$param_name = ($param->getName() == 'image') ? 'zooimage' : $param->getName();
					$info['frontpage_params_to_assign'][$param_name][] = $name;
				}
			}
		}

		// get category params
		$info['category_params'] = array();
		foreach ($application->getMetaXML()->getElementsByPath('params[@group="category-content"]/param') as $param) {
			$info['category_params'][(string) $param->attributes()->type][(string) $param->attributes()->name] = (string) $param->attributes()->label;
		}

		$info['category_params_to_assign'] = array();
		foreach ($export_xml->getElementsByPath('categories/category[not(@id="_root")]/content') as $content) {	
			foreach ($content->children() as $param) {
				$name       = (string) $param->attributes()->name;
				$param_name = ($param->getName() == 'image') ? 'zooimage' : $param->getName();
				if (!isset($info['category_params_to_assign'][$param_name]) || !in_array($name, $info['category_params_to_assign'][$param_name])) {
					$info['category_params_to_assign'][$param_name][] = $name;
				}
			}
		}
		
		// get types
		foreach ($application->getTypes() as $type) {
			foreach ($type->getElements() as $element) {
				$type_elements[$type->id][$element->getElementType()][] = $element;
			}
		}

		// get item types
		$info['items'] = array();
		foreach ($export_xml->items as $group => $items) {
			$group = ($items->attributes()->name) ? (string) $items->attributes()->name : $group;			
			if (($count = count($items->item)) && ($data = $items->item[0]->data)) {
				$info['items'][$group]['elements'] = array();
				foreach ($data->children() as $element_xml) {
					$alias = (string) $element_xml->attributes()->identifier;
					if (!isset($info['items'][$group]['elements'][$alias])){ 
						$element_type = $element_xml->getName();
						$element_name = (string) $element_xml->attributes()->name;

						// add element type
						$info['items'][$group]['elements'][$alias]['type'] = ucfirst($element_type);

						// add element name
						$info['items'][$group]['elements'][$alias]['name'] = $element_name;

						// add elements to assign too
						$info['items'][$group]['elements'][$alias]['assign'] = array();
						foreach ($type_elements as $type => $assign_elements) { 
							if (isset($assign_elements[$element_type])) {
								$info['items'][$group]['elements'][$alias]['assign'][$type] = $assign_elements[$element_type];
							} 
						} 
					}
				}
				$info['items'][$group]['item_count'] = $count;
			}
		}
	
		return $info;
	}

	/*
		Function: import
			Import from xml file.

		Parameters:
			$file				- The csv file
			$type			 	- the type to import to
			$contains_headers 	- does the csv file contain a header row
			$field_separator 	- the field separator
			$field_enclosure 	- the field enclosure
			$element_assignment - the element assignment

		Returns:
			Boolean - true on success
	*/
	public static function importCSV(
		$file,
		$type = '',
		$contains_headers = false,
		$field_separator = ',',
		$field_enclosure = '"',
		$element_assignment = array()) {

		// get application
		if ($application = Zoo::getApplication()) {

			if ($type_obj = $application->getType($type)) {

				$c = 0;
				$assignments = array();
				foreach ($element_assignment as $column => $value) {
					if (!empty($value[$type])) {
						$name = $value[$type];
						$assignments[$name][] = $column;
					}
				}

				if (!isset($assignments['_name'])) {
					throw new ImportHelperException('No item name was assigned.');
				}

				if (($handle = fopen($file, "r")) !== FALSE) {

					$item_table   = YTable::getInstance('item');
					$category_table = YTable::getInstance('category');
					$user_id = JFactory::getUser()->get('id');
					$now     = JFactory::getDate();
					$row	 = 0;
					
					$categories = array();
					while (($data = fgetcsv($handle, 0, $field_separator, $field_enclosure)) !== FALSE) {
						if (!($contains_headers && $row == 0)) {

							$item = new Item();
							$item->application_id = $application->id;
							$item->type = $type;

							// store created by
							$item->created_by  = $user_id;

							// set created
							$item->created	   = $now->toMySQL();

							// store modified_by
							$item->modified_by = $user_id;

							// set modified
							$item->modified	   = $now->toMySQL();

							// store element_data and item name
							$item_categories = array();
							foreach ($assignments as $assignment => $columns) {
								$column = current($columns);
								switch ($assignment) {
									case '_name':
										$item->name = $data[$column];
										break;
									case '_created_by_alias':
										$item->created_by_alias = $data[$column];
										break;
									case '_created':
										if (!empty($data[$column])) {
											$item->created = $data[$column];
										}
										break;
									default:
										if (substr($assignment, 0, 9) == '_category') {
											foreach ($columns as $column) {
												$item_categories[] = $data[$column];
											}
										} else if ($element = $item->getElement($assignment)) {
											switch ($element->getElementType()) {
												case 'text':
												case 'textarea':
												case 'link':
												case 'email':
												case 'date':
													$element_data = array();
													foreach ($columns as $column) {
														if (!empty($data[$column])) {
															$element_data[$column] = array('value' => $data[$column]);
														}
													}
													$element->bindData($element_data);
													break;
												case 'image':
												case 'download':
													$element->bindData(array('file' => $data[$column]));
													break;
												case 'googlemaps':
													$element->bindData(array('location' => $data[$column]));
													break;
											}
										}
										break;
								}
							}

							$item->alias = YString::sluggify($item->name);

							if (empty($item->alias)) {
								$item->alias = '42';
							}

							// set a valid category alias
							while (ItemHelper::checkAliasExists($item->alias)) {
								$item->alias .= '-2';
							}

							if (!empty($item->name)) {
								try {
									$item_table->save($item);
									
									// store categories
									foreach ($item_categories as $category_name) {

										$related_categories = $category_table->getByName($application->id, $category_name);

										if (empty($related_categories)) {

											$category = new Category();
											$category->application_id = $application->id;
											$category->name = $category_name;
											$category->parent = 0;

											$category->alias = YString::sluggify($category_name);

											// set a valid category alias
											while (CategoryHelper::checkAliasExists($category->alias)) {
												$category->alias .= '-2';
											}

											try {

												$category_table->save($category);
												$related_categories[] = $category;

											} catch (CategoryTableException $e) {}
										}

										// add category to item relations
										if (!empty($related_categories)) {
											foreach($related_categories as $related_category){

												// insert relation to database
												$query = "INSERT IGNORE INTO ".ZOO_TABLE_CATEGORY_ITEM
														." VALUES (".(int) $related_category->id.",".(int) $item->id.")";

												// execute database query
												YDatabase::getInstance()->query($query);
											}
										}
									}

								} catch (ItemTableException $e) {}
							}
						}

						// get max column count
						$row++;
					}

					fclose($handle);

					return true;

				} else {
					throw new ImportHelperException('Could not open csv file.');
				}
			} else {
				throw new ImportHelperException('Could not find type.');
			}
		}

		throw new ImportHelperException('No application to import too.');

	}

	/*
		Function: getImportInfoCSV
			Builds the assign element info from csv.

		Parameters:
			$file - CSV file

		Returns:
			Array - Assign element info
	*/
	public static function getImportInfoCSV($file, $contains_headers = false, $field_separator = ',', $field_enclosure = '"') {

		$info = array();

		$application = Zoo::getApplication();
		
		// get types
		$info['types'] = array();
		foreach ($application->getTypes() as $type) {
			$info['types'][$type->id] = array();
			foreach ($type->getElements() as $element) {
				// filter elements
				if (in_array($element->getElementType(), array('text', 'textarea', 'link', 'email', 'image', 'download', 'date', 'googlemaps'))) {
					$info['types'][$type->id][$element->getElementType()][] = $element;
				}
			}
		}

		// get item types
		$info['item_count'] = 0;

		$info['columns'] = array();

		// get column names and row count
		$row = 0;
		$columns = 0;
		if (($handle = fopen($file, "r")) !== FALSE) {
			
			while (($data = fgetcsv($handle, 0, $field_separator, $field_enclosure)) !== FALSE) {
				if ($row == 0) {
					// get column names from header row
					if ($contains_headers) {
						$info['columns'] = $data;						
					} else {
						$info['columns'] = array_fill(0, count($data), '');						
					}
				}
				
				// get max column count
				$row++;
				
			}

			// get item count
			$info['item_count'] = $contains_headers ? $row - 1 : $row;

			fclose($handle);
		}

		return $info;
	}

}

/*
	Class: ImportHelperException
*/
class ImportHelperException extends YException {}