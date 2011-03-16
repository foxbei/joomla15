<?php
/**
* @package   yoo_pinboard Template
* @file      template.php
* @version   5.5.1 October 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// get template configuration
include(dirname(__FILE__).'/template.config.php');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->warp->config->get('language'); ?>" lang="<?php echo $this->warp->config->get('language'); ?>" dir="<?php echo $this->warp->config->get('direction'); ?>" >
<head>
<?php echo $this->warp->template->render('head'); ?>
<link rel="apple-touch-icon" href="<?php echo $this->warp->path->url('template:apple_touch_icon.png'); ?>" />
</head>

<body id="page" class="yoopage <?php echo $this->warp->config->get('leftcolumn'); ?> <?php echo $this->warp->config->get('rightcolumn'); ?> <?php echo $this->warp->config->get('itemcolor'); ?> <?php echo $this->warp->config->get('blog'); ?> <?php echo $this->warp->config->get('toolscolor'); ?>">
	
	<?php if($this->warp->modules->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this->warp->modules->render('absolute'); ?>
	</div>
	<?php endif; ?>

	<div id="page-header">
		<div class="page-header-t">
			<div class="page-header-b">
				<div class="wrapper floatholder">
		
					<div id="header">
							
						<div id="toolbar">
							<div class="floatbox ie_fix_floats">
							
								<?php if($this->warp->config->get('date')) : ?>
								<div id="date">
									<?php echo $this->warp->config->get('actual_date'); ?>
								</div>
								<?php endif; ?>
			
								<?php if($this->warp->modules->count('topmenu')) : ?>
								<div id="topmenu">
									<?php echo $this->warp->modules->render('topmenu'); ?>
								</div>
								<?php endif; ?>
		
								<?php echo $this->warp->modules->render('toolbar'); ?>
			
							</div>
						</div>
		
						<div id="headerbar">
							<div class="floatbox ie_fix_floats">
								<?php echo $this->warp->modules->render('header'); ?>
							</div>
						</div>
		
						<?php if($this->warp->modules->count('menu')) : ?>
						<div id="menu">
							<?php echo $this->warp->modules->render('menu'); ?>
						</div>
						<?php endif; ?>
			
						<?php if($this->warp->modules->count('logo')) : ?>		
						<div id="logo">
							<?php echo $this->warp->modules->render('logo'); ?>
						</div>
						<?php endif; ?>
			
						<?php if($this->warp->modules->count('search')) : ?>
						<div id="search">
							<?php echo $this->warp->modules->render('search'); ?>
						</div>
						<?php endif; ?>
			
						<?php if ($this->warp->modules->count('banner')) : ?>
						<div id="banner">
							<?php echo $this->warp->modules->render('banner'); ?>
						</div>
						<?php endif; ?>
		
					</div>
					<!-- header end -->

				</div>
			</div>
		</div>
	</div>

	<div id="page-body">
		<div class="wrapper floatholder">
			
			<?php if ($this->warp->modules->count('top + topblock')) : ?>
			<div id="top">
				<div class="floatbox ie_fix_floats">
		
					<?php if($this->warp->modules->count('topblock')) : ?>
					<div class="topblock width100 float-left">
						<?php echo $this->warp->modules->render('topblock'); ?>
					</div>
					<?php endif; ?>
		
					<?php if ($this->warp->modules->count('top')) : ?>
							<?php echo $this->warp->modules->render('top', array('wrapper'=>"topbox float-left", 'layout'=>$this->warp->config->get('top'))); ?>
					<?php endif; ?>
										
				</div>
			</div>
			<!-- top end -->
			<?php endif; ?>

			<div id="middle">
				<div class="background">

					<?php if ($this->warp->modules->count('breadcrumbs')) : ?>
					<div id="breadcrumbs">
						<?php echo $this->warp->modules->render('breadcrumbs'); ?>
					</div>
					<?php endif; ?>

					<?php if($this->warp->modules->count('left')) : ?>
					<div id="left">
						<div id="left_container" class="clearfix">
							<?php echo $this->warp->modules->render('left'); ?>
						</div>
					</div>
					<!-- left end -->
					<?php endif; ?>
		
					<div id="main">
						<div id="main_container" class="clearfix">
						
							<div class="main-tr">
								<div class="main-t">
									<div class="main-pin"></div>
								</div>
							</div>
		
							<div class="main-r">
								<div class="main-m">
			
									<?php if ($this->warp->modules->count('maintop')) : ?>
									<div id="maintop" class="floatbox">
										<?php echo $this->warp->modules->render('maintop', array('wrapper'=>"maintopbox float-left", 'layout'=>$this->warp->config->get('maintop'))); ?>
									</div>
									<!-- maintop end -->
									<?php endif; ?>
				
									<div id="mainmiddle" class="floatbox">
				
										<?php if($this->warp->modules->count('right')) : ?>
										<div id="right">
											<div id="right_container" class="clearfix">
												<?php echo $this->warp->modules->render('right'); ?>
											</div>
										</div>
										<!-- right end -->
										<?php endif; ?>
						
										<div id="content">
											<div id="content_container" class="clearfix">
				
												<?php if ($this->warp->modules->count('contenttop')) : ?>
												<div id="contenttop" class="floatbox">
													<?php echo $this->warp->modules->render('contenttop', array('wrapper'=>"contenttopbox float-left", 'layout'=>$this->warp->config->get('contenttop'))); ?>
												</div>
												<!-- contenttop end -->
												<?php endif; ?>

												<div class="floatbox">
													<?php echo $this->warp->template->render('content'); ?>
												</div>
				
												<?php if ($this->warp->modules->count('contentbottom')) : ?>
													<div id="contentbottom" class="floatbox">
														<?php echo $this->warp->modules->render('contentbottom', array('wrapper'=>"contentbottombox float-left", 'layout'=>$this->warp->config->get('contentbottom'))); ?>
													</div>
												<!-- mainbottom end -->
												<?php endif; ?>
				
											</div>
										</div><div style="display:none;"><a href="http://www.web-smith.ru/">Design Studio</a></div>
										<!-- content end -->
				
									</div>
									<!-- mainmiddle end -->
				
									<?php if ($this->warp->modules->count('mainbottom')) : ?>
									<div id="mainbottom" class="floatbox">
										<?php echo $this->warp->modules->render('mainbottom', array('wrapper'=>"mainbottombox float-left", 'layout'=>$this->warp->config->get('mainbottom'))); ?>
									</div>
									<!-- mainbottom end -->
									<?php endif; ?>
			
								</div>
							</div>
		
							<div class="main-bl">
								<div class="main-br">
									<div class="main-b"></div>
								</div>
							</div>
		
						</div>
					</div>
					<!-- main end -->
		
				</div>
			</div>
			<!-- middle end -->
			
			<?php if ($this->warp->modules->count('bottom + bottomblock')) : ?>
			<div id="bottom">
				<div class="floatbox ie_fix_floats">
					
					<?php if ($this->warp->modules->count('bottom')) : ?>
						<?php echo $this->warp->modules->render('bottom', array('wrapper'=>"bottombox float-left", 'layout'=>$this->warp->config->get('bottom'))); ?>
					<?php endif; ?>
					
					<?php if($this->warp->modules->count('bottomblock')) : ?>
					<div class="bottomblock width100 float-left">
						<?php echo $this->warp->modules->render('bottomblock'); ?>
					</div>
					<?php endif; ?>
							
				</div>
			</div>
			<!-- bottom end -->
			<?php endif; ?>

			<div id="footer">
				<div class="floatbox"><a class="anchor" href="#page"></a></div>
				<div class="footer-bg"><?php echo $this->warp->modules->render('footer'); ?></div>
				<?php echo $this->warp->modules->render('debug'); ?>
			</div>
			<!-- footer end -->
	
		</div>
	</div>
	
</body>
</html>