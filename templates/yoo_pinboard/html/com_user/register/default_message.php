<?php
/**
* @package   yoo_pinboard Template
* @file      default_message.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<h2>
	<?php echo $this->escape($this->message->title); ?>
</h2>

<p>
	<?php echo $this->escape($this->message->text); ?>
</p>
