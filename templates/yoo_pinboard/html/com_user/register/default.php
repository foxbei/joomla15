<?php
/**
* @package   yoo_pinboard Template
* @file      default.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	
	<div class="user">
	
		<?php if ( $this->params->get( 'show_page_title' ) ) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<?php if(isset($this->message)) : ?>
			<?php $this->display('message'); ?>
		<?php endif; ?>

		<form action="<?php echo JRoute::_( 'index.php?option=com_user' ); ?>" method="post" id="josForm" name="josForm" class="form-validate">
		<fieldset>
			<legend><?php echo JText::_('Register'); ?></legend>
			
			<div>
				<label class="label-left" id="namemsg" for="name">
					<?php echo JText::_( 'Name' ); ?>:
				</label>
				<input type="text" name="name" id="name" value="<?php echo $this->escape($this->user->get( 'name' ));?>" maxlength="50" /> *
			</div>
			<div>
				<label class="label-left" id="usernamemsg" for="username">
					<?php echo JText::_( 'User name' ); ?>:
				</label>
				<input type="text" id="username" name="username" value="<?php echo $this->escape($this->user->get( 'username' ));?>" maxlength="25" /> *
			</div>
			<div>
				<label class="label-left" id="emailmsg" for="email">
					<?php echo JText::_( 'Email' ); ?>:
				</label>
				<input type="text" id="email" name="email" value="<?php echo $this->escape($this->user->get( 'email' ));?>" maxlength="100" /> *
			</div>
			<div>
				<label class="label-left" id="pwmsg" for="password">
					<?php echo JText::_( 'Password' ); ?>:
				</label>
				<input class="inputbox required validate-password" type="password" id="password" name="password" value="" /> *
			</div>
			<div>
				<label class="label-left" id="pw2msg" for="password2">
					<?php echo JText::_( 'Verify Password' ); ?>:
				</label>
				<input class="inputbox required validate-passverify" type="password" id="password2" name="password2" value="" /> *
			</div>
			<div>
				<?php echo JText::_( 'REGISTER_REQUIRED' ); ?>
			</div>
			<div>
				<button class="button validate" type="submit"><?php echo JText::_('Register'); ?></button>
			</div>
			
		</fieldset>
		
		<input type="hidden" name="task" value="register_save" />
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="gid" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>

	</div>
</div>