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
<div class="breadcrumbs">
<?php for ($i = 0; $i < $count; $i ++) :

	// clean subtitle from breadcrumb
	if ($pos = strpos($list[$i]->name, '||')) {
		$name = trim(substr($list[$i]->name, 0, $pos));
	} else {
		$name = $list[$i]->name;
	}
	
	// if not the last item in the breadcrumbs add the separator
	if ($i < $count -1) {
		if(!empty($list[$i]->link)) {
			echo '<a href="'.$list[$i]->link.'">'.$name.'</a>';
		} else {
			echo '<span>' . $name . '</span>';
		}
	} else { // when $i == $count -1
	    echo '<span>' . $name . '</span>';
	}
endfor; ?>
</div>