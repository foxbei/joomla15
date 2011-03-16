<?php 
/**
* @package   ZOO Component
* @file      default.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

// add js
JHTML::script('comment.js', ZOO_ADMIN_URI.'assets/js/');

?>

<form id="comments-default" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<ul class="filter">
		<li class="filter-left">
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" />
			<button onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
		</li>
		<li class="filter-right">
			<?php echo $this->lists['select_author'];?>
		</li>		
		<li class="filter-right">
			<?php echo $this->lists['select_item'];?>
		</li>		
		<li class="filter-right">
			<?php echo $this->lists['select_state'];?>
		</li>		
	</ul>
	
	<?php  if($this->pagination->total > 0) : ?>

	<table id="actionlist" class="list stripe">
	    <thead>
	        <tr>
	            <th class="checkbox">
					<input type="checkbox" class="check-all" />
	            </th>
	            <th class="author">
					<?php echo JText::_('Author'); ?>
	            </th>
	            <th class="comment">
					<?php echo JText::_('Comment'); ?>
	            </th>
	            <th class="comment-on">
	                <?php echo JText::_('Comment On'); ?>
	            </th>
	        </tr>
	    </thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>    
	    <tbody id="table-body">
	    	<?php 
				foreach ($this->comments as $comment) {
					$this->comment = $comment;
					echo $this->partial('row');
				}
			?>
	    </tbody>
	</table>

	<?php elseif($this->is_filtered) :

			$title   = JText::_('FILTER_NO_COMMENTS').'!';
			$message = null;
			echo $this->partial('message', compact('title', 'message'));

		else : 

			$title   = JText::_('NO_COMMENTS_YET').'!';
			$message = JText::_('COMMENTS_MANAGER_DESCRIPTION');
			echo $this->partial('message', compact('title', 'message'));
		
		endif;
	?>

</div>	

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($){
		$('#comments-default').Comment();
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>