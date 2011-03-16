<?php
/**
* @package   yoo_pinboard Template
* @file      default_logout.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php /** @todo Should this be routed */ ?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	
	<div class="user">
	
		<?php if ( $this->params->get( 'show_page_title' ) ) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<?php if ( $this->params->get( 'show_logout_title' ) ) : ?>
		<h1>
			<?php echo $this->escape($this->params->get( 'header_logout' )); ?>
		</h1>
		<?php endif; ?>

		<?php if ($this->params->get('description_logout') || $this->image) : ?>
		<div class="description">
			<?php echo $this->image; ?>
			<?php if ($this->params->get('description_logout')) : ?>
				<?php echo $this->escape($this->params->get('description_logout_text')); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="login" id="login">
		<fieldset>
			<legend><?php echo JText::_( 'Logout' ) ?></legend>
			<div>
				<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'Logout' ); ?>" />
			</div>
		</fieldset>

		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="logout" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		</form>
		
	</div>
</div>