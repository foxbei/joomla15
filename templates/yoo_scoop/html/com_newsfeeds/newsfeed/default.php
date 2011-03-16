<?php
/**
* @package   yoo_scoop Template
* @file      default.php
* @version   5.5.3 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct acces
defined('_JEXEC') or die('Restricted access');
?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="weblinks">

		<?php if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<h2>
			<a href="<?php echo $this->newsfeed->channel['link']; ?>" target="_blank"><?php echo str_replace('&apos;', "'", $this->newsfeed->channel['title']); ?></a>
		</h2>

		<?php if ( $this->params->get( 'show_feed_description' ) ) : ?>
		<div class="description">
			<?php echo str_replace('&apos;', "'", $this->newsfeed->channel['description']); ?>
		</div>
		<?php endif; ?>

		<?php if ( isset($this->newsfeed->image['url']) && isset($this->newsfeed->image['title']) && $this->params->get( 'show_feed_image' ) ) : ?>
			<img src="<?php echo $this->newsfeed->image['url']; ?>" alt="<?php echo $this->newsfeed->image['title']; ?>" />
		<?php endif; ?>

		<ul>
			<?php foreach ( $this->newsfeed->items as $item ) :  ?>
			<li>
			<?php if ( !is_null( $item->get_link() ) ) : ?>
				<a href="<?php echo $item->get_link(); ?>" target="_blank"><?php echo $item->get_title(); ?></a>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_item_description' ) && $item->get_description()) : ?>
				<br />
				<?php $text = $this->limitText($item->get_description(), $this->params->get( 'feed_word_count' ));
					echo str_replace('&apos;', "'", $text);
				?>
				<br /><br />
			<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>

	</div>
</div>
