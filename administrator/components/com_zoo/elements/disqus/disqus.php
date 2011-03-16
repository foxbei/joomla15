<?php
/**
* @package   ZOO Component
* @file      disqus.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementDisqus
       The Disqus element class (http://www.disqus.com)
*/
class ElementDisqus extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$value = $this->_data->get('value');
		$website   = $this->_config->get('website');
		return !empty($value) && !empty($website);
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

		// init vars
		$website   = $this->_config->get('website');
		$developer = $this->_config->get('developer');
		
		// render html
		if ($website && $this->_data->get('value')) {

			// developer mode
			if ($developer) {
				$html[] = "<script type='text/javascript'>";
				$html[] = "var disqus_developer = 1;";
				$html[] = "</script>";			
			}

			$html[] = "<div id=\"disqus_thread\"></div>";
			$html[] = "<script type=\"text/javascript\" src=\"http://disqus.com/forums/$website/embed.js\"></script>";
			$html[] = "<noscript><a href=\"http://$website.disqus.com/?url=ref\">View the discussion thread.</a></noscript>";
			$html[] = "<a href=\"http://disqus.com\" class=\"dsq-brlink\">blog comments powered by <span class=\"logo-disqus\">Disqus</span></a>";
			$html[] = "<script type=\"text/javascript\">
							//<![CDATA[
							(function() {
									var links = document.getElementsByTagName('a');
									var query = '?';
									for(var i = 0; i < links.length; i++) {
										if(links[i].href.indexOf('#disqus_thread') >= 0) {
											query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
										}
									}
									document.write('<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://disqus.com/forums/$website/get_num_replies.js' + query + '\"></' + 'script>');
								})();
							//]]>
                       </script>";
			return implode("\n", $html);
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

		// init vars
		$default = $this->_config->get('default');
		
		if ($default != '' && $this->_item != null && $this->_item->id == 0) {
			$this->_data->set('value', 1);
		}

		return JHTML::_('select.booleanlist', 'elements[' . $this->identifier . '][value]', '', $this->_data->get('value'));
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		return array('value' => $value->get('value'));
	}

}