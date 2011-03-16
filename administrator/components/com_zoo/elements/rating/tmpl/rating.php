<?php
/**
* @package   ZOO Component
* @file      rating.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets js/css
JHTML::script('rating.js', 'administrator/components/com_zoo/elements/rating/assets/js/');
JHTML::stylesheet('rating.css', 'administrator/components/com_zoo/elements/rating/assets/css/');

$id = 'rating-' . $instance;

?>
<div id="<?php echo $id; ?>" class="yoo-zoo rating">

	<div class="rating-container star<?php echo $stars; ?>">
		<div class="previous-rating" style="width: <?php echo intval($rating / $stars * 100); ?>%;"></div>
		
		<?php if (!$disabled) : ?>
		<div class="current-rating">
		
			<?php for($i = $stars; $i > 0; $i--) : ?>
			<div class="stars star<?php echo $i; ?>" title="<?php echo $i.' '.JText::_('out of').' '.$stars; ?>"></div>
			<?php endfor ?>
			
		</div>
		<?php endif; ?>
	</div>
	
	<?php if ($show_message) : ?>
	<div class="vote-message">
		<?php echo $rating.'/<strong>'.$stars.'</strong> '.JText::sprintf('rating (%s votes)', $votes); ?>
	</div>
	<?php endif; ?>
</div>
<?php if (!$disabled) : ?>
	<script type="text/javascript">
		jQuery(function($){
			$('#<?php echo $id; ?>').ElementRating({ url: '<?php echo JRoute::_($link, false); ?>' });
		});
	</script>
<?php endif; ?>