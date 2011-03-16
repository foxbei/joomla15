<?php
/**
* @package   yoo_pinboard Template
* @file      modules.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Module chrome for rendering yoo module
 */
function modChrome_yoo($module, &$params, &$attribs) {
	
	// init vars
	$id              = $module->id;
	$position        = $module->position;
	$showtitle       = $module->showtitle;
	$content         = $module->content;
	$suffix          = $params->get('moduleclass_sfx', '');
	$order           = isset($attribs['order']) ? intval($attribs['order']) : null;
	$badge           = '';
	$color           = '';
	$extra_badge     = '';

	if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
		$is_ie7 = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 7') !== false;	
		$is_ie6 = strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6') !== false;
	}

	// create title
	$pos   = JString::strpos($module->title, ' ');
	$title = ($pos !== false) ? JString::substr($module->title, 0, $pos).'<span class="color">'.JString::substr($module->title, $pos).'</span>' : $module->title;

	// preserve module content, to fix current joomla 1.5.3 issue
    static $mod_content = array();
	isset($mod_content[$id]) ? $content = $mod_content[$id] : $mod_content[$id] = $content;
	
	// only render module if order is matching
	if ($order !== null) {
		$modules =& JModuleHelper::getModules($position);
		if (!(isset($modules[$order]) && $modules[$order]->id == $id)) {
			return;
		}
	}

	// set badge if exists
	$suffix = preg_replace('/\s\s+/', ' ', $suffix); // trim all whitespaces
	$split = explode(' ', $suffix);
	$suffix = $split[0];
	if (count($split) == 2) {
		$badge = "badge-" . $split[1];
	}

	// set default module types
	if ($suffix == '') {
		if ($module->position == 'header') $suffix = 'headerbar';
		if ($module->position == 'top') $suffix = 'note';
		if (($module->position == 'top-equal') || ($module->position == 'top-goldenratio')) $suffix = 'note';
		if ($module->position == 'left') $suffix = 'postit';
		
		if ($module->position == 'right') {
			$suffix = 'dotted';
			global $option;
			if (isset($option) && $option == 'com_content') {
				if (JRequest::getCmd('view') == 'frontpage' || (in_array(JRequest::getCmd('view'), array('section', 'category')) && JRequest::getCmd('layout') == 'blog')) {
					$suffix = 'postit';
				}
			}
		}
		
		if (($module->position == 'main-top-equal') || ($module->position == 'main-top-goldenratio')) $suffix = 'postit';
		if (($module->position == 'content-top-equal') || ($module->position == 'content-top-goldenratio')) $suffix = 'postit';
		if (($module->position == 'content-bottom-equal') || ($module->position == 'content-bottom-goldenratio')) $suffix = 'postit';
		if (($module->position == 'main-bottom-equal') || ($module->position == 'main-bottom-goldenratio')) $suffix = 'postit';
		if (($module->position == 'bottom-equal') || ($module->position == 'bottom-goldenratio')) $suffix = 'note';
		if ($module->position == 'bottom') $suffix = 'note';
	}

	// force module type
	if ($module->position == 'toolbar')  $suffix = 'blank';
	if ($module->position == 'header')  $suffix = 'headerbar';

	// legacy compatibility
	if ($suffix == '-blank') $suffix = 'blank';
	if ($suffix == '_menu')  $suffix = 'menu';

	// set module skeleton using the suffix
	switch ($suffix) {

		case 'headerbar':
			$skeleton = '0-3n-0';
			$suffix   = 'mod-' . $suffix;
			$extra_badge = '<div class="badge-soldier"></div>	';
			break;

		case 'polaroid':
			$skeleton = '2n-4n[hb]-3n';
			$suffix   = 'mod-' . $suffix . '2';
			$extra_badge = '<div class="badge-tape"></div>	';
			break;

		case 'postit':
			$skeleton = '2n-2n-3n';
			$suffix   = 'mod-' . $suffix . '2';
			break;

		case 'tile':
			$skeleton = '3n-3n-3n';
			$suffix   = 'mod-' . $suffix;
			break;
			
		case 'note':
			$skeleton = '2n-2n-3n';
			$suffix   = 'mod-' . $suffix;
			break;
			
		case 'print':
			$skeleton = '2n-3n-3n';
			$suffix   = 'mod-' . $suffix;
			break;
			
		case 'cardboard':
			$skeleton = '3n-3n-3n';
			$suffix   = 'mod-' . $suffix;
			break;

		case 'clip':
			$skeleton = '2n-2n-3n';
			$suffix   = 'mod-' . $suffix;
			$extra_badge = '<div class="badge-paperclip"></div>	';
			break;

		case 'dotted':
			$skeleton = '0-1-0';
			$suffix   = 'mod-' . $suffix;
			break;

		case 'menu':
			if ($module->position == 'right') {
				$suffix = "mod-dotted";
				$skeleton = '0-1-0';
			} else {
				$suffix = "mod-tile";
				$skeleton = '3n-3n-3n';
			}
			$suffix   = $suffix . ' mod-menu';
			break;

		case 'blank':
			$suffix   = 'mod-blank';
			
		default:
			$skeleton = 'not defined';
	}

	// module output
	switch ($skeleton) {
		case '3n-3n-3n':
			/*
			 * module skeleton with 3n-3n-3n div structure 
			 * usage: rounded transparent
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-t1">
						<div class="box-t2">
							<div class="box-t3"></div>
						</div>
					</div>
					
					<div class="box-1">
						<div class="box-2">
							<div class="box-3 deepest">
								<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
								<?php endif; ?>
								<?php echo $content; ?>
							</div>
						</div>
					</div>

					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '2n-3n-3n':
			/*
			 * module skeleton with 3n-3n-3n div structure 
			 * usage: paper2
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-t1">
						<div class="box-t2"></div>
					</div>
					
					<div class="box-1">
						<div class="box-2">
							<div class="box-3 deepest">
								<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
								<?php endif; ?>
								<?php echo $content; ?>
							</div>
						</div>
					</div>

					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '2n-2n-3n':
			/*
			 * module skeleton with 0-3n-3n div structure
			 * usage: postit2
			 *
			 * 2n: 2 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-t1">
						<div class="box-t2"></div>
					</div>
					
					<div class="box-1">
						<div class="box-2 deepest">
							<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
							<?php endif; ?>
							<?php echo $content; ?>
						</div>
					</div>
						
					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;


		case '3n-1-3n':
			/*
			 * module skeleton with 3n-1-3n div structure 
			 * usage: rounded
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
					
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
				
					<div class="box-t1">
						<div class="box-t2">
							<div class="box-t3"></div>
						</div>
					</div>

					<div class="box-1 deepest">
						<?php if ($showtitle) : ?>
						<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
						<?php endif; ?>
						<?php echo $content; ?>
					</div>

					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '3n-6n-3n':
			/*
			 * module skeleton with 3n-6-3n div structure 
			 * usage: black
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
					
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
				
					<div class="box-t1">
						<div class="box-t2">
							<div class="box-t3"></div>
						</div>
					</div>

					<div class="box-1">
						<?php if ($showtitle) : ?>
						<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
						<?php endif; ?>
						
						<div class="box-2">
							<div class="box-3">
								<div class="box-4">
									<div class="box-5">
										<div class="box-6 deepest">
											<?php echo $content; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '0-3n[hb]-3n':
			/*
			 * module skeleton with 0-3n-3n div structure
			 * usage: polaroid
			 *
			 * 3n: 3 nested divs
			 * [hb]: header at bottom
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">

					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-1">
						<div class="box-2 deepest">
							<div class="box-3">
								<?php echo $content; ?>
							</div>
						</div>
						
						<?php if ($showtitle) : ?>
							<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
						<?php endif; ?>
						
					</div>
						
					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;
			
		case '2n-4n[hb]-3n':
			/*
			 * module skeleton with 0-3n-3n div structure
			 * usage: polaroid2
			 *
			 * 3n: 3 nested divs
			 * [hb]: header at bottom
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-t1">
						<div class="box-t2"></div>
					</div>
					
					<div class="box-1">
						<div class="box-2">
							<div class="box-3 deepest">
								<div class="box-4">
									<?php echo $content; ?>
								</div>
							</div>
						
							<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
							<?php endif; ?>
						
						</div>
					</div>
						
					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '0-2n-3n':
			/*
			 * module skeleton with 0-3n-3n div structure
			 * usage: postit
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-1 deepest">
						<div class="box-2">
							<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
							<?php endif; ?>
							<?php echo $content; ?>
						</div>
					</div>
						
					<div class="box-b1">
						<div class="box-b2">
							<div class="box-b3"></div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '0-4nf-0':
			/*
			 * module skeleton with 0-4n-0 div structure
			 * usage: tab
			 *
			 * 4nf: 4 nested divs with inner floatbox
			 */
			?>
			
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
				
					<div class="box-1">
						<div class="box-2">
							<div class="box-3">
								<div class="box-4 deepest">
								
									<?php if ($showtitle) : ?>
										<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
									<?php endif; ?>
									
									<div class="floatbox">
										<?php echo $content; ?>
									</div>
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 
			break;

		case '0-3n-0':
			/*
			 * module skeleton with 0-3n-0 div structure
			 * usage: header position
			 *
			 * 3n: 3 nested divs
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-1">
						<div class="box-2">
							<div class="box-3 deepest">
								<?php if ($showtitle) : ?>
								<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
								<?php endif; ?>
								<?php echo $content; ?>
							</div>
						</div>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		case '0-1-0':
			/*
			 * module skeleton with 1 div structure
			 * usage: dotted, line
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module deepest">
				
					<?php if ($badge != '') : ?>
						<div class="<?php echo $badge ?>"></div>
					<?php endif; ?>
					<?php echo $extra_badge ?>
					
					<div class="box-1">
						<?php if ($showtitle) : ?>
							<h3 class="header"><span class="header-2"><span class="header-3"><?php echo $title; ?></span></span></h3>
						<?php endif; ?>
						<?php echo $content; ?>
					</div>
					
				</div>
			</div>
			<?php 
			break;

		default:
			/*
			 * usage: any undefined module
			 */
			?>
			<div class="<?php echo $suffix; ?>">
				<div class="module">
				
					<?php if ($showtitle) : ?>
					<h3 class="module"><?php echo $title; ?></h3>
					<?php endif; ?>
					<?php echo $content; ?>
					
				</div>
			</div>
			<?php 
			break;
	}
}

?>