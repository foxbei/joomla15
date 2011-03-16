<?php
/**
* @package   ZOO Component
* @file      image.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: ElementImage
		The image element class
*/
class ElementImage extends Element implements iSubmittable, iSubmissionUpload {

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
		$file = $this->_data->get('file');
		
		return !empty($file);
	}
	
	/*
		Function: getSearchData
			Get elements search data.
					
		Returns:
			String - Search data
	*/
	public function getSearchData() {
		if ($this->_config->get('custom_title')) {
			return $this->_data->get('title');
		}		
		return null;
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
		$title  	  = $this->_data->get('title');
		$params		  = new YArray($params);
		$file  		  = ZooHelper::resizeImage(JPATH_ROOT.DS.$this->_data->get('file'), $params->get('width', 0), $params->get('height', 0));
		$link   	  = JURI::root() . trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $file)), '/');

		if ($params->get('link_to_item', false)) {

            if ($this->getItem()->getState()) {
                $url	   = RouteHelper::getItemRoute($this->_item);
                $target	   = false;
                $rel  	   = '';
                $title 	   = empty($title) ? $this->_item->name : $title;
            } else {

                $url = $target = $rel = '';
                
            }
			
		} else if ($this->_data->get('link')) {
			
			$url 	= $this->_data->get('link');		
			$target	= $this->_data->get('target');
			$rel  	= $this->_data->get('rel');

		} else if ($this->_data->get('lightbox_image')) {

			// load lightbox
			if ($this->_config->get('load_lightbox', 0)) {
				JHTML::script('slimbox_packed.js', 'administrator/components/com_zoo/elements/gallery/assets/lightbox/');
				JHTML::stylesheet('slimbox.css', 'administrator/components/com_zoo/elements/gallery/assets/lightbox/css/');
			}

			$lightbox_image = ZooHelper::resizeImage(JPATH_ROOT.DS.$this->_data->get('lightbox_image', ''), 0 , 0);
			$url		    = JURI::root() . trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $lightbox_image)), '/');
			$target	= '';
			$rel  	= 'lightbox['.$title.']';

		} else {
			
			$url = $target = $rel = '';	
					
		}

		// get alt
		$alt = empty($title) ? $this->_item->name : $title;

		// render layout
		if ($layout = $this->getLayout()) {
			return self::renderLayout($layout, 
				array(
					'file' => $file,
					'title' => $title,
					'alt' => $alt,
					'link' => $link,
					'link_enabled' => !empty($url),
					'url' => $url,
					'target' => $target,
					'rel' => $rel
				)
			);
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

		JHTML::script('image.js', 'administrator/components/com_zoo/assets/js/');

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'file' => $this->_data->get('file'),
                    'title' => $this->_data->get('title'),
                    'link' => $this->_data->get('link'),
                    'target' => $this->_data->get('target'),
                    'rel' => $this->_data->get('rel'),
					'lightbox_image' => $this->_data->get('lightbox_image')
                )
            );
        }

	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {

		// load js
		JHTML::script('image.js', 'administrator/components/com_zoo/elements/image/assets/js/');

        // init vars
        $image        = $this->_data->get('file');
        
        // is uploaded file
        $image        = is_array($image) ? '' : $image;

        // get params
        $params       = new YArray($params);
        $trusted_mode = $params->get('trusted_mode');
        
        // build image select
        $lists = array();
        if ($trusted_mode) {
            $options = array(JHTML::_('select.option', '', '- '.JText::_('Select Image').' -'));
            if (!empty($image) && !$this->_inUploadPath($image)) {
                $options[] = JHTML::_('select.option', $image, '- '.JText::_('No Change').' -');
            }
            $img_ext = str_replace(',', '|', trim(JComponentHelper::getParams('com_media')->get('image_extensions'), ','));
			foreach (YFile::readDirectoryFiles(JPATH_ROOT.'/'.$this->_getUploadImagePath() . '/', $this->_getUploadImagePath() . '/', '/\.('.$img_ext.')$/i', false) as $file) {
                $options[] = JHTML::_('select.option', $file, basename($file));
            }
            $lists['image_select'] = JHTML::_('select.genericlist', $options, 'elements['.$this->identifier.'][image]', 'class="image"', 'value', 'text', $image);
        } else {
            if (!empty($image)) {
                $image = ZooHelper::resizeImage(JPATH_ROOT.DS.$image, 0, 0);
                $image = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $image)), '/');
            }
        }

        if (!empty($image)) {
            $image = JURI::root() . $image;
        }

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array('element' => $this->identifier, 'lists' => $lists, 'image' => $image));
        }

	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {

        // init vars
        $trusted_mode = $params->get('trusted_mode');

        // get old file value
        $element = new ElementImage();
        $element->identifier = $this->identifier;
        $old_file = $element->setData($this->_item->elements)->getElementData()->get('file');

        $file = '';
        // get file from select list
        if ($trusted_mode && $file = $value->get('image')) {
         
            if (!$this->_inUploadPath($file) && $file != $old_file) {
                throw new YValidatorException(sprintf('This file is not located in the upload directory.'));
            }

            if (!JFile::exists($file)) {
                throw new YValidatorException(sprintf('This file does not exist.'));
            }

        // get file from upload
        } else {

            try {

                // get the uploaded file information
                $userfile = JRequest::getVar('elements_'.$this->identifier, array(), 'files', 'array');
                
				$max_upload_size = $this->_config->get('max_upload_size', '512') * 1024;
				$max_upload_size = empty($max_upload_size) ? null : $max_upload_size;
                $validator = new YValidatorFile(array('mime_type_group' => 'image', 'max_size' => $max_upload_size));
                $file = $validator->addMessage('mime_type_group', 'Uploaded file is not an image.')->clean($userfile);

            } catch (YValidatorException $e) {
                if ($e->getCode() != UPLOAD_ERR_NO_FILE) {
                    throw $e;
                }

                if (!$trusted_mode && $old_file && $value->get('image')) {
                    $file = $old_file;
                }

            }

        }

        if ($params->get('required') && empty($file)) {
            throw new YValidatorException('Please select an image to upload.');
        }

		return array('file' => $file);
	}

    protected function _inUploadPath($image) {
        return $this->_getUploadImagePath() == dirname($image);
    }

    protected function _getUploadImagePath() {
		return trim(trim($this->_config->get('upload_directory', 'images/stories/zoo/uploads/')), '\/');
    }

	/*
		Function: doUpload
			Does the actual upload during submission

		Returns:
			void
	*/
    public function doUpload() {

        // get the uploaded file information
        $userfile = $this->_data->get('file');

        if (is_array($userfile)) {
            // get file name
            $ext = YFile::getExtension($userfile['name']);
            $base_path = JPATH_ROOT . '/' . $this->_getUploadImagePath() . '/';
            $file = $tmp = $base_path . $userfile['name'];
            $filename = basename($file, '.'.$ext);

            $i = 1;
            while (JFile::exists($tmp)) {
                $tmp = $base_path . $filename . '-' . $i++ . '.' . $ext;
            }
            $file = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $tmp)), '/');;

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new YException('Unable to upload file.');
            }

            $this->_data->set('file', $file);
        }
    }

}

class ElementImageData extends ElementData{

	public function encodeData() {

		// add image width/height
		$filepath = JPATH_ROOT.DS.$this->_params->get('file');

		if (JFile::exists($filepath)) {
			$size = getimagesize($filepath);
			$this->set('width', ($size ? $size[0] : 0));
			$this->set('height', ($size ? $size[1] : 0));
		}
		
		return parent::encodeData();			
	}		
	
}