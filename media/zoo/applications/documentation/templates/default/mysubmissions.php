<?php
/**
* @package   ZOO Component
* @file      mysubmissions.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css/js
if (strtolower(substr($GLOBALS['mainframe']->getTemplate(), 0, 3)) != 'yoo') {
	JHTML::stylesheet('reset.css', 'media/zoo/assets/css/');
}
JHTML::stylesheet('zoo.css', $this->template->getURI().'/assets/css/');

// include syntaxhighlighter
JHTML::script('jquery.beautyOfCode.js', $this->template->getURI().'/assets/js/');

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->submission->alias; ?>">

	<div class="mysubmissions">

		<h1 class="headline"><?php echo JText::_('My Submissions'); ?></h1>
		
		<p><?php echo sprintf(JText::_('Hi %s, here you can edit your submissions and add new submission.'), $this->user->name); ?></p>
		
		<?php
		
			echo $this->partial('mysubmissions');
		
		?>

	</div>

	<script type="text/javascript">
		jQuery(function($) {
			$.beautyOfCode.init({
			  theme: 'Default',
			  brushes: ['Vb', 'Sql', 'Scala', 'Ruby', 'Python', 'Perl', 'JavaFX', 'Java', 'Erlang', 'Css', 'Cpp', 'Xml', 'JScript', 'CSharp', 'Plain', 'Php']
			});
		});
	</script>

</div>