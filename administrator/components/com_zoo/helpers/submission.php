<?php
/**
* @package   ZOO Component
* @file      submission.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: SubmissionHelper
		Helper class for submission
*/
class SubmissionHelper {

	/*
		Function: filterData
			Remove html from data

		Parameters:
			$data - Data

		Returns:
			String
	*/
	public static function filterData($data) {
        
		if (is_array($data)) {

			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = self::filterData($value);
			}
			return $result;

        } elseif (is_object($data)) {
            
            $result = new stdClass();
            foreach (get_object_vars($data) as $key => $value) {
				$result->$key = self::filterData($value);
			}
			return $result;

		} else {

			// remove all html tags or escape if in [code] tag
			$data = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', create_function('$matches', 'return htmlspecialchars($matches[0]);'), $data);
			$data = strip_tags($data);

			return $data;

		}
	}

	/*
		Function: translateIDToAlias
			Translate submission id to alias.

		Parameters:
			$id - Submission id

		Returns:
			Mixed - Null or Submission alias string
	*/
	public static function translateIDToAlias($id){
		$submission = YTable::getInstance('submission')->get($id);

		if ($submission) {
			return $submission->alias;
		}

		return null;
	}

	/*
		Function: translateAliasToID
			Translate submission alias to id.

		Return:
			Int - The submission id or 0 if not found
	*/
	public static function translateAliasToID($alias) {

		// init vars
		$db = JFactory::getDBO();

		// search alias
		$query = 'SELECT id'
			    .' FROM '.ZOO_TABLE_SUBMISSION
			    .' WHERE alias = '.$db->Quote($alias);
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	/*
		Function: getAlias
			Get unique submission alias.

		Parameters:
			$id - Submission id
			$alias - Submission alias

		Returns:
			Mixed - Null or Submission alias string
	*/
	public static function getUniqueAlias($id, $alias = '') {

		if (empty($alias) && $id) {
			$alias = JFilterOutput::stringURLSafe(YTable::getInstance('submission')->get($id)->name);
		}

		if (!empty($alias)) {
			$i = 2;
			$new_alias = $alias;
			while (self::checkAliasExists($new_alias, $id)) {
				$new_alias = $alias . '-' . $i++;
			}
			return $new_alias;
		}

		return $alias;
	}

	/*
 		Function: checkAliasExists
 			Method to check if a alias already exists.
	*/
	public static function checkAliasExists($alias, $id = 0) {

		$xid = intval(self::translateAliasToID($alias));
		if ($xid && $xid != intval($id)) {
			return true;
		}

		return false;
	}

	/*
		Function: getSubmissionHash
			Retrieve hash of submission, type, item.

		Parameters:
			$submission_id - Submission id
			$type_id - Type id
			$item_id - Item id

		Returns:
			String
	*/
	public static function getSubmissionHash($submission_id, $type_id, $item_id = 0) {

		// get secret from config
		$secret = JFactory::getConfig()->getValue('config.secret');

        $item_id = empty($item_id) ? 0 : $item_id;

		return md5($submission_id.$type_id.$item_id.$secret);
	}

}

/*
	Class: SubmissionHelperException
*/
class SubmissionHelperException extends YException {}