<?php
/**
* @package   ZOO Component
* @file      comment.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: CommentHelper
		Helper class for comments
*/
class CommentHelper {

	/*
       Class constants
    */
	const COOKIE_PREFIX   = 'zoo-comment_';
	const COOKIE_LIFETIME = 15552000; // 6 months

    /*
		Variable: _author
			Active author.
    */
	protected static $_author;

	/*
		Function: countComments
			Retrieve comment count for item.

		Returns:
			Int
	*/
	public static function countComments($item) {
		return YTable::getInstance('comment')->count(array('conditions' => array("item_id = ? AND state = ?", $item->id, Comment::STATE_APPROVED)));
	}

	/*
		Function: renderComments
			Render comments and respond form html.

		Returns:
			String
	*/
	public static function renderComments($view, $item) {

		if ($item->getApplication()->isCommentsEnabled()) {
		
			// get application params
			$params = new YParameter();
			$params->loadArray(Zoo::getApplication()->getParams()->get('global.comments.'));

			if ($params->get('twitter_enable') && !function_exists('curl_init')) {
				JError::raiseWarning(500, JText::_('To use Twitter, CURL needs to be enabled in your php settings.'));
				$params->set('twitter_enable', false);
			}
			
			// get active author
			$active_author = self::activeAuthor();
	
			// get comments count
			$params->set('count', self::countComments($item));
			
			// get comment content from session
			$content = JFactory::getSession()->get('com_zoo.comment.content');
			$params->set('content', $content);
			
			// get comments and build tree
			$comments = YTable::getInstance('comment')->getCommentsForItem($item->id, $params->get('order', 'ASC'), $active_author);
			$comments = self::_buildTree($comments, $params->get('max_depth', 5));
			$comments = self::_buildComments($view, $comments, 0, $params);
			
			if ($item->isCommentsEnabled() || count($comments)) {
				// create comments html
				return $view->partial('comments', compact('item', 'active_author', 'comments', 'params'));
			}
		}
		
		return null;

	}

	/*
		Function: renderCommentLevels
			Render comment levels recursively as html.

		Returns:
			String
	*/
	protected static function _buildComments($view, $comments, $comment_id, $params, $level = 1) {
				
		$html = array();
		
		if (isset($comments[$comment_id]) && $comments[$comment_id]->hasChildren()) {
			foreach ($comments[$comment_id]->getChildren() as $comment) {
				$html[] = '<li>';
				$html[] = $view->partial('comment', array('comment' => $comment, 'author' => $comment->getAuthor(), 'params' => $params));
				$html[] = self::_buildComments($view, $comments, $comment->id, $params, $level + 1);
				$html[] = '</li>';
			}			
		}
		
		if (!empty($html)) {
			return '<ul class="level'.$level.'">'.implode("\n", $html).'</ul>';
		}
		
		return null;
	}

	/*
		Function: buildTree
			Build comment tree.

		Parameters:
			$comments - Comment array
			$depth - Maximum tree depth

		Returns:
			Array - Comment array
	*/
	protected static function _buildTree($comments, $depth) {

		// create root
		$comments[0] = new Comment();
		$comments[0]->id = 0;

		// set parent and child relations
		foreach ($comments as $comment) {
			if (isset($comments[$comment->parent_id])) {
				$comment->setParent($comments[$comment->parent_id]);
				$comments[$comment->parent_id]->addChild($comment);
			}
		}

		// get nested comments, which are too deep
		$move = array();
		foreach ($comments as $comment) {
			if (count($comment->getPathway()) > $depth) {
				$comment->getParent()->removeChild($comment);
				$move[] = $comment;
			}
		}

		// add comments to root again, which are too deep
		foreach ($move as $comment) {
			$comment->setParent($comments[0]);
			$comments[0]->addChild($comment);
		}
		
		return $comments;
	}
	
	/*
		Function: activeAuthor
			Retrieve currently active author object.

		Returns:
			CommentAuthor
	*/
	public static function activeAuthor() {
		
		if (!isset(self::$_author)) {

			// get login (joomla users always win)
			$login = YRequest::getString(self::COOKIE_PREFIX.'login', '', 'cookie');
						
			// get active user
			$user = JFactory::getUser();
			
			if ($user->id) {

				// create author object from user
				self::$_author = new CommentAuthorJoomla($user->name, $user->email, '', $user->id);

			} else if ($login == 'facebook'
						&& ($connection = self::getFacebookClient())
						&& ($content = $connection->getCurrentUserProfile())
						&& isset($content->id)
						&& isset($content->name)) {

				// create author object from facebook user id
				self::$_author = new CommentAuthorFacebook($content->name, null, null, $content->id);

			} else if ($login == 'twitter' 
						&& ($connection = self::getTwitterClient())
						&& ($content = $connection->get('account/verify_credentials'))
						&& isset($content->screen_name)
						&& isset($content->id)) {
							
				// create author object from twitter user id
				self::$_author = new CommentAuthorTwitter($content->screen_name, null, null, $content->id);			
				
			} else {

				self::twitterLogout();
				self::facebookLogout();
				
				// create author object from cookies
				$cookie = self::readCookies();
				self::$_author = new CommentAuthor($cookie['author'], $cookie['email'], $cookie['url']);
				
			}
		}

		setcookie(CommentHelper::COOKIE_PREFIX.'login', self::$_author->getUserType(), time() + CommentHelper::COOKIE_LIFETIME, '/');

		return self::$_author;
	}

