<?php
/**
* @package   Warp Theme Framework
* @file      default.php
* @version   5.5.8
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright  2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<script language="javascript" type="text/javascript">
function iFrameHeight() {
	var h = 0;
	if ( !document.all ) {
		h = document.getElementById('blockrandom').contentDocument.height;
		document.getElementById('blockrandom').style.height = h + 60 + 'px';
	} else if( document.all ) {
		h = document.frames('blockrandom').document.body.scrollHeight;
		document.all.blockrandom.style.height = h + 20 + 'px';
	}
}
</script>

<div id="system" class="<?php echo $this->params->get('pageclass_sfx')?>">

	<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_title')); ?></h1>
	<?php endif; ?>

	<iframe <?php echo $this->wrapper->load; ?>
		id="blockrandom"
		name="iframe"
		src="<?php echo $this->wrapper->url; ?>"
		width="<?php echo $this->params->get( 'width' ); ?>"
		height="<?php echo $this->params->get( 'height' ); ?>"
		scrolling="<?php echo $this->params->get( 'scrolling' ); ?>"
		align="top"
		frameborder="0"
		class="">
		<?php echo JText::_( 'NO_IFRAMES' ); ?>
	</iframe>

</div>
