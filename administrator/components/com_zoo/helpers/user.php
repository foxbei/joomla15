<?php
/**
* @package   ZOO Component
* @file      user.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
   Class: UserHelper
   The Helper Class for user
*/
class UserHelper {

    public static function isJoomlaAdmin(JUser $user) {
		return in_array(strtolower($user->usertype), array('superadministrator', 'super administrator', 'administrator')) || $user->gid == 25 || $user->gid == 24;
    }

    public static function isJoomlaSuperAdmin(JUser $user) {
		return in_array(strtolower($user->usertype), array('superadministrator', 'super administrator')) || $user->gid == 25;
    }

	public static function getBrowserDefaultLanguage() {
		$langs = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

			if (count($lang_parse[1])) {

				$langs = array_combine($lang_parse[1], $lang_parse[4]);

				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}

				arsort($langs, SORT_NUMERIC);
			}
		}

		return array_shift(explode('-', array_shift(array_keys($langs))));

	}
    
}