	/*
		Function: readCookies
			Retrieve author, email, url from cookie.

		Returns:
			Array
	*/
	public static function readCookies() {

		// get cookies
		foreach (array('hash', 'author', 'email', 'url') as $key) {
			$data[$key] = YRequest::getString(self::COOKIE_PREFIX.$key, '', 'cookie');
		}
		
		// verify hash
		if (self::getCookieHash($data['author'], $data['email'], $data['url']) == $data['hash']) {
			return $data;
		}
		
		return array('hash' => null, 'author' => null, 'email' => null, 'url' => null);
	}

	/*
		Function: saveCookies
			Save author, email, url as cookie.

		Parameters:
			$data - Cookie data

		Returns:
			Void
	*/
	public static function saveCookies($author, $email, $url) {
	
		$hash = self::getCookieHash($author, $email, $url);
	
		// set cookies
		foreach (compact('hash', 'author', 'email', 'url') as $key => $value) {
			setcookie(self::COOKIE_PREFIX.$key, $value, time() + self::COOKIE_LIFETIME);
		}

	}

	/*
		Function: getCookieHash
			Retrieve hash of author and email.

		Parameters:
			$author - Author
			$email - Email
			$url - URL

		Returns:
			String
	*/
	public static function getCookieHash($author, $email, $url) {
		
		// get secret from config
		$secret = JFactory::getConfig()->getValue('config.secret');
		
		return md5($author.$email.$url.$secret);
	}

