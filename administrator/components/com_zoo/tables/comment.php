<?php
/**
* @package   ZOO Component
* @file      comment.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: CommentTable
      The Table Class for comments.
*/
class CommentTable extends YTable {

	protected function __construct() {
		parent::__construct('Comment', ZOO_TABLE_COMMENT, 'id');
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {
	
		// auto update all comments of a joomla user, if name/email changed
		if ($object->user_id 
			&& ($row = $this->first(array('conditions' => array('user_id = ?', $object->user_id)))) 
			&& ($row->author != $object->author || $row->email != $object->email)) {

			// get database
			$db = $this->getDBO();		
		
			$query = "UPDATE ".$this->getTableName()
				." SET author = ".$db->quote($object->author).", email = ".$db->quote($object->email)
				." WHERE user_id = ".$object->user_id;
			$db->query($query);
		}
				
		return parent::save($object);
	}

	/*
		Function: getCommentsForItem
			Retrieve comments by item id

		Parameters:
			$item_id - Item id

		Returns:
			Array
	*/
	public function getCommentsForItem($item_id, $order = 'ASC', CommentAuthor $author = null) {

		// set query options
		$order = 'created '.($order == 'ASC' ? 'ASC' : 'DESC');

		if ($author) {
			$conditions = array("item_id = ? AND (state = ? OR (author = '?' AND email = '?' AND user_id = '?' AND user_type = '?'))", $item_id, Comment::STATE_APPROVED, $author->name, $author->email, $author->user_id, $author->getUserType());
		} else {
			$conditions = array("item_id = ? AND state = ?", $item_id, Comment::STATE_APPROVED);
		}
		
		return $this->all(compact('conditions', 'order'));
	}

	/*
		Function: getLastComment
			Retrieve last comment by ip address and author (optional)
											
		Parameters:
			$ip - IP address
			$author - The author
										
		Returns:
			Comment
	*/		
	public function getLastComment($ip, CommentAuthor $author = null) {

		// set query options
		$order = 'created DESC';

		if ($author && !$author->isGuest()) {
			$conditions = array("ip = '?' AND user_id = '?' AND user_type = '?'", $ip, $author->user_id, $author->getUserType());
		} else {
			$conditions = array("ip = '?'", $ip);
		}
	
		return $this->first(compact('conditions', 'order'));
	}

	/*
		Function: getApprovedCommentCount
			Retrieve approved comments by author
											
		Parameters:
			$author - Author
										
		Returns:
			Int
	*/		
	public function getApprovedCommentCount(CommentAuthor $author) {

		// set query options
		if ($author && !$author->isGuest()) {
			$conditions = array("state = ? AND user_id = '?' AND user_type = '?'", Comment::STATE_APPROVED, $author->user_id, $author->getUserType());
		} else {
			$conditions = array("state = ? AND user_id = '0' AND author = '?' AND email = '?'", Comment::STATE_APPROVED, $author->name, $author->email);
		}
		
		return $this->count(compact('conditions'));
	}
	
	/*
		Function: delete
			delete comment with id <$comment_id>
														
		Parameters:
			$object - comment object
										
		Returns:
			true, if comment is deleted
			false, otherwise
	*/	
	public function delete($object) {		
		
		// get database
		$db = $this->getDBO();		

		$old_parent = $object->id;
		$new_parent = $object->parent_id;
		
		parent::delete($object);
		
		$query = "UPDATE ".$this->getTableName()
			." SET parent_id = ".$new_parent
			." WHERE parent_id = ".$old_parent;
		return $db->query($query);
	}

}

/*
	Class: CommentTableException
*/
class CommentTableException extends YException {}