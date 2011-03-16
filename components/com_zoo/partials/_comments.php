<?php
/**
* @package   ZOO Component
* @file      _comments.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js and css
JHTML::script('comment.js', 'media/zoo/assets/js/');
JHTML::stylesheet('comments.css', 'media/zoo/assets/css/');

// css classes
$css[] = $params->get('max_depth', 5) > 1 ? 'nested' : null;
$css[] = $params->get('registered_users_only') && $active_author->isGuest() ? 'no-response' : null;

?>

<div id="comments">
	<div class="comments <?php echo implode("\n", $css); ?>">
	
		<h3 class="comments-meta">
			<span class="comments-count"><?php echo JText::_('Comments').' ('.$params->get('count').')'; ?></span>
		</h3>
	
		<?php 
			echo $comments; 		 
		 	if($item->isCommentsEnabled()) :
				echo $this->partial('respond', compact('active_author', 'params', 'item')); 
			endif; 
		?>
		
	</div>
</div>

<script type="text/javascript">
	new Zoo.Comment({ cookiePrefix: '<?php echo CommentHelper::COOKIE_PREFIX; ?>', cookieLifetime: '<?php echo CommentHelper::COOKIE_LIFETIME; ?>' });
</script>