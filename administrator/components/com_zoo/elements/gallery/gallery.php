<?php
/**
* @package   ZOO Component
* @file      gallery.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: ElementGallery
		The file element class
*/
class ElementGallery extends Element {

	protected $_uri;
	protected $_root;
	protected $_path;
	
	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter
				
		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		
		// init vars
		$value 	   = $this->_data->get('value');
		$directory = $this->_config->get('directory');
		$directory = JPATH_ROOT.DS.trim($directory, '/').DS.trim($value, '/');				
		
		return !empty($value) && is_readable($directory) && is_dir($directory);
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$this->_uri   = JURI::base();
		$this->_path  = JPATH_ROOT.DS.trim($this->_config->get('directory'), '/').DS.trim($this->_data->get('value'), '/');

		// set params
		$parameter = new YParameter();
		$parameter->loadArray($this->_config->toArray())->loadArray($params);
		$parameter->set('title', $this->_data->get('title'));

		// get thumbnails
		$thumbs = $this->_getThumbnails($parameter);
		
		// no thumbnails found
		if (!count($thumbs)) {
			return JText::_('No thumbnails found');
		}

		// sort thumbnails
		$this->_sortThumbnails($thumbs, $parameter->get('order', 'asc'));

		// limit thumbnails to count
		$count = intval($parameter->get('count', 0));
		if ($count > 0 && $count < count($thumbs)) {
			$thumbs = array_slice($thumbs, 0, $count);
		}

		// add css and javascript
		JHTML::script('gallery.js', 'administrator/components/com_zoo/elements/gallery/assets/js/');
		JHTML::stylesheet('style.css', 'administrator/components/com_zoo/elements/gallery/assets/css/');

		// load lightbox
		if ($parameter->get('load_lightbox', 0)) {
			JHTML::script('slimbox_packed.js', 'administrator/components/com_zoo/elements/gallery/assets/lightbox/');
			JHTML::stylesheet('slimbox.css', 'administrator/components/com_zoo/elements/gallery/assets/lightbox/css/');
		}

		// init template vars
	   	static $gallery_count = 1;
		$gallery_id = 'zoo-gallery-'.$gallery_count++;

		// render layout
		$mode      = $parameter->get('mode', 'lightbox');
		$thumb     = $parameter->get('thumb', 'default');
		$spotlight = $parameter->get('spotlight', 0);
		$effect    = $parameter->get('effect', 'fade');
		$rel       = $parameter->get('rel', '');
		
		if ($layout = $this->getLayout($mode.'.php')) {
			return self::renderLayout($layout, compact('thumbs', 'gallery_id', 'mode', 'thumb', 'spotlight', 'effect', 'rel'));
		}

		return null;
	}
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$title     = htmlspecialchars(html_entity_decode($this->_data->get('title'), ENT_QUOTES), ENT_QUOTES);
		$directory = $this->_config->get('directory');

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'title' => $title,
                    'directory' => $directory,
					'value' => $this->_data->get('value')
                )
            );
        }

        return null;

	}

	protected function _getThumbnails($params) {
		
		$thumbs     = array();
		$width      = $params->get('width');
		$height     = $params->get('height');
		$resize     = $params->get('resize', 1);
		$title      = $this->_data->get('title', '');
		$files      = JFolder::files($this->_path, '.', false, true, array('.svn', 'CVS','.DS_Store'));
	    $files      = array_filter($files, create_function('$file', 'return preg_match("#(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$#i", $file);'));

		// set default thumbnail size, if incorrect sizes defined
		$width  = intval($width);
		$height = intval($height);
		if ($width < 1 && $height < 1) {
			$width  = 100;
			$height = null;
		}
		
		foreach ($files as $file) {

			$filename = basename($file);
			$thumb    = ZooHelper::resizeImage($file, $width, $height);

			// if thumbnail exists, add it to return value
			if (is_file($thumb)) {

				// set image name or title if exsist
				if ($title != '') {
					$name = $title;
				} else {
					$name = JFile::stripExt($filename);
					$name = JString::str_ireplace('_', ' ', $name);
					$name = JString::ucwords($name);
				}
				
				// get image info
				list($thumb_width, $thumb_height) = @getimagesize($thumb);
				
				$thumbs[] = array(
					'name'         => $name,
					'filename'     => $filename,
					'img'          => $this->_uri.$this->_getRelativePath($file),
					'img_file'     => $file,
					'thumb'        => $this->_uri.$this->_getRelativePath($thumb),
					'thumb_width'  => $thumb_width,
					'thumb_height' => $thumb_height
				);
			}
		}
				
		return $thumbs;
	}

	protected function _sortThumbnails(&$thumbs, $order) {
		usort($thumbs, create_function('$a,$b', 'return strcmp($a["filename"], $b["filename"]);'));
		if ($order == 'random') shuffle($thumbs);
		if ($order == 'desc') $thumbs = array_reverse($thumbs);
	}

	protected function _getRelativePath($file) {
        return trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file)), '/');
	}

}