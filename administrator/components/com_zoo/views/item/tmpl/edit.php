<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.2.0 November 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

	defined('_JEXEC') or die('Restricted access');

	JHTML::_('behavior.tooltip');
		
	// add script
	JHTML::script('observer.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('autocompleter.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('autocompleter.request.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('tag.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('alias.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('item.js', 'administrator/components/com_zoo/assets/js/');
	JHTML::script('element.js', 'administrator/components/com_zoo/assets/js/');

	// filter output
	JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES, array('params', 'elements')); 

?>

<form id="item-edit" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

	<?php echo $this->partial('menu'); ?>

	<div class="box-bottom">
		<div class="col col-left width-60">

			<fieldset class="creation-form">
				<legend><?php echo JText::_('Details'); ?></legend>
				<div class="element element-name">
					<strong><?php echo JText::_('Name'); ?></strong>
					<div id="name-edit">
						<div class="row">
							<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->item->name; ?>"></input>
							<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
						</div>
						<div class="slug">
							<span><?php echo JText::_('Slug'); ?>:</span>
							<a class="trigger" href="#" title="<?php echo JText::_('Edit Item Slug');?>"><?php echo $this->item->alias; ?></a>
							<div class="panel">
								<input type="text" name="alias" value="<?php echo $this->item->alias; ?>"></input>
								<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>"/>
								<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="element element-published">
					<strong><?php echo JText::_('Published'); ?></strong>
					<?php echo $this->lists['select_published']; ?>
				</div>
				<div class="element element-searchable">
					<strong><?php echo JText::_('Searchable'); ?></strong>
					<?php echo $this->lists['select_searchable']; ?>
				</div>
				<div class="element element-comments">
					<strong><?php echo JText::_('Comments'); ?></strong>
					<?php echo $this->lists['select_enable_comments']; ?>
				</div>
				<div class="element element-frontpage">
					<strong><?php echo JText::_('Frontpage'); ?></strong>
					<?php echo $this->lists['select_frontpage']; ?>
				</div>
				<div class="element element-categories">
					<strong><?php echo JText::_('Categories'); ?></strong>
					<?php echo $this->lists['select_categories']; ?>
				</div>
				<div class="element element-primary-category">
					<strong><?php echo JText::_('Primary Category'); ?></strong>
					<?php echo $this->lists['select_primary_category']; ?>
				</div>
				<?php
				foreach ($this->item->getElements() as $element) {
					$element->loadAssets();

					// set label
					$name = $element->getConfig()->get('name');

					if ($description = $element->getConfig()->get('description')) {
						$description = ' class="editlinktip hasTip" title="'.$description.'"';
					}

					$html   = array();
					$html[] = '<div class="element element-'.$element->getElementType().'">';
					$html[] = '<strong'.$description.'>'.$name.'</strong>';
					$html[] = $element->edit();
					$html[] = '</div>';

					echo implode("\n", $html);
				}
				?>
			</fieldset>

		</div>
		
		<div class="col col-right width-40">

			<table width="100%" class="infobox">
				<?php if ($this->item->id) : ?>
				<tr>
					<td>
						<strong><?php echo JText::_('Item ID'); ?>:</strong>
					</td>
					<td>
						<?php echo $this->item->id; ?>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td>
						<strong><?php echo JText::_('Type'); ?></strong>
					</td>
					<td>
						<?php echo $this->item->getType()->name; ?>
						<input type="hidden" name="type" value="<?php echo $this->item->type; ?>"></input>
					</td>
				</tr>					
				<tr>
					<td>
						<strong><?php echo JText::_('State'); ?></strong>
					</td>
					<td>
						<?php echo $this->item->state > 0 ? JText::_('Published') : ($this->item->state < 0 ? JText::_('Archived') : JText::_('Draft Unpublished'));?>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('Hits'); ?></strong>
					</td>
					<td>
						<?php echo $this->item->hits;?>
						<span <?php echo !$this->item->hits ? 'style="display: none; visibility: hidden;"' : null; ?>>
							<input name="reset_hits" type="button" class="button" value="<?php echo JText::_('Reset'); ?>" onclick="submitbutton('resethits');"></input>
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('Created'); ?></strong>
					</td>
					<td>
						<?php echo $this->item->created == $this->db->getNullDate() ? JText::_('New item') : JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('Modified'); ?></strong>
					</td>
					<td>
						<?php echo $this->item->modified == $this->db->getNullDate() ? JText::_('Not modified') : JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
                <tr>
					<td>
						<strong><?php echo JText::_('Author'); ?></strong>
					</td>
					<td>
						<?php

							// author
							if ($author = $this->item->created_by_alias) {
								echo $author;
							} else if ($name = JFactory::getUser($this->item->created_by)->name) {
								echo $name;
							} else {
								echo JText::_('Guest');
							}

						?>
					</td>
				</tr>
			</table>

			<?php
							
				// get item xml form
				$form = new YParameterFormDefault(dirname(__FILE__).'/params.xml');
			
				// set details parameter
				$details = new YParameter();
				$details->set('created_by', $this->item->created_by == '' ? JFactory::getUser()->id : 'NO_CHANGE');
				$details->set('access', $this->item->access);
				$details->set('created_by_alias', $this->item->created_by_alias);
				$details->set('created', JHTML::_('date', $this->item->created, '%Y-%m-%d %H:%M:%S'));
				$details->set('publish_up', JHTML::_('date', $this->item->publish_up, '%Y-%m-%d %H:%M:%S'));
				$details->set('publish_down', JHTML::_('date', $this->item->publish_down, '%Y') <= 1969 || $this->item->publish_down == $this->db->getNullDate() ? JText::_('Never') : JHTML::_('date', $this->item->publish_down, '%Y-%m-%d %H:%M:%S'));
			
			?>

			<div id="parameter-accordion">
				<h3 class="toggler"><?php echo JText::_('Details'); ?></h3>
				<div class="content">
					<?php echo $form->setValues($details)->render('details'); ?>
				</div>
				<h3 class="toggler"><?php echo JText::_('Metadata'); ?></h3>
				<div class="content">
					<?php echo $form->setValues($this->params->get('metadata.'))->render('params[metadata]', 'metadata'); ?>
				</div>
				<h3 class="toggler"><?php echo JText::_('Template'); ?></h3>
				<div class="content">
					<?php
						if ($template = $this->application->getTemplate()) {
							echo $template->getParamsForm(true)->setValues($this->params->get('template.'))->render('params[template]', 'item');
						} else {
							echo '<em>'.JText::_('Please select a Template').'</em>';
						}
					?>
				</div>
				<h3 class="toggler"><?php echo JText::_('Tags'); ?></h3>
				<div class="content">
					<div id="tag-area">
						<div class="input">
							<div class="hint"><?php echo JText::_('Add new tag');?></div>
							<input type="text" value="" autocomplete="off" />
							<button class="button-grey" type="button"><?php echo JText::_('Add tag')?></button>
							<div class="icon"></div>						
						</div>
						<p><?php echo JText::_('Seperate multiple tags through commas'); ?>.</p>
						<div class="tag-list">
						<?php foreach ($this->item->getTags() as $tag) :?>
							<div>
								<a></a>
								<?php echo $tag; ?>
								<input type="hidden" value="<?php echo $tag; ?>" name="tags[]" />
							</div>
						<?php endforeach;?>	
						</div>
						<?php if (count($this->lists['most_used_tags'])) : ?>
						<p><?php echo JText::_('Choose from the most used tags');?>.</p>
						<div class="tag-cloud">
							<?php foreach ($this->lists['most_used_tags'] as $tag) :?>
								<a class="button-grey" title="<?php echo $tag->items . ' ' . ($tag->items == 1 ? JText::_('item') : JText::_('items')); ?>"><?php echo $tag->name; ?></a>
							<?php endforeach;?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="hits" value="<?php echo $this->item->hits; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">
	window.addEvent('domready', function() {
		new Zoo.AliasEdit({ edit: <?php echo (int) $this->item->id; ?> });
		new Zoo.EditItem();
		$('name-edit').getElement('input[name="name"]').focus();
		new Zoo.Tag();
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>