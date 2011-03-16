<?php
/**
* @package   ZOO Component
* @file      _respond.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// only registered users can comment
$registered = $params->get('registered_users_only');

?>

<div id="respond">
	<h2><?php echo JText::_('Leave a comment'); ?></h2>
	
	<form method="post" action="<?php echo JRoute::_('index.php?option=com_zoo&controller=comment&task=save'); ?>">
	
	<?php if ($active_author instanceof CommentAuthorJoomla) : ?>
		<p class="user">
			<?php echo JText::_('Logged in as').' '.$active_author->name.' ('.JText::_('Joomla').')'; ?>
		</p>
	<?php elseif ($active_author instanceof CommentAuthorFacebook) : ?>
		<p class="user">
			<?php echo JText::_('Logged in as').' '.$active_author->name.' ('.JText::_('Facebook').')'; ?>
			- <a class="facebook-logout" href="<?php echo JRoute::_('index.php?option=com_zoo&controller=comment&task=facebooklogout&item_id='.$item->id); ?>"><?php echo JText::_('Logout'); ?></a>
		</p>
	<?php elseif ($active_author instanceof CommentAuthorTwitter) : ?>
		<p class="user">
			<?php echo JText::_('Logged in as').' '.$active_author->name.' ('.JText::_('Twitter').')'; ?>
			- <a class="twitter-logout" href="<?php echo JRoute::_('index.php?option=com_zoo&controller=comment&task=twitterlogout&item_id='.$item->id); ?>"><?php echo JText::_('Logout'); ?></a>
		</p>
	<?php elseif ($active_author->isGuest()) : ?>
		
		<?php
			$message = $registered ? JText::_('LOGIN_TO_LEAVE_COMMENT') : JText::_('You are commenting as guest.');
		?>
		
		<p class="user"><?php echo $message; ?> <?php if ($params->get('facebook_enable') || $params->get('twitter_enable')) echo JText::_('Optional login below.'); ?></p>
		
		<?php if ($params->get('facebook_enable') || $params->get('twitter_enable')) : ?>	
			<p class="connects">
	
				<?php if ($params->get('facebook_enable')) : ?>
				<a class="facebook-connect" href="<?php echo JRoute::_($this->link_base.'&controller=comment&item_id='.$item->id.'&task=facebookconnect'); ?>">
					<img alt="<?php echo JText::_('Facebook'); ?>" src="<?php echo JURI::root().'media/zoo/assets/images/connect_facebook.png'; ?>"></img>
				</a>
				<?php endif; ?>
				
				<?php if ($params->get('twitter_enable')) : ?>
				<a class="twitter-connect" href="<?php echo JRoute::_($this->link_base.'&controller=comment&item_id='.$item->id.'&task=twitterconnect'); ?>">
					<img alt="<?php echo JText::_('Twitter'); ?>" src="<?php echo JURI::root().'media/zoo/assets/images/connect_twitter.png'; ?>"></img>
				</a>
				<?php endif; ?>
				
			</p>
		<?php endif; ?>

		<?php if (!$registered) : ?>
		
			<div class="author <?php if($params->get('require_name_and_mail')) echo 'required' ;?>">
				<input id="comments-author" type="text" name="author" value="<?php echo $active_author->name; ?>"/>
				<label for="comments-author"><?php echo JText::_('Name'); ?></label>
			</div>
			<div class="email <?php if($params->get('require_name_and_mail')) echo 'required' ;?>">
				<input id="comments-email" type="text" name="email" value="<?php echo $active_author->email; ?>"/>
				<label for="comments-email"><?php echo JText::_('E-mail'); ?></label>
			</div>
			<div class="url">
				<input id="comments-url" type="text" name="url" value="<?php echo $active_author->url; ?>"/>
				<label for="comments-url"><?php echo JText::_('Website'); ?></label>
			</div>
			
		<?php endif; ?>
		
	<?php endif; ?>

		<?php if (!$registered || ($registered && !$active_author->isGuest())) : ?>
		
			<div class="content">
				<textarea name="content" rows="" cols="" ><?php echo $params->get('content'); ?></textarea>
			</div>
	
			<div class="actions">
				<input name="submit" type="submit" value="<?php echo JText::_('Submit comment'); ?>" accesskey="s"/>
				<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
				<span class="submit-message">
					<?php echo JText::_('Submitting comment...'); ?>
				</span>		
			</div>
			
			<input type="hidden" name="item_id" value="<?php echo $this->item->id; ?>"/>
			<input type="hidden" name="parent_id" value="0"/>
			<input type="hidden" name="redirect" value="<?php echo str_replace('&', '&amp;', YRequest::getString('REQUEST_URI', '', 'server')); ?>"/>
			<?php echo JHTML::_('form.token'); ?>
			
		<?php endif; ?>
		
	</form>
</div>