<?php
/**
* @package   ZOO Component
* @file      application.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: ApplicationHelper
   The Helper Class for application
*/
class ApplicationHelper {

	/*
		Function: getApplications
			Get all applications for an application group.

		Parameters:
			$group - Application group

		Returns:
			Array - The applications of the application group
	*/
    public static function getApplications($group) {
        // get application instances for selected group
        $applications = array();
        foreach (YTable::getInstance('application')->all(array('order' => 'name')) as $application) {
            if ($application->getGroup() == $group) {
                $applications[$application->id] = $application;
            }
        }
        return $applications;
    }

	/*
		Function: translateIDToAlias
			Translate application id to alias.

		Parameters:
			$id - Application id

		Returns:
			Mixed - Null or Category alias string
	*/
	public static function translateIDToAlias($id){
		$application = YTable::getInstance('application')->get($id);

		if ($application) {
			return $application->alias;
		}

		return null;
	}

	/*
		Function: translateAliasToID
			Translate application alias to id.

		Return:
			Int - The application id or 0 if not found
	*/
	public static function translateAliasToID($alias) {

		// init vars
		$db = YDatabase::getInstance();

		// search alias
		$query = 'SELECT id'
			    .' FROM '.ZOO_TABLE_APPLICATION
			    .' WHERE alias = '.$db->Quote($alias)
				.' LIMIT 1';

		return $db->queryResult($query);
	}

	/*
		Function: getAlias
			Get unique application alias.

		Parameters:
			$id - Application id
			$alias - Application alias

		Returns:
			Mixed - Null or Application alias string
	*/
	public static function getUniqueAlias($id, $alias = '') {

		if (empty($alias) && $id) {
			$alias = JFilterOutput::stringURLSafe(YTable::getInstance('application')->get($id)->name);
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
}
