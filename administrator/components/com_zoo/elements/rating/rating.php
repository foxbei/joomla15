<?php
/**
* @package   ZOO Component
* @file      rating.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: ElementRating
		The rating element class
*/
class ElementRating extends Element {

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// set callbacks
		$this->registerCallback('vote');
		$this->registerCallback('reset');
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return true;
	}
	
	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		
		static $instance;
		
		// init vars
		$stars      = $this->_config->get('stars');
		$allow_vote = $this->_config->get('allow_vote');		
		
		$disabled     = isset($params['rating_disabled']) ? $params['rating_disabled'] : false;
		$show_message = isset($params['show_message']) ? $params['show_message'] : false;
		
		// init vars
		$instance = empty($instance) ? 1 : $instance + 1;
		$link     = 'index.php?option=com_zoo&task=callelement&format=raw&item_id='.$this->_item->id.'&element='.$this->identifier;
		
		// render layout
		if ($layout = $this->getLayout()) {
			return self::renderLayout($layout, array('instance' => $instance, 'stars' => $stars, 'allow_vote' => $allow_vote, 'disabled' => $disabled, 'show_message' => $show_message, 'rating' => $this->getRating(), 'votes' => (int) $this->_data->get('votes', 0), 'link' => $link));
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
				
		// create html
		$html  = '<div id="'.$this->identifier.'">';
		$html .= '<table>';
		$html .= JHTML::_('element.editrow', JText::_('Rating'), $this->getRating());
		$html .= JHTML::_('element.editrow', JText::_('Votes'), (int) $this->_data->get('votes', 0));
		$html .= '</table>';
				
		$javascript = '';
		if ($this->_data->get('votes', 0)) {
			
			$option     = JRequest::getCmd('option');
			$controller = JRequest::getWord('controller');
			
			$html .= '<input name="reset-rating" type="button" class="button" value="'.JText::_('Reset').'"/>';
			
			// create js
			$javascript  = "jQuery('#$this->identifier').EditElementRating({ url: '".JRoute::_('index.php?option='.$option.'&controller='.$controller.'&format=raw&type='.$this->getType()->identifier.'&elm_id='.$this->identifier.'&item_id='.$this->getItem()->id, false)."' });";
			$javascript  = "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";
			
		}
		
		$html .= '</div>';
		
		return $html.$javascript;

	}	

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		JHTML::script('rating.js', 'administrator/components/com_zoo/elements/rating/assets/js/');

		return $this;
	}	
	
	public function reset() {
		
		$db = YDatabase::getInstance();
		
		$query = 'DELETE'
				    .' FROM ' . ZOO_TABLE_RATING
			   	    .' WHERE item_id = '.(int) $this->getItem()->id;
			
		$db->query($query);
		
		$this->_data->set('votes', 0);
		$this->_data->set('value', 0);
		
		//save item
		YTable::getInstance('item')->save($this->getItem());		
		
		return $this->edit();
	}
	
	/*
		Function: rating
			Get rating.

		Returns:
			String - Rating number
	*/
	public function getRating() {
		return number_format((double) $this->_data->get('value', 0), 1);
	}

	/*
		Function: vote
			Execute vote.

		Returns:
			String - Message
	*/
	public function vote($vote = null) {

		// init vars
		$max_stars  = $this->_config->get('stars');
		$allow_vote = $this->_config->get('allow_vote');

		$db   = YDatabase::getInstance();
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		$vote = (int) $vote;

		for ($i = 1; $i <= $max_stars; $i++) { 
			$stars[] = $i;
		}

		if ($allow_vote > $user->get('aid', 0)) {
			return json_encode(array(
				'value' => 0,
				'message' => JText::_('NOT_ALLOWED_TO_VOTE')
			));
		}

		if (in_array($vote, $stars) && isset($_SERVER['REMOTE_ADDR']) && ($ip = $_SERVER['REMOTE_ADDR'])) {

			// check if ip already exists
			$query = 'SELECT *'
				    .' FROM ' . ZOO_TABLE_RATING
			   	    .' WHERE element_id = '.$db->Quote($this->identifier)
			   	    .' AND item_id = '.(int) $this->_item->id
			   	    .' AND ip = '.$db->Quote($ip);
			
			$db->query($query);

			// voted already
			if ($db->getNumRows()) {
				return json_encode(array(
					'value' => 0,
					'message' => JText::_("You've already voted")
				));
			}

			// insert vote
			$query    = "INSERT INTO " . ZOO_TABLE_RATING
	   	               ." SET element_id = ".$db->Quote($this->identifier)
			   	       ." ,item_id = ".(int) $this->_item->id
		   	           ." ,user_id = ".(int) $user->id
		   	           ." ,value = ".(int) $vote
	   	               ." ,ip = ".$db->Quote($ip)
   	                   ." ,created = ".$db->Quote($date->toMySQL());

			// execute query
			$db->query($query);
			
			// calculate rating/votes
			$query = 'SELECT AVG(value) AS rating, COUNT(id) AS votes'
				    .' FROM ' . ZOO_TABLE_RATING
				   	.' WHERE element_id = '.$db->Quote($this->identifier)			
				    .' AND item_id = '.$this->_item->id
				    .' GROUP BY item_id';
	
			if ($res = $db->queryAssoc($query)) {
				$this->_data->set('votes', $res['votes']);
				$this->_data->set('value', $res['rating']);
			} else {
				$this->_data->set('votes', 0);
				$this->_data->set('value', 0);
			}
		}

		//save item
		YTable::getInstance('item')->save($this->getItem());

		return json_encode(array(
			'value' => intval($this->getRating() / $max_stars * 100),
			'message' => sprintf(JText::_('%s rating from %s votes'), $this->getRating(), $this->_data->get('votes'))
		));
	}
	
}

class ElementRatingData extends ElementData{

	public function encodeData() {		

		if ($this->_element->getItem()) {
			$db   = YDatabase::getInstance();
			
			// calculate rating/votes
			$query = 'SELECT AVG(value) AS rating, COUNT(id) AS votes'
				    .' FROM ' . ZOO_TABLE_RATING
				   	.' WHERE element_id = '.$db->Quote($this->_element->identifier)			
				    .' AND item_id = '.$this->_element->getItem()->id
				    .' GROUP BY item_id';
	
			if ($res = $db->queryAssoc($query)) {
				$this->set('votes', $res['votes']);
				$this->set('value', $res['rating']);	
			} else {
				$this->set('votes', 0);
				$this->set('value', 0);				
			}		
		}
		return parent::encodeData();
	}
	
}