	/*
		Function: getClientIP
			Retrieve client ip address.

		Returns:
			String
	*/
	public static function getClientIP() {
		$ip = 'unknown';

		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$ip = getenv('REMOTE_ADDR');
		} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/*
		Function: matchWords
			Match words against comments content, author, URL, Email or IP.

		Parameters:
			$comments - Comment
			$words - Words to match against

		Returns:
			Boolean
	*/
	public static function matchWords($comment, $words) {
		
		$vars = array('author', 'email', 'url', 'ip', 'content');
		
		if ($words = explode("\n", $words)) {
			foreach ($words as $word) {
				if ($word = trim($word)) {

					$pattern = '/'.preg_quote($word).'/i';

					foreach ($vars as $var) {
						if (preg_match($pattern, $comment->$var)) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/*
		Function: filterContentInput
			Remove html from comment content

		Parameters:
			$content - Content

		Returns:
			String
	*/
	public static function filterContentInput($content) {

		// remove all html tags or escape if in [code] tag
		$content = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', create_function('$matches', 'return htmlspecialchars($matches[0]);'), $content);
		$content = strip_tags($content);

		return $content;
	}

	/*
		Function: filterContentOutput
			Auto linkify urls, emails

		Parameters:
			$content - Content

		Returns:
			String
	*/
	public static function filterContentOutput($content) {

		$content = ' '.$content.' ';
		$content = preg_replace_callback('/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:;,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix', 'CommentHelper::_makeURLClickable', $content);
	    $content = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto:$1\" rel=\"nofollow\">$1</a>$2", $content);
		$content = JString::substr($content, 1);
		$content = JString::substr($content, 0, -1);

		return nl2br($content);
	}

	protected static function _makeURLClickable($matches) {
		$url = $original_url = $matches[0];

		if (empty($url)) {
			return $url;
		}

		// Prepend scheme if URL appears to contain no scheme (unless a relative link starting with / or a php file).
		if (strpos($url, ':') === false &&	substr($url, 0, 1) != '/' && substr($url, 0, 1) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
			$url = 'http://' . $url;
		}

		return " <a href=\"$url\" rel=\"nofollow\">$original_url</a>";
	}

	/*
		Function: validateEmail
			Email address validation.

		Parameters:
			$email - Email address

		Returns:
			Boolean
	*/
	public static function validateEmail($email) {
		$pattern = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';

		return preg_match($pattern, $email);
	}

	/*
		Function: validateURL
			URL validation.

		Parameters:
			$url - URL

		Returns:
			Boolean
	*/
	public static function validateURL($url) {
		$pattern = '/^[a-z][a-z0-9+\-.]*:(\/\/([a-z0-9\-._~%!$&\'()*+,;=]+@)?([a-z0-9\-._~%]+|\[[a-f0-9:.]+\]|\[v[a-f0-9][a-z0-9\-._~%!$&\'()*+,;=:]+\])'
		          .'(:[0-9]+)?(\/[a-z0-9\-._~%!$&\'()*+,;=:@]+)*\/?|(\/?[a-z0-9\-._~%!$&\'()*+,;=:@]+(\/[a-z0-9\-._~%!$&\'()*+,;=:@]+)*\/?)?)'
		          .'(\?[a-z0-9\-._~%!$&\'()*+,;=:@\/?]*)?(\#[a-z0-9\-._~%!$&\'()*+,;=:@\/?]*)?$/i';
		
		return preg_match($pattern, $url);
	}

	/*
		Function: akismet
			Check if comment is spam using Akismet.

		Parameters:
			$comment - Comment
			$api_key - Akismet (Wordpress) API Key
		
		Returns:
			Void
	*/
	public static function akismet($comment, $api_key = '') {
		
		// load akismet class
		JLoader::register('Akismet', ZOO_ADMIN_PATH.'/libraries/akismet/akismet.php');

		// check comment
		$akismet = new Akismet(JURI::root(), $api_key);
		$akismet->setCommentAuthor($comment->author);
		$akismet->setCommentAuthorEmail($comment->email);
		$akismet->setCommentAuthorURL($comment->url);
		$akismet->setCommentContent($comment->content);

		// set state
		if ($akismet->isCommentSpam()) {
			$comment->state = Comment::STATE_SPAM;
		}
		
	}

	/*
		Function: mollom
			Check if comment is spam using Mollom.

		Parameters:
			$comment - Comment
			$public_key - Public Key
			$private_key - Private Key
		
		Returns:
			Void
	*/
	public static function mollom($comment, $public_key = '', $private_key = '') {
		
		// check if curl functions are available
		if (!function_exists('curl_init')) return;

		// load mollom class
		JLoader::register('Mollom', ZOO_ADMIN_PATH.'/libraries/mollom/mollom.php');

		// set keys and get servers
		Mollom::setPublicKey($public_key);
		Mollom::setPrivateKey($private_key);
		Mollom::setServerList(Mollom::getServerList());
				
		// check comment
		$feedback = Mollom::checkContent(null, null, $comment->content, $comment->author, $comment->url, $comment->email);

		// set state
		if ($feedback['spam'] != 'ham') {
			$comment->state = Comment::STATE_SPAM;
		}

	}

	public static function getFacebookClient() {

		// get comment params
		$params = new YParameter();
		$params->loadArray(Zoo::getApplication()->getParams()->get('global.comments.'));

		if (!function_exists('curl_init')) {
			return null;
		}

		// load facebook classes
		JLoader::register('Facebook', ZOO_ADMIN_PATH.'/libraries/facebook/facebook.php');

		$access_token = null;
		if (isset($_SESSION['facebook_access_token'])) {
			$access_token = $_SESSION['facebook_access_token'];
		}

		// Build FacebookOAuth object with client credentials.
		return new Facebook(array('app_id' => $params->get('facebook_app_id'), 'app_secret' => $params->get('facebook_app_secret'), 'access_token' => $access_token));

	}
	
	public static function getFacebookFields($fb_uid, $fields = null) {
		try {

			$connection = self::getFacebookClient();
			if ($connection) {

				$infos = $connection->getProfile($fb_uid);

				if (is_object($infos)) {
					if (is_array($fields)) {
						return array_intersect_key((array)$infos, array_flip($fields));
					} else {
						return (array)$infos;
					}
				}
			}

		} catch (Exception $e) {}
	}

	public static function facebookLogout() {
		// remove access token from session
		$_SESSION['facebook_access_token'] = null;
	}
	
	public static function getTwitterClient() {
						
		// get comment params
		$params = new YParameter();
		$params->loadArray(Zoo::getApplication()->getParams()->get('global.comments.'));		
		
		if (!function_exists('curl_init')) {
			return null;
		}
		
		// load twitter classes
		JLoader::register('TwitterOAuth', ZOO_ADMIN_PATH.'/libraries/twitter/twitteroauth.php');
		
		$oauth_token = null;
		$oauth_token_secret = null;
		if (isset($_SESSION['twitter_oauth_token']) && isset($_SESSION['twitter_oauth_token_secret'])) {
			$oauth_token = $_SESSION['twitter_oauth_token'];
			$oauth_token_secret = $_SESSION['twitter_oauth_token_secret'];
		}

		// Build TwitterOAuth object with client credentials.
		return new TwitterOAuth($params->get('twitter_consumer_key'), $params->get('twitter_consumer_secret'), $oauth_token, $oauth_token_secret);

	}

	public static function getTwitterFields($t_uid, $fields = null) {
		try {
				
			$connection = self::getTwitterClient();
			if ($connection) {
				$infos = $connection->get('users/show.json?user_id='.$t_uid);

				if (is_object($infos)) {
					if (is_array($fields)) { 
						return array_intersect_key((array)$infos, array_flip($fields));
					} else {
						return (array)$infos;
					}
				}				
			}
			
		} catch (Exception $e) {}
	}
	
	public static function twitterLogout() {
		// remove access token from session
		$_SESSION['twitter_oauth_token'] = null;
		$_SESSION['twitter_oauth_token_secret'] = null;		
	}
		
}

/*
	Class: CommentHelperException
*/
class CommentHelperException extends YException {}