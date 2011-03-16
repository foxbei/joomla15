<?php
/**
* @package   ZOO Component
* @file      itemcommentslink.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementItemCommentsLink
		The item comments link element class
*/
class ElementItemCommentsLink extends Element {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return $this->_item && $this->_item->getApplication()->isCommentsEnabled() && ($this->_item->isCommentsEnabled() || $this->_item->getCommentsCount(1));
	}
	
	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
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

		if (!empty($this->_item)) {
			$comment_count = $this->_item->getCommentsCount(1);
	
			if ($comment_count == 0) {
				$text = isset($params['no_comments_text']) ? $params['no_comments_text'] : JText::_('No comments');
			} else if ($comment_count == 1) {
				$text = sprintf((isset($params['single_comment_text']) ? $params['single_comment_text'] : JText::_('%s comment')), 1);
			} else {
				$text = sprintf((isset($params['multiple_comments_text']) ? $params['multiple_comments_text'] : JText::_('%s comments')), $comment_count);
			}

            if ($this->_item->getState()) {

                $url  = RouteHelper::getItemRoute($this->_item).'#comments';

                return '<a href="' . JRoute::_($url). '">' . $text . '</a>';

            } else {

                return $text;

            }

		}

	}
	
}