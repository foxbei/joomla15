<?php
/**
* @package   ZOO Component
* @file      uninstall.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$error = false;
$extensions = array();
$db = JFactory::getDBO();

// additional extensions
if (isset($this->manifest->additional[0])) {
	$add = $this->manifest->additional[0];
	if (count($add->children())) {
	    $exts = $add->children();
	    foreach ($exts as $ext) {

			// set query
			switch ($ext->name()) {
				case 'plugin':
					$query = 'SELECT * FROM #__plugins WHERE element='.$db->Quote($ext->attributes('name'));
					break;
				case 'module':
					$query = 'SELECT * FROM #__modules WHERE module='.$db->Quote($ext->attributes('name'));
					break;
			}

			// query extension id and client id
			$db->setQuery($query);
			$res = $db->loadObject();

			$extensions[] = array(
				'name' => $ext->data(),
				'type' => $ext->name(),
				'id' => isset($res->id) ? $res->id : 0,
				'client_id' => isset($res->client_id) ? $res->client_id : 0,
				'installer' => new JInstaller(),
				'status' => false);
	    }
	}
}

// uninstall additional extensions
for ($i = 0; $i < count($extensions); $i++) {
	if ($extensions[$i]['id'] > 0 && $extensions[$i]['installer']->uninstall($extensions[$i]['type'], $extensions[$i]['id'], $extensions[$i]['client_id'])) {
		$extensions[$i]['status'] = true;
	}
}

?>
<h3><?php echo JText::_('Additional Extensions'); ?></h3>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_('Extension'); ?></th>
			<th width="60%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($extensions as $i => $ext) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="key"><?php echo $ext['name']; ?> (<?php echo JText::_($ext['type']); ?>)</td>
				<td>
					<?php $style = $ext['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
					<span style="<?php echo $style; ?>"><?php echo $ext['status'] ? JText::_('Uninstalled successfully') : JText::_('Uninstall FAILED'); ?></span>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
