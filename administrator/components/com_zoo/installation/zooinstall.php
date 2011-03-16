<?php
/**
* @package   ZOO Component
* @file      zooinstall.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

class ZooInstall {
	
	public static function doInstall(JInstallerComponent &$component) {
		
		// fix joomla 1.5 bug
		$component->parent->getDBO = $component->parent->getDBO();
		
		// initialize zoo framework
		require_once($component->parent->getPath('source').'/admin/config.php');

		// sanatize table indexes
		$index_sql_path = $component->parent->getPath('source').'/admin/installation/index.sql';
		if (JFile::exists($index_sql_path)) {

			$db = YDatabase::getInstance();

			// read index.sql
			$buffer = JFile::read($index_sql_path);

			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (!empty($queries)) {

				foreach($queries as $query) {

					// replace table prefixes
					$query = $db->replacePrefix($query);

					// parse table name
					preg_match('/ALTER\s*TABLE\s*`(.*)`/i', $query, $result);

					if (count($result) < 2) {
						continue;
					}

					$table = $result[1];

					// get existing indexes
					$indexes = $db->queryObjectList('SHOW INDEX FROM ' . $table);

					// drop existing indexes
					$removed = array();
					foreach ($indexes as $index) {
						if (in_array($index->Key_name, $removed)) {
							continue;
						}
						if ($index->Key_name != 'PRIMARY') {
							$db->query('DROP INDEX ' . $index->Key_name . ' ON ' . $table);
							$removed[] = $index->Key_name;
						}
					}

					// add new indexes
					$db->query($query);
				}
			}
		}
		

		// applications
		if (!JFolder::exists(ZOO_APPLICATION_PATH)) {
			JFolder::create(ZOO_APPLICATION_PATH);
		}
		
		$applications = array();
		foreach (JFolder::folders($component->parent->getPath('source').'/applications', '.', false, true) as $folder) {
			try {
				
				if ($manifest = InstallHelper::findManifest($folder)) {
					
					$name = InstallHelper::getName($manifest);
					$status = InstallHelper::installApplicationFromFolder($folder);
					$applications[] = compact('name', 'status');
			
				}
				
			} catch (YException $e) {
				
				$name = basename($folder);
				$status = false;
				$applications[] = compact('name', 'status');

			}
		}
		
		self::displayResults($applications, 'Applications', 'Application');
		
		// additional extensions
		
		// init vars
		$error = false;
		$extensions = array();
		
		// get plugin files
		$plugin_files = array();
		foreach (YFile::readDirectoryFiles(JPATH_PLUGINS, JPATH_PLUGINS.'/', '/\.php$/', true) as $file) {
			$plugin_files[] = basename($file);
		}
		
		// get extensions
		if (isset($component->manifest->additional[0])) {
			$add = $component->manifest->additional[0];
			if (count($add->children())) {
			    $exts = $add->children();
			    foreach ($exts as $ext) {
					$installer = new JInstaller();
					$installer->setOverwrite(true);
					
					$update = false;
					if (($ext->name() == 'module' && JFolder::exists(JPATH_ROOT.'/modules/'.$ext->attributes('name'))) 
						|| ($ext->name() == 'plugin' && in_array($ext->attributes('name').'.php', $plugin_files))) {
						$update = true;
					}
		
					$folder = $component->parent->getPath('source').'/'.$ext->attributes('folder');
					$folder = rtrim($folder, "\\/") . '/';
					if (JFolder::exists($folder)) {
					    if ($update) {
							foreach (YFile::readDirectoryFiles($folder, $folder, '/positions\.config$/', true) as $file) {
								JFile::delete($file);
							}
						}
			
				    	$extensions[] = array(
							'id' => $ext->attributes('name'),
							'name' => $ext->data(),
							'type' => $ext->name(),
							'folder' => $folder,
							'installer' => $installer,
							'status' => false,
				    		'update' => $update
				    	);
				    }
			    }
			}	
		}
		
		// install additional extensions
		for ($i = 0; $i < count($extensions); $i++) {
			if (is_dir($extensions[$i]['folder'])) {
				if (@$extensions[$i]['installer']->install($extensions[$i]['folder'])) {
					$extensions[$i]['status'] = $extensions[$i]['update'] ? 2 : 1;
					if ($extensions[$i]['status'] == 1) {
						switch ($extensions[$i]['id']) {
							case 'mod_zooquickicon':
								$query = "UPDATE #__modules, (SELECT MAX(ordering) +1 as ord FROM #__modules WHERE position = ".$db->quote('icon').") tt"
									." SET published = 1, position = ".$db->quote('icon').", ordering = tt.ord"
									." WHERE module = 'mod_zooquickicon'";
								$db->query($query);
								break;
							case 'zoosearch':
								$db->query('UPDATE #__plugins SET published = 1 WHERE element =  '.$db->quote($extensions[$i]['id']));
								break;
						}
					}
				} else {
					$error = true;
					break;
				}
			}
		}
				
		// rollback on installation errors
		if ($error) {
			$component->parent->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Error'), 'component');
			for ($i = 0; $i < count($extensions); $i++) { 
				if ($extensions[$i]['status']) {
					$extensions[$i]['installer']->abort(JText::_($extensions[$i]['type']).' '.JText::_('Install').': '.JText::_('Error'), $extensions[$i]['type']);
					$extensions[$i]['status'] = false;
				}
			}
			
			return false;
		}
		
		self::displayResults($extensions, 'Extensions', 'Extension');
		
		// UPGRADES
		
		// get versions
		$new_version = $component->manifest->getElementByPath('version')->data();
		$version = '';
		// check for old version number
		$version_file_path = $component->parent->getPath('extension_administrator').'/version.php';
		if (JFile::exists($version_file_path)) {
			require_once($version_file_path);
		}
		
		// write new version file
		$buffer = "<?php\n\ndefined('_JEXEC') or die('Restricted access');\n\n\$version = '$new_version';";
		JFile::write($version_file_path, $buffer);
		
		// include update script
		require_once($component->parent->getPath('source').'/admin/installation/update.php');
		
		return true;
	}
	
	public static function displayResults($result, $name, $type) {
		
		?>
		
		<h3><?php echo JText::_($name); ?></h3>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_($type); ?></th>
					<th width="60%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php 
					foreach ($result as $i => $ext) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $ext['name']; ?></td>
						<td>
							<?php $style = $ext['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<?php $update = $ext['status'] == 2 ? 'Updated' : 'Installed'; ?>
							<span style="<?php echo $style; ?>"><?php echo $ext['status'] ? JText::_($update.' successfully') : JText::_('NOT Installed'); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<?php
		
	}
	
}