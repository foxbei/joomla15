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

?>

<form id="submissions-default" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<?php if(count($this->submissions) > 0) : ?>

	<table class="list stripe">
		<thead>
			<tr>
				<th class="checkbox">
					<input type="checkbox" class="check-all" />
				</th>
				<th class="name" colspan="2">
					<?php echo JHTML::_('grid.sort', 'Name', 'name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="types">
					<?php echo JText::_('Submittable Types'); ?>
				</th>
				<th class="trusted">
					<?php echo JText::_('Trusted Mode'); ?>
				</th>
				<th class="published">
				    <?php echo JHTML::_('grid.sort', 'Published', 'state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="access">
					<?php echo JHTML::_('grid.sort', 'Access', 'access', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$table = YTable::getInstance('submission');
			for ($i=0, $n=count($this->submissions); $i < $n; $i++) :
				
				$row = $this->submissions[$i];

				$checked = JHTML::_('grid.id', $i, $row->id);

				if ($row->state == 1) {
				    $text = JText::_('Unpublish submission');
					$img = 'tick.png';
					$alt = JText::_( 'Published' );
				} else if ($row->state == 0) {
				    $text = JText::_('Publish submission');
					$img = 'publish_x.png';
					$alt = JText::_( 'Unpublished' );
				}

				$types = array();
				foreach ($row->getSubmittableTypes() as $type) {
				    $types[] = $type->name;
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

				// trusted mode
				$trusted_mode     = (int) $row->isInTrustedMode();
				$trusted_mode_img = $trusted_mode ? 'tick.png' : 'publish_x.png';
				$trusted_mode_alt = $trusted_mode ? JText::_('Trusted Mode enabled') : JText::_('Trusted Mode disabled');

				?>
				<tr>
				    <td class="checkbox">
						<?php echo $checked; ?>
					</td>
				    <td class="icon"></td>
				    <td class="name">
						<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Submission');?>::<?php echo $row->name; ?>">
							<a href="<?php echo JRoute::_($this->baseurl.'&task=edit&cid[]='.$row->id);  ?>"><?php echo $row->name; ?></a>
						</span>
					</td>
					<td class="types">
						<?php if (count($types)) : ?>
				        <?php echo implode(', ', $types); ?>
						<?php else: ?>
						<span><?php echo JText::_('You will need at least one submittable type for this submission to work!'); ?></span>
						<?php endif; ?>
				    </td>
					<td class="trusted">
						<?php if ($row->access != 0) : ?>
						<a href="javascript:void(0);" title="<?php echo JText::_('Enable/Disable Trusted Mode');?>" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $trusted_mode ? 'disabletrustedmode' : 'enabletrustedmode' ?>')">
						<?php endif; ?>
							<img src="images/<?php echo $trusted_mode_img;?>" width="16" height="16" border="0" alt="<?php echo $trusted_mode_alt; ?>" />
						<?php if ($row->access != 0) : ?>
						</a>
						<?php endif; ?>
					</td>
					<td class="published">
						<span class="editlinktip hasTip" title="<?php echo $text;?>">
							<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')">
								<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
							</a>
				        </span>
					</td>
					<td class="access">
						<?php echo $access;?>
					</td>
				</tr>
			<?php endfor; ?>
		</tbody>
	</table>

	<?php else : 
	
			$title   = JText::_('NO_SUBMISSIONS_YET').'!';
			$message = JText::_('SUBMISSION_MANAGER_DESCRIPTION');
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

<?php echo ZOO_COPYRIGHT; ?>