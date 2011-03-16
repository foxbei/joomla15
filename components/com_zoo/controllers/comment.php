<?php
/**
* @package   ZOO Component
* @file      comment.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: CommentController
		The controller class for comments
*/
class CommentController extends YController {

    /*
       Variable: author
         Active author.
    */
	public $author;

 	/*
		Function: Constructor

		Parameters:
			$default - Array

		Returns:
			DefaultController
	*/
	public function __construct($default = array()) {
		parent::__construct($default);

		// get comment params
		$this->params = new YParameter();
		$this->params->loadArray(Zoo::getApplication()->getParams()->get('global.comments.'));

	}

	public function save() {
		
		// check for request forgeries
		YRequest::checkToken() or jexit('Invalid Token');

		// set currently active author
		$this->author = CommentHelper::activeAuthor();	
		
		// init vars
		$redirect = YRequest::getString('redirect');		
		$login 	  = YRequest::getString(CommentHelper::COOKIE_PREFIX.'login', '', 'cookie');

		if ($this->author->getUserType() == $login) {
			
			if ($this->params->get('enable_comments', false)) {
			
				// init vars
				$content   = YRequest::getVar('content', null, '', 'string', JREQUEST_ALLOWRAW);
				$item_id   = YRequest::getInt('item_id', 0);
				$parent_id = YRequest::getInt('parent_id', 0);
		
				// filter content
				$content = CommentHelper::filterContentInput($content);
		
				// set content in session
				$this->session->set('com_zoo.comment.content', $content);
		
				// set author name, email and url, if author is guest
				if ($this->author->isGuest()) {

					$this->author->name  = YRequest::getString('author');
					$this->author->email = YRequest::getString('email');
					$this->author->url   = YRequest::getString('url');
					
					// save cookies
					CommentHelper::saveCookies($this->author->name, $this->author->email, $this->author->url);					
					
				}				
	
				try {
		
					// get comment table
					$table = YTable::getInstance('comment');
		
					// get parent
					$parent    = $table->get($parent_id);
					$parent_id = ($parent && $parent->item_id == $item_id) ? $parent->id : 0;
					
					// create comment
					$comment = new Comment();
					$comment->parent_id = $parent_id;
					$comment->item_id = $item_id;
					$comment->ip = CommentHelper::getClientIP();
					$comment->created = JFactory::getDate()->toMySQL();
					$comment->content = $content;
					$comment->state = Comment::STATE_UNAPPROVED;

					// auto approve comment
					$approved = $this->params->get('approved', 0);
					if ($this->author->isJoomlaAdmin()) {
						$comment->state = Comment::STATE_APPROVED;
					} else if ($approved == 1) {
						$comment->state = Comment::STATE_APPROVED;
					} else if ($approved == 2 && $table->getApprovedCommentCount($this->author)) {
						$comment->state = Comment::STATE_APPROVED;
					}			

					// bind Author
					$comment->bindAuthor($this->author);

					// validate comment, if not an administrator
					if (!$this->author->isJoomlaAdmin()) {
						$this->_validate($comment);
					}

					// save comment		
					$table->save($comment);	
		
					// remove content from session, if comment was saved
					$this->session->set('com_zoo.comment.content', '');

				} catch (CommentControllerException $e) {

					// raise warning on exception
					JError::raiseWarning(0, (string) $e);

				} catch (YException $e) {
		
					// raise warning on exception
					JError::raiseWarning(0, JText::_('ERROR_SAVING_COMMENT'));
		
					// add exception details, for super administrators only
					if ($this->user->superadmin) {
						JError::raiseWarning(0, (string) $e);
					}
		
				}
		
				// add anchor to redirect, if comment was saved
				if ($comment->id) {
					$redirect .= '#comment-'.$comment->id;
				}
			
			} else {
				// raise warning on comments not enabled
				JError::raiseWarning(0, JText::_('Comments are not enabled.'));
			}
		} else {
						
			// raise warning on exception
			JError::raiseWarning(0, JText::_('ERROR_SAVING_COMMENT'));
			
			// add exception details, for super administrators only
			if ($this->user->superadmin) {
				JError::raiseWarning(0, JText::_('User types didn\'t match.'));
			}
		}

		$this->setRedirect($redirect);
	}
	
