<?php
/**
* @package   yoo_scoop Template
* @file      default_graph.php
* @version   5.5.3 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<fieldset>
	<legend><?php echo $this->escape($this->poll->title); ?></legend>

	<table cellspacing="0" cellpadding="0" border="0">
	<?php foreach($this->votes as $vote) : ?>
		<tr>
			<td width="100%" colspan="3">
				<?php echo $vote->text; ?>
			</td>
		</tr>
		<tr>
			<td align="right" width="25">
				<strong><?php echo $this->escape($vote->hits); ?></strong>
			</td>
			<td width="30" >
				<?php echo $this->escape($vote->percent); ?>%
			</td>
			<td width="300" >
				<div class="<?php echo $vote->class; ?>" style="height:<?php echo $vote->barheight; ?>px;width:<?php echo $vote->percent; ?>%"></div>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	
</fieldset>

<?php echo JText::_( 'Number of Voters' ); ?>: <?php if(isset($this->votes[0])) echo $this->votes[0]->voters; ?>
<br /><?php echo JText::_( 'First Vote' ); ?>: <?php echo $this->escape($this->first_vote); ?>
<br /><?php echo JText::_( 'Last Vote' ); ?>: <?php echo $this->escape($this->last_vote); ?>