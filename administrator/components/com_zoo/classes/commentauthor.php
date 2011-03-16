<?php
/**
* @package   ZOO Component
* @file      commentauthor.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: CommentAuthor
		Author related attributes and functions.
*/
class CommentAuthor {

	public $name;
	public $email;
	public $url;
	public $user_id;

	/*
    	Function: __construct
    	  Default Constructor

		Parameters:
	      $name - Author name
	      $email - Email address
	      $url - Homepage url
	      $user_id - Joomla user id

 	*/
	public function __construct($name = '', $email = '', $url = '', $user_id = '') {

		// set vars
		$this->name    = $name;
		$this->email   = $email;
		$this->url     = $url;
		$this->user_id = $user_id;
	}

	public function getAvatar($size = 32) {
		$default = JURI::root().'media/zoo/assets/images/avatar.png';
		
		if ($this->email) {
			return '<img title="'.$this->name.'" src="http://www.gravatar.com/avatar/'.md5(JString::strtolower($this->email)).'?s='.$size.'&amp;d='.$default.'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		} else {
			return '<img title="'.$this->name.'" src="'.$default.'" height="'.$size.'" width="'.$size.'" alt="'.$this->name.'" />';
		}
	}

	public function isGuest() {
		return empty($this->user_id);
	}
	
	public function isJoomlaAdmin() {
		return false;
	}
	
	public function getUserType() {
		return strtolower(str_replace('CommentAuthor', '', get_class($this)));
	}
	
}

/*
	Class: CommentAuthorJoomla
		Joomla Author related attributes and functions.
*/
class CommentAuthorJoomla extends CommentAuthor {

	public function getJoomlaUser() {
		return JUser::getInstance($this->user_id);
	}

	public function isJoomlaAdmin() {
		$user = $this->getJoomlaUser();
		return UserHelper::isJoomlaAdmin($user);
	}

}

/*
	Class: CommentAuthorFacebook
		Facebook Author related attributes and functions.
*/
class CommentAuthorFacebook extends CommentAuthor {

	public function getAvatar($size = 32) {

		if ($this->user_id) {

			$cache 		 = new YCache(ZOO_CACHE_PATH.DS.'author_cache.txt', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';

			// try to get avatar url from cache
			if ($cache_check) {
				$url = $cache->get($this->user_id);
			}

			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = CommentHelper::getFacebookFields($this->user_id, array('picture'));
				if (isset($info['picture'])) {
					$url = $info['picture'];
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url);
					$cache->save();
				}
			}

			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}
	
}

/*
	Class: CommentAuthorTwitter
		Twitter Author related attributes and functions.
*/
class CommentAuthorTwitter extends CommentAuthor {

	public function getAvatar($size = 32) {
		if ($this->user_id) {
			
			$cache 		 = new YCache(ZOO_CACHE_PATH.DS.'author_cache.txt', true, 604800);
			$cache_check = ($cache) ? $cache->check() : false;
			$url 		 = '';
			
			// try to get avatar url from cache
			if ($cache_check) { 
				$url = $cache->get($this->user_id);
			}
			
			// if url is empty, try to get avatar url from twitter
			if (empty($url)) {
				$info = CommentHelper::getTwitterFields($this->user_id, array('profile_image_url'));
				if (isset($info['profile_image_url'])) {
					$url = $info['profile_image_url'];
				}
				if ($cache_check) {
					$cache->set($this->user_id, $url);
					$cache->save();
				}
			}
			
			if (!empty($url)) {
				return '<img alt="'.$this->name.'" title="'.$this->name.'" src="'.$url.'" height="'.$size.'" width="'.$size.'" />';
			}

		}
	    return parent::getAvatar($size);
	}
	
}