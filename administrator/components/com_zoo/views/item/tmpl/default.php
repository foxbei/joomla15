<?php 
/**
* @package   ZOO Component
* @file      default.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// add js
JHTML::script('item.js', 'administrator/components/com_zoo/assets/js/');

// init vars
$db		= JFactory::getDBO();
$user	= JFactory::getUser();
$config	= JFactory::getConfig();
$now	= JFactory::getDate();

?>

<form id="items-default" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<?php if ($this->is_filtered || $this->pagination->total > 0) :?>

		<ul class="filter">
			<li class="filter-left">
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" />
				<button onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
			</li>
			<li class="filter-right">
				<?php echo $this->lists['select_category'];?>
			</li>
			<li class="filter-right">
				<?php echo $this->lists['select_type'];?>
			</li>
			<li class="filter-right">
				<?php echo $this->lists['select_author'];?>
			</li>
		</ul>

	<?php endif;
	
	if($this->pagination->total > 0) : ?>

		<table class="list stripe">
			<thead>
				<tr>
					<th class="checkbox">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					</th>
					<th class="name" colspan="2">
						<?php echo JHTML::_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="type">
						<?php echo JHTML::_('grid.sort', 'Type', 'a.type', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="published">
						<?php echo JHTML::_('grid.sort', 'Published', 'a.state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="searchable">
						<?php echo JText::_('Searchable'); ?>
					</th>
					<th class="comments">
						<?php echo JText::_('Comments'); ?>
					</th>						
					<th class="priority">
						<?php echo JHTML::_('grid.sort', 'Order Priority', 'a.priority', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="access">
						<?php echo JHTML::_('grid.sort', 'Access', 'a.access', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="author">
						<?php echo JHTML::_('grid.sort', 'Author', 'a.created_by', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="date">
						<?php echo JHTML::_('grid.sort', 'Date', 'a.created', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
					<th class="hits">
						<?php echo JHTML::_('grid.sort', 'Hits', 'a.hits', @$this->lists['order_Dir'], @$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="12">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>	
			<tbody>
			<?php
			$nullDate = $db->getNullDate();
			for ($i=0, $n=count($this->items); $i < $n; $i++) :

				$row     = $this->items[$i];
				$checked = JHTML::_('grid.id', $i, $row->id);
	
				$publish_up = JFactory::getDate($row->publish_up);
				$publish_down = JFactory::getDate($row->publish_down);
				$publish_up->setOffset($config->getValue('config.offset'));
				$publish_down->setOffset($config->getValue('config.offset'));
	
				if ( $now->toUnix() <= $publish_up->toUnix() && $row->state == 1 ) {
					$img = 'publish_y.png';
					$alt = JText::_( 'Published' );
				} else if ( ( $now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate ) && $row->state == 1 ) {
					$img = 'publish_g.png';
					$alt = JText::_( 'Published' );
				} else if ( $now->toUnix() > $publish_down->toUnix() && $row->state == 1 ) {
					$img = 'publish_r.png';
					$alt = JText::_( 'Expired' );
				} else if ( $row->state == 0 ) {
					$img = 'publish_x.png';
					$alt = JText::_( 'Unpublished' );
				} else if ( $row->state == -1 ) {
					$img = 'disabled.png';
					$alt = JText::_( 'Archived' );
				}
						
				if ($row->searchable == 0) {
					$search_img = 'publish_x.png';
					$search_alt = JText::_( 'None searchable' );
				} elseif ($row->searchable == 1) {
					$search_img = 'tick.png';
					$search_alt = JText::_( 'Searchable' );
				}
	
				$comments_enabled = (int) $row->getParams()->get('config.enable_comments', 1);
				$comments_img 	  = $comments_enabled ? 'tick.png' : 'publish_x.png';
				$comments_alt 	  = $comments_enabled ? JText::_('Comments enabled') : JText::_('Comments disabled');					
					
				$times = '';
	
				if (isset($row->publish_up)) {
					if ($row->publish_up == $nullDate) {
						$times .= JText::_( 'Start: Always' );
					} else {
						$times .= JText::_( 'Start' ) .": ". $publish_up->toFormat();
					}
				}
	
				if (isset($row->publish_down)) {
					if ($row->publish_down == $nullDate) {
						$times .= "<br />". JText::_( 'Finish: No Expiry' );
					} else {
						$times .= "<br />". JText::_( 'Finish' ) .": ". $publish_down->toFormat();
					}
				}
	
				// author
				$author = $row->created_by_alias;
				if (!$author) {
					if (isset($this->users[$row->created_by])) {
						$author = $this->users[$row->created_by]->name;

						if ($user->authorize('com_users', 'manage')) {
							$edit_link = 'index.php?option=com_users&task=edit&cid[]='.$row->created_by;
							$author = '<a href="'.JRoute::_($edit_link).'" title="'.JText::_('Edit User').'">'. $author.'</a>';
						}
					} else {
						$author = JText::_('Guest');
					}
				}
	
				// access
				$group_access = isset($this->groups[$row->access]) ? $this->groups[$row->access]->name : '';
	
				if (!$row->access)  {
					$color_access = 'style="color: green;"';
					$task_access  = 'accessregistered';
				} else if ($row->access == 1) {
					$color_access = 'style="color: red;"';
					$task_access  = 'accessspecial';
				} else {
					$color_access = 'style="color: black;"';
					$task_access  = 'accesspublic';
				}
	
				$access = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''.$task_access .'\')" '.$color_access.'>'. JText::_($group_access) .'</a>';
			?>
				<tr>				
					<td class="checkbox">
						<?php echo $checked; ?>
					</td>
					<td class="icon"></td>
					<td class="name">
						<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Item');?>::<?php echo $row->name; ?>">
							<a href="<?php echo JRoute::_($this->baseurl.'&task=edit&cid[]='.$row->id);  ?>"><?php echo $row->name; ?></a>
						</span>
					</td>
					<td class="type">
						<?php echo Zoo::getApplication()->getType($row->type)->name; ?>
					</td>
					<td class="published">
						<span class="editlinktip hasTip" title="<?php echo JText::_('Publish Information');?>::<?php echo $times; ?>"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')">
							<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a></span>
					</td>
					<td class="searchable">
						<a href="javascript:void(0);" title="<?php echo JText::_('Edit searchable state');?>" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->searchable ? 'makenonesearchable' : 'makesearchable' ?>')">
							<img src="images/<?php echo $search_img;?>" width="16" height="16" border="0" alt="<?php echo $search_alt; ?>" />
						</a>
					</td>
					<td class="comments">
						<a href="javascript:void(0);" title="<?php echo JText::_('Enable/Disable comments');?>" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $comments_enabled ? 'disablecomments' : 'enablecomments' ?>')">
							<img src="images/<?php echo $comments_img;?>" width="16" height="16" border="0" alt="<?php echo $comments_alt; ?>" />
						</a>
					</td>										
					<td class="priority">
						<span class="minus"></span>
						<input type="text" class="value" value="<?php echo $row->priority; ?>" size="5" name="priority[<?php echo $row->id; ?>]"/>
						<span class="plus"></span>
					</td>
					<td class="access">
						<?php echo $access;?>
					</td>
					<td class="author">
						<?php echo $author; ?>
					</td>
					<td class="date">
						<?php echo JHTML::_('date',  $row->created, JText::_('DATE_FORMAT_LC4') ); ?>
					</td>
					<td class="hits">
						<?php echo $row->hits ?>
					</td>
				</tr>
				<?php endfor; ?>
			</tbody>
		</table>

	<?php elseif($this->is_filtered) :

			$title   = JText::_('SEARCH_NO_ITEMS').'!';
			$message = null;
			echo $this->partial('message', compact('title', 'message'));

		else : 
	
			$title   = JText::_('NO_ITEMS_YET').'!';
			$message = JText::_('ITEM_MANAGER_DESCRIPTION');
			echo $this->partial('message', compact('title', 'message'));
		
		endif;
	?>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	window.addEvent('domready', function(){
		
		var app = new Zoo.BrowseItems();

	});
</script>

<?php echo ZOO_COPYRIGHT; ?>