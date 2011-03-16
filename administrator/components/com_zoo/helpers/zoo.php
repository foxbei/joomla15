<?php
/**
* @package   ZOO Component
* @file      zoo.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: ZooHelper
		The general helper Class for zoo
*/
class ZooHelper {

	/*
		Function: toolbarHelp
			Add help button to current toolbar to show help url in popup window.

	   Parameters:
	      $ref - Help url

	   Returns:
	      Void
	*/
	public static function toolbarHelp($ref = 'http://docs.yootheme.com/home/category/zoo-20') {
		$toolbar = JToolBar::getInstance('toolbar');
		$toolbar->appendButton('ZooHelp', $ref);
	}

	/*
		Function: resizeImage
			Resize and cache image file.

		Returns:
			String - image path
	*/
	public static function resizeImage($file, $width, $height) {

		// init vars
		$width      = (int) $width;
		$height     = (int) $height;
		$file_info  = pathinfo($file);
		$thumbfile  = JPATH_ROOT.'/cache/com_zoo/images/'. $file_info['filename'] . '_' . md5($file.$width.$height) . '.' . $file_info['extension'];
		$cache_time = 86400; // cache time 24h

		// check thumbnail directory
		if (!JFolder::exists(dirname($thumbfile))) {
			JFolder::create(dirname($thumbfile));
		}

		// create or re-cache thumbnail
		if (YImageThumbnail::check() && (!is_file($thumbfile) || ($cache_time > 0 && time() > (filemtime($thumbfile) + $cache_time)))) {
			$thumbnail = new YImageThumbnail($file);

			if ($width > 0 && $height > 0) {
				$thumbnail->setSize($width, $height);
				$thumbnail->save($thumbfile);
			} else if ($width > 0 && $height == 0) {
				$thumbnail->sizeWidth($width);
				$thumbnail->save($thumbfile);
			} else if ($width == 0 && $height > 0) {
				$thumbnail->sizeHeight($height);
				$thumbnail->save($thumbfile);
			} else {
                if (JFile::exists($file)) {
                    JFile::copy($file, $thumbfile);
                }
            }

		}

		if (is_file($thumbfile)) {
			return $thumbfile;
		}

		return $file;
	}

	/*
		Function: triggerContentPlugins
			Trigger joomla content plugins on given text.

	   Parameters:
            $text - Text

		Returns:
			String - Text
	*/
	public static function triggerContentPlugins($text) {

		// import joomla content plugins
		JPluginHelper::importPlugin('content');

		$params        = new JParameter('');
		$dispatcher    = JDispatcher::getInstance();
		$article       = JTable::getInstance('content');
		$article->text = $text;

		// disable loadmodule plugin on feed view
		if (JFactory::getDocument()->getType() == 'feed') {
			$plugin = JPluginHelper::getPlugin('content', 'loadmodule');
			$pluginParams = new JParameter($plugin->params);
			if ($pluginParams->get('enabled', 1)) {
				// expression to search for
				$regex = '/{loadposition\s*.*?}/i';
				$article->text = preg_replace($regex, '', $article->text);
			}
		}

		$dispatcher->trigger('onPrepareContent', array(&$article, &$params, 0));

		return $article->text;
	}

    /*
		Function: getGroups
			Returns user group objects.

		Returns:
			Array - groups
	*/
	public static function getGroups() {

		$db    = JFactory::getDBO();
		$query = "SELECT g.id, g.name FROM #__groups AS g";

		$db->setQuery($query);

		return $db->loadObjectList("id");
	}

    /*
		Function: getLayouts
			Returns layouts for a type of an app.

		Returns:
			Array - layouts
	*/
    public static function getLayouts($application, $type_id, $layout_type = '') {

        $result = array();

        // get template
        if ($template = $application->getTemplate()) {

			// get renderer
			$renderer = new ItemRenderer();
			$renderer->addPath($template->getPath());

			$path   = 'item';
			$prefix = 'item.';
			if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type_id)) {
				$path   .= DIRECTORY_SEPARATOR.$type_id;
				$prefix .= $type_id.'.';
			}

			foreach ($renderer->getLayouts($path) as $layout) {
				$metadata = $renderer->getLayoutMetaData($prefix.$layout);
				if (empty($layout_type) || ($metadata->get('type') == $layout_type)) {
					$result[$layout] = $metadata;
				}
			}
		}

        return $result;

    }

}

/*
   Class: JHTMLZoo
   	  A class that contains zoo html functions
*/
class JHTMLZoo {

	/*
    	Function: calendar
    	  Get zoo datepicker.

	   Returns:
	      Returns zoo datepicker html string.
 	*/
	public static function calendar($value, $name, $id, $attribs = null)	{

		if (!defined('ZOO_CALENDAR_SCRIPT_DECLARATION')) {
			define('ZOO_CALENDAR_SCRIPT_DECLARATION', true);

			JHTML::script('date.js', 'administrator/components/com_zoo/assets/js/');

			$translations = array(
				'closeText' => 'Done',
				'currentText' => 'Today',
				'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
				'dayNamesMin' => array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
				'dayNamesShort' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
				'monthNames' => array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
				'monthNamesShort' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
				'prevText' => 'Prev',
				'nextText' => 'Next',
				'weekHeader' => 'Wk',
				'appendText' => '(yyyy-mm-dd)'
			);

			foreach ($translations as $key => $translation) {
				$translations[$key] = is_array($translation) ? array_map(create_function('$text', 'return JText::_($text);'), $translation) : JText::_($translation);
			}

			$javascript = 'jQuery(function($) { $("body").Calendar({ translations: '.json_encode($translations).' }); });';

			JFactory::getDocument()->addScriptDeclaration($javascript);			

		}

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		return '<input style="width: 110px" type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'
			.'<img src="'.JURI::root(true).'/templates/system/images/calendar.png'.'" class="zoo-calendar" />';
	}