	protected function _validate($comment) {

		// get params
		$require_author 		 = $this->params->get('require_name_and_mail', 0);
		$registered     		 = $this->params->get('registered_users_only', 0);
		$time_between_user_posts = $this->params->get('time_between_user_posts', 120);
		$blacklist      		 = $this->params->get('blacklist', '');

		// check if related item exists
		if (YTable::getInstance('item')->get($comment->item_id) === null) {
			throw new CommentControllerException('Related item does not exists.');
		}

		// check if content is empty
		if (empty($comment->content)) {
			throw new CommentControllerException('Please enter a comment.');
		}
		
		// only registered users can comment
		if ($registered && $this->author->isGuest()) {
			throw new CommentControllerException('LOGIN_TO_LEAVE_OMMENT');
		}

		// validate required name/email
		if ($this->author->isGuest() && $require_author && (empty($comment->author) || empty($comment->email))) {
			throw new CommentControllerException('Please enter the required fields author and email.');
		}

		// validate email format
		if (!empty($comment->email) && !CommentHelper::validateEmail($comment->email)) {
			throw new CommentControllerException('Please enter a valid email address.');
		}
		
		// validate url format
		if (!empty($comment->url) && !CommentHelper::validateURL($comment->url)) {
			throw new CommentControllerException('Please enter a valid website link.');
		}
	
		// check quick multiple posts
		if ($last = YTable::getInstance('comment')->getLastComment($comment->ip, $this->author)) {
			if (JFactory::getDate($comment->created)->toUnix() < JFactory::getDate($last->created)->toUnix() + $time_between_user_posts) {
				throw new CommentControllerException('You are posting comments too quickly. Slow down a bit.');
			}
		}

		// check against spam blacklist
		if (CommentHelper::matchWords($comment, $blacklist) && $comment->state != Comment::STATE_SPAM) {
			$comment->state = Comment::STATE_SPAM;
		}

		// check comment for spam (akismet)
		if ($this->params->get('akismet_enable', 0) && $comment->state != Comment::STATE_SPAM) {
			try {		

				CommentHelper::akismet($comment, $this->params->get('akismet_api_key'));

			} catch (Exception $e) {

				// re-throw exception, for super administrators only
				if ($this->user->superadmin) throw new YException($e->getMessage());

			}
		}

		// check comment for spam (mollom)
		if ($this->params->get('mollom_enable', 0) && $comment->state != Comment::STATE_SPAM) {
			try {		

				CommentHelper::mollom($comment, $this->params->get('mollom_public_key'), $this->params->get('mollom_private_key'));

			} catch (Exception $e) {
				
				// re-throw exception, for super administrators only
				if ($this->user->superadmin) throw new YException($e->getMessage());
				
			}
		}
		
	}

	public function facebookConnect() {

		// init vars
		$item_id = YRequest::getInt('item_id', 0);
		$item    = YTable::getInstance('item')->get($item_id);
		
		// get facebook client
		$connection = CommentHelper::getFacebookClient();
		
		if ($connection && empty($connection->access_token)) {

			$uri	  = new JURI();
			$redirect = $uri->root() .$this->link_base.'&controller=comment&task=facebookauthenticate&item_id='.$item_id;
			$redirect = $connection->getAuthenticateURL($redirect);

		} else {

			// already connected
			$redirect = JRoute::_(RouteHelper::getItemRoute($item));
			
		}
		
		$this->setRedirect($redirect);

	}

	public function facebookAuthenticate() {

		// init vars
		$item_id = YRequest::getInt('item_id', 0);
		$item    = YTable::getInstance('item')->get($item_id);

		// get facebook client
		$connection = CommentHelper::getFacebookClient();

		if ($connection) {
			$code = YRequest::getString('code', '');
			$uri	  = new JURI();
			$redirect = $uri->root() .$this->link_base.'&controller=comment&task=facebookauthenticate&item_id='.$item_id;
			$url  = $connection->getAccessTokenURL($code, $redirect);

			$http = new HttpHelper();
			$result = $http->get($url, array('ssl_verifypeer' => false));
			$token = str_replace('access_token=', '', $result['body']);
			$_SESSION['facebook_access_token'] = $token;
		}

		$redirect = JRoute::_(RouteHelper::getItemRoute($item));
		$this->setRedirect($redirect);
	}

	public function facebookLogout() {
		CommentHelper::facebookLogout();
		$this->setRedirect(YRequest::getString('HTTP_REFERER', '', 'server'));
	}

	public function twitterConnect() {

		// get twitter client
		$connection = CommentHelper::getTwitterClient();

		// redirect to the referer after authorize/login procedure
		$referer = YRequest::getString('HTTP_REFERER', '', 'server');

		// retrieve request token only if token is not supplied already
		if ($connection && empty($connection->token)) {

			$uri = new JURI();
			$redirect = $uri->root() .$this->link_base.'&app_id='.Zoo::getApplication()->id.'&controller='.$this->controller.'&task=twitterauthenticate&referer='.urlencode($referer);

			// get temporary credentials		
			$request_token = $connection->getRequestToken($redirect);

			// save temporary credentials to session
			$_SESSION['twitter_oauth_token'] = $token = $request_token['oauth_token'];
			$_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

			// if last connection failed don't display authorization link
			switch ($connection->http_code) {
			  case 200:
			    // build authorize URL and redirect user to Twitter
			    $redirect = $connection->getAuthorizeURL($token);
			    break;
			  default:
			    // show notification if something went wrong.
				JError::raiseWarning(0, JText::_('ERROR_CONNECT_TWITTER'));

				$redirect = $referer;
			}
		} else {
			// already connected
			$redirect = $referer;
		}
		
		$this->setRedirect($redirect);		    

	}
	
	public function twitterAuthenticate() {
		
		// get twitter client
		$connection = CommentHelper::getTwitterClient();
		
		if ($connection) {
			// retrieve access token
			$token_credentials = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			
			// replace request token with access token in session.
			if ($token_credentials) {
				$_SESSION['twitter_oauth_token'] = $token_credentials['oauth_token'];
				$_SESSION['twitter_oauth_token_secret'] = $token_credentials['oauth_token_secret'];
			} else {
				// show notification if something went wrong.
				JError::raiseWarning(0, JText::_('ERROR_CONNECT_TWITTER'));
			}
		}
		
		$this->setRedirect(YRequest::getString('referer'));		
	}
	
	public function twitterLogout() {
		CommentHelper::twitterLogout();		
		$this->setRedirect(YRequest::getString('HTTP_REFERER', '', 'server'));		
	}	
	
}

/*
	Class: CommentControllerException
*/
class CommentControllerException extends YException {}