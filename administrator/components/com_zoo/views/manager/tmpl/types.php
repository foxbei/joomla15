<?php 
/**
* @package   ZOO Component
* @file      types.php
* @version   2.3.7 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 

JHTML::_('behavior.tooltip');

?>

<form id="manager-types" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<?php if (count($this->types)) : ?>

	<table id="actionlist" class="list stripe">
	<thead>
		<tr>
			<th class="checkbox">
				<input type="checkbox" class="check-all" />
			</th>
			<th class="name" colspan="2">
				<?php echo JText::_('Name'); ?>
			</th>
			<th class="template">
				<?php echo JText::_('Template Layouts'); ?>
			</th>
			<th class="submission">
				<?php echo JText::_('Submission Layouts'); ?>
			</th>
			<th class="module">
				<?php echo JText::_('Module Layouts'); ?>
			</th>
			<th class="plugin">
				<?php echo JText::_('Plugin Layouts'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($this->types as $type) :
				$edit     = JRoute::_($this->baseurl.'&task=edittype&cid[]='.$type->id);
				$edit_elm = JRoute::_($this->baseurl.'&task=editelements&cid[]='.$type->id);
		?>
		<tr>
			<td class="checkbox">
				<input type="checkbox" name="cid[]" value="<?php echo $type->id; ?>" />
			</td>
			<td class="icon"></td>
			<td class="name">
				<span class="editlink hasTip" title="<?php echo JText::_('Edit Type');?>::<?php echo $type->name; ?>">
					<a href="<?php echo $edit; ?>"><?php echo $type->name; ?></a>
				</span>
				<span class="actions-links">&rsaquo; 
					<span class="hasTip" title="<?php echo JText::_('Edit Elements');?>::<?php echo $type->name; ?>">
						<a href="<?php echo $edit_elm; ?>"><?php echo JText::_('Edit Elements');?></a>
					</span>
				</span>
			</td>
			<td class="template">
				<?php foreach ($this->templates as $template) {
					$metadata = $template->getMetadata();
					echo '<div>'.$metadata['name'].': ';
					
					$renderer = new ItemRenderer();
					$renderer->addPath($template->getPath());
					
					$path   = 'item';
					$prefix = 'item.';
					if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type->id)) {
						$path   .= DIRECTORY_SEPARATOR.$type->id;
						$prefix .= $type->id.'.';
					}
					
					$links = array();
					foreach ($renderer->getLayouts($path) as $layout) {
						
						// get layout metadata
						$metadata = $renderer->getLayoutMetaData($prefix.$layout);

                        if (in_array($metadata->get('type'), array(null, 'related', 'googlemaps'))) {
                            
                            // create link
                            $link = '<a href="'.JRoute::_($this->baseurl.'&task=assignelements&type='.$type->id.'&template='.$template->name.'&layout='.$layout).'">'.$metadata->get('name', $layout).'</a>';

                            // create tooltip
                            if ($description = $metadata->get('description')) {
                                $link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
                            }

                            $links[] = $link;
                        }
					}
					echo implode(' | ', $links);
					echo '</div>';
				} ?>
			</td>
            <td class="submission">
				<?php
					foreach ($this->templates as $template) {
						$metadata = $template->getMetadata();
						echo '<div>'.$metadata['name'].': ';

						$renderer = new ItemRenderer();
						$renderer->addPath($template->getPath());

						$path   = 'item';
						$prefix = 'item.';
						if ($renderer->pathExists($path.DIRECTORY_SEPARATOR.$type->id)) {
							$path   .= DIRECTORY_SEPARATOR.$type->id;
							$prefix .= $type->id.'.';
						}

						$links = array();
						foreach ($renderer->getLayouts($path) as $layout) {

							// get layout metadata
							$metadata = $renderer->getLayoutMetaData($prefix.$layout);

							if ($metadata->get('type') == 'submission') {

								// create link
								$link = '<a href="'.JRoute::_($this->baseurl.'&task=assignsubmission&type='.$type->id.'&template='.$template->name.'&layout='.$layout).'">'.$metadata->get('name', $layout).'</a>';

								// create tooltip
								if ($description = $metadata->get('description')) {
									$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
								}

								$links[] = $link;
							}
						}
						echo implode(' | ', $links);
						echo '</div>';
					}
				?>
			</td>
			<td class="module">
				<?php foreach ($this->modules as $module) {
					
					$module_name = $module['name'];	
					if (($xml = YXML::loadFile($module['path'].DIRECTORY_SEPARATOR.$module['name'].'.xml')) && $xml->getName() == 'install') {
						$module_name = (string) $xml->getElementByPath('name');
					}
					
					echo '<div>'.$module_name.': ';
					
					$renderer = new YRenderer();
					$renderer->addPath($module['path']);

					$links = array();
					foreach ($renderer->getLayouts('item') as $layout) {
						
						// get layout metadata
						$metadata = $renderer->getLayoutMetaData("item.$layout");

						// create link
						$link = '<a href="'.JRoute::_($this->baseurl.'&task=assignelements&type='.$type->id.'&module='.$module['name'].'&layout='.$layout).'">'.$metadata->get('name', $layout).'</a>';
						
						// create tooltip
						if ($description = $metadata->get('description')) {
							$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
						}
						
						$links[] = $link;
					}
					echo implode(' | ', $links);
					echo '</div>';
				} ?>
			</td>
			<td class="plugin">
				<?php 
					foreach ($this->plugins as $plugin_type => $plugins) {
						foreach ($plugins as $plugin) {
							
							$plugin_name = $plugin['name'];
							if (($xml = YXML::loadFile(dirname($plugin['path']).DIRECTORY_SEPARATOR.$plugin['name'].'.xml')) && $xml->getName() == 'install') {
								$plugin_name = (string) $xml->getElementByPath('name');
							}							
							echo '<div>'.$plugin_name.': ';
					
							$renderer = new YRenderer();
							$renderer->addPath($plugin['path']);

							$links = array();
							foreach ($renderer->getLayouts('item') as $layout) {
						
								// get layout metadata
								$metadata = $renderer->getLayoutMetaData("item.$layout");

								// create link
								$link = '<a href="'.JRoute::_($this->baseurl.'&task=assignelements&type='.$type->id.'&plugin='.$plugin_type.'/'.$plugin['name'].'&layout='.$layout).'">'.$metadata->get('name', $layout).'</a>';
						
								// create tooltip
								if ($description = $metadata->get('description')) {
									$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
								}
						
								$links[] = $link;
							}
							echo implode(' | ', $links);
							echo '</div>';
						}
					} 
                ?>
            </td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>

	<?php else: 
	
			$title   = JText::_('You don\'t have any types yet').'!';
			$message = JText::_('In the Type Manager you can create, edit and manage your content types. Each type is a composition of different elements. To create an item you have to chose of which type it should be. Build your custom type to fit your item requirements. Make sure your app has at least one type to create an item. Types can be created by clicking the new button in the toolbar to the upper right.');
			echo $this->partial('message', compact('title', 'message'));
		
	endif; ?>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_('form.token'); ?>

</form>

<?php echo ZOO_COPYRIGHT; ?>