	/*
    	Function: image
    	  Get image resource info.

	   Returns:
	      Array - Image info
 	*/
	public static function image($image, $width = null, $height = null) {

		$resized_image = ZooHelper::resizeImage(JPATH_ROOT.DS.$image, $width, $height);
		$inner_path    = trim(str_replace('\\', '/', preg_replace('/^'.preg_quote(JPATH_ROOT, '/').'/i', '', $resized_image)), '/');
		$path 		   = JPATH_ROOT.'/'.$inner_path;

		if (is_file($path) && $size = getimagesize($path)) {

			$info['path'] 	= $path;
			$info['src'] 	= JURI::root().$inner_path;
			$info['mime'] 	= $size['mime'];
			$info['width'] 	= $size[0];
			$info['height'] = $size[1];
			$info['width_height'] = sprintf('width="%d" height="%d"', $info['width'], $info['height']);

			return $info;
		}

		return null;
	}

	/*
		Function: categoryList
			Returns category select list html string.
	*/
	public static function categoryList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false) {

		// set options
		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

		// get category tree list
		$list = CategoryHelper::buildTreeList(0, $application->getCategoryTree(), array(), '-&nbsp;', '.&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;');

		// create options
		foreach ($list as $category) {
			$options[] = JHTML::_('select.option', $category->id, $category->treename);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);

	}

	/*
		Function: layoutList
			Returns layout select list html string.
	*/
	public static function layoutList($application, $type_id, $layout_type, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false) {

		// set options
		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

        $layouts = ZooHelper::getLayouts($application, $type_id, $layout_type);

        foreach ($layouts as $layout => $metadata) {
            $options[] = JHTML::_('select.option', $layout, $metadata->get('name'));
        }

        return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);


		// create options
		foreach ($application->getTemplates() as $template) {
			$metadata = $template->getMetadata();
			$options[] = JHTML::_('select.option', $template->name, $metadata['name']);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);

	}

 	/*
    	Function: typeList
    		Returns type select list html string.
 	*/
	public static function typeList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $filter = array()) {

		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

		foreach (Zoo::getApplication()->getTypes() as $type) {
			if (empty($filter) || in_array($type->id, $filter)) {
				$options[] = JHTML::_('select.option', $type->id, JText::_($type->name));
			}
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: authorList
    		Returns author select list html string.
 	*/
    public static function authorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $show_registered_users = true) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
			    .' FROM #__users AS u'
                . ' WHERE u.block = 0'
                . ($show_registered_users ? '' : ' AND u.gid > 18');

		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
    }

 	/*
    	Function: itemAuthorList
    		Returns author select list html string.
 	*/
	public static function itemAuthorList($options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT DISTINCT u.id AS value, u.name AS text'
			    .' FROM '.ZOO_TABLE_ITEM.' AS i'
			    .' JOIN #__users AS u ON i.created_by = u.id';

		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: itemList
    		Returns item select list html string.
 	*/
	public static function itemList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = 'SELECT DISTINCT c.item_id as value, a.name as text'
			   	.' FROM '.ZOO_TABLE_COMMENT.' AS c'
			   	.' LEFT JOIN '.ZOO_TABLE_ITEM. ' AS a ON c.item_id = a.id'
			   	.' WHERE a.application_id = ' . $application->id
			   	.' ORDER BY a.name';

		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

		$db = YDatabase::getInstance();
		$rows = $db->queryAssocList($query);

		foreach ($rows as $row) {
			$options[] = JHTML::_('select.option', $row['value'], $row['text']);
		}

		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

  	/*
    	Function: accessLevel
    		Returns user access select list.
 	*/
	public static function accessLevel($selected = null, $exclude = array()) {

        if (is_array($exclude)) {
			reset($exclude);
		} else {
			$exclude = array($exclude);
		}

		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__groups'
		. ' ORDER BY id';

		$db = YDatabase::getInstance();
		$groups = $db->queryAssocList($query);

        foreach($exclude as $key) {
            unset($groups[$key]);
        }

		return JHTML::_('select.genericlist', $groups, 'access', 'class="inputbox"', 'value', 'text', $selected, '', 1);

	}

 	/*
    	Function: itemAuthorList
    		Returns author select list html string.
 	*/
	public static function commentAuthorList($application, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {
		$query = "SELECT DISTINCT c.author AS value, c.author AS text"
			    ." FROM ".ZOO_TABLE_COMMENT." AS c"
			    .' LEFT JOIN '.ZOO_TABLE_ITEM. ' AS a ON c.item_id = a.id'
				." WHERE c.author <> ''"
				." AND a.application_id = " . $application->id
				." ORDER BY c.author";
		return self::queryList($query, $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: queryList
			Returns select list html string.
 	*/
	public static function queryList($query, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

		$db = YDatabase::getInstance();
		$list = $db->queryObjectList($query);

		$options = array_merge($options, $list);
		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: arrayList
			Returns select list html string.
 	*/
	public static function arrayList($array, $options, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false) {

		if (is_array($options)) {
			reset($options);
		} else {
			$options = array($options);
		}

		$options = array_merge($options, JHTMLZoo::listOptions($array));
		return JHTML::_('select.genericlist', $options, $name, $attribs, $key, $text, $selected, $idtag, $translate);
	}

 	/*
    	Function: selectOptions
    		Returns select option as JHTML compatible array.
 	*/
	public static function listOptions($array, $value = 'value', $text = 'text') {

		$options = array();

		if (is_array($array)) {
			foreach ($array as $val => $txt) {
				$options[] = JHTML::_('select.option', strval($val), $txt, $value, $text);
			}
		}

		return $options;
	}

}