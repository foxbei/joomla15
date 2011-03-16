<?php
/**
* @package   ZOO Component
* @file      video.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register parent class
JLoader::register('ElementFile', ZOO_ADMIN_PATH.'/elements/file/file.php');

/*
	Class: ElementVideo
		The video element class
*/
class ElementVideo extends ElementFile implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
            $params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$file = $this->_data->get('file');
		$url  = $this->_data->get('url');
		return !empty($file) || !empty($url);
	}

	/*
		Function: getVideoFormat
			Trys to return the video format for source.

	   Parameters:
            $source - the video source

		Returns:
			String - the video format, if found
	*/
	public function getVideoFormat($source) {		
		foreach ($this->_getVideoFormats() as $key => $tmp) {
		   if (isset($tmp['regex'])) {
			   if (preg_match($tmp['regex'], $source, $matches) && isset($matches[1])) {
				   return $key;
			   }
		   } else if (YFile::getExtension($source) == $key) {
			   return $key;
		   }
		}
		return null;
	}

	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// init vars
		$width     = $this->_data->get('width');
		$height    = $this->_data->get('height');
		$autoplay  = $this->_data->get('autoplay', false);
		$autoplay_bool = $autoplay ? 'true' : 'false';
		$autoplay_google = $autoplay ? 'autoplay:"true"' : '';
		$directory = $this->_config->get('directory');
		$formats   = $this->_getVideoFormats();
		$file 	   = $this->_data->get('file');
		$source    = $file ? JURI::root().$directory.'/'.$file : $this->_data->get('url');
		$format    = $this->getVideoFormat($source);

		if (isset($formats[$format])) {

			if (in_array($format, array('flv', 'swf', 'youtube.com', 'video.google.com', 'vimeo.com', 'liveleak.com', 'vids.myspace.com'))) {

				JHTML::script('swfobject.js', 'administrator/components/com_zoo/elements/video/assets/js/');

				$width = $width ? $width : 200;
				$height = $height ? $height : 200;
			}

			if (in_array($format, array('mov', 'mpg', 'mp4'))) {
				JHTML::script('quicktime.js', 'administrator/components/com_zoo/elements/video/assets/js/');
			}

			// parse source link
			if (isset($formats[$format]['regex'])) {
				if (preg_match($formats[$format]['regex'], $source, $matches)) {
					if (isset($matches[1])) {
						$source = $matches[1];
					}
				}
			}

			// video params
			$params = array("{SOURCE}", "{ID}", "{WIDTH}", "{HEIGHT}", "{AUTOPLAY}", "{AUTOPLAY-AS-INT}", "{AUTOPLAY-GOOGLE}");

			// replacements
			$replace = array($source, "video-" . YUtility::generateUUID(), $width, $height, $autoplay_bool, $autoplay, $autoplay_google);

			return JFilterOutput::ampReplace(str_replace($params, $replace, $formats[$format]['html']));

		}

		return JText::_('No video selected.');

	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		JHTML::script('video.js', 'administrator/components/com_zoo/elements/video/assets/js/');
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {

		// init vars
		$directory  = $this->_config->get('directory');
		$directory 	= trim($directory, '/').'/';
		$width      = $this->_data->get('width');
		$height     = $this->_data->get('height');
		$autoplay   = $this->_data->get('autoplay', false);
		$extensions	= 'avi|divx|flv|mov|mpg|mp4|wmv|swf';
		$formats    = $this->_getVideoFormats();
	
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'directory' => $directory,
                    'extensions' => $extensions,
                    'file' => $this->_data->get('file'),
                    'url' => $this->_data->get('url'),
					'width' => $width,
                    'height' => $height,
                    'autoplay' => $autoplay
                )
            );
        }
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {

        // init vars
		$width      = $this->_data->get('width');
		$height     = $this->_data->get('height');
		$autoplay   = $this->_data->get('autoplay', false);

        // get params
        $params       = new YArray($params);
        $trusted_mode = $params->get('trusted_mode');

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout,
                array(
                    'element' => $this->identifier,
                    'url' => $this->_data->get('url'),
                    'trusted_mode' => $trusted_mode,
                    'width' => $width,
                    'height' => $height,
                    'autoplay' => $autoplay
                )
            );
        }

        return null;

	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
        $validator = new YValidatorUrl(array('required' => $params->get('required')), array('required' => 'Please enter an URL.'));
        $url = $validator->clean($value->get('url'));
        if ($url) {

			// get video format
			$format = $this->getVideoFormat($url);

            // filter file formats
            $formats = array_filter($this->_getVideoFormats(), create_function('$a', 'return isset($a["regex"]);'));          

            if (!in_array($format, array_keys($formats))) {
                throw new YValidatorException('Not a valid video format.');
            }
        }

        $validator = new YValidatorInteger(array('required' => false), array('number' => 'The Width needs to be a number.'));
        $width     = $validator->clean($value->get('width'));
		$width	   = empty($width) ? '' : $width;

        $validator = new YValidatorInteger(array('required' => false), array('number' => 'The Height needs to be a number.'));
        $height    = $validator->clean($value->get('height'));
		$height	   = empty($height) ? '' : $height;

        $autoplay  = $value->get('autoplay');

		return compact('url', 'format', 'width', 'height', 'autoplay');
	}

	/*
	   Function: _getVideoFormats
	       Return all supported video formats and corresponding html

	   Returns:
	       Array - Formats
	*/
	protected function _getVideoFormats() {
				
		// Flash Video
		$formats['flv']['name'] = 'Flash Video (.flv)';
		$formats['flv']['html'] = '
				<div id="{ID}">
					<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
				</div>
				<script type="text/javascript">
					swfobject.embedSWF("'.ZOO_ADMIN_URI.'elements/video/assets/player/NonverBlaster.swf", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", { mediaURL: "{SOURCE}", teaserURL: "", allowSmoothing: "true", autoPlay: "{AUTOPLAY}", scaleIfFullScreen: "true", showScalingButton: "true", controlColor:"0xFFFFFF" }, {allowFullScreen:"true", wmode: "transparent", play:"{AUTOPLAY}" });
				</script>';
			
		// Flash
		$formats['swf']['name'] = 'Flash (.swf)';
		$formats['swf']['html'] = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">		
				swfobject.embedSWF("{SOURCE}", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {}, {allowFullScreen:"true", wmode: "transparent", play:"{AUTOPLAY}" });
			</script>';
			
		// Windows Media Video
		$formats['wmv']['name'] = 'Windows Media Video (.wmv)';
		$formats['wmv']['html'] = '
			<object id="{ID}" width="{WIDTH}" height="{HEIGHT}" classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components..." type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">
				<param name="filename" value="{SOURCE}">
				<param name="Showcontrols" value="True">
				<param name="autoStart" value="{AUTOPLAY}">
				<embed type="application/x-mplayer2" src="{SOURCE}" name="MediaPlayer" autostart="{AUTOPLAY}" width="{WIDTH}" height="{HEIGHT}"></embed>
			</object>';

		// QuickTime Player HTML
		$quicktime = '
			<script type="text/javascript">
				QT_WriteOBJECT_XHTML("{SOURCE}", "{WIDTH}", "{HEIGHT}", "", "AUTOPLAY", "{AUTOPLAY}");
			</script>';
			
		// QuickTime
		$formats['mov']['name'] = 'QuickTime (.mov)';
		$formats['mov']['html'] = $quicktime;
			
		// MPEG Video
		$formats['mpg']['name'] = 'MPEG Video (.mpg)';
		$formats['mpg']['html'] = $quicktime;
		
		// MPEG4 Video
		$formats['mp4']['name'] = 'MPEG4 Video (.mp4)';
		$formats['mp4']['html'] = $quicktime;		
		
		// Divx Player HTML
		$divx = '
			<object type="video/divx" data="{SOURCE}" style="width:{WIDTH}px;height:{HEIGHT}px;">
				<param name="type" value="video/divx" />
				<param name="src" value="{SOURCE}" />
				<param name="data" value="{SOURCE}" />
				<param name="codebase" value="{SOURCE}" />
				<param name="url" value="{SOURCE}" />
				<param name="mode" value="full" />
				<param name="pluginspage" value="http://go.divx.com/plugin/download/" />
				<param name="allowContextMenu" value="true" />
				<param name="previewImage" value="{SOURCE}" />
				<param name="autoPlay" value="{AUTOPLAY}" />
				<param name="minVersion" value="1.0.0" />
				<param name="custommode" value="none" />
				<p>No video? Get the DivX browser plug-in for <a href="http://download.divx.com/player/DivXWebPlayerInstaller.exe">Windows</a> or <a href="http://download.divx.com/player/DivXWebPlayer.dmg">Mac</a></p>
			</object>';

		// Divx	
		$formats['divx']['name'] = 'DivX (.divx)';
		$formats['divx']['html'] = $divx;

		// Audio Video Interleave
		$formats['avi']['name'] = 'Audio Video Interleave (.avi)';
		$formats['avi']['html'] = $divx;
			
		// Youtube
		$formats['youtube.com']['name']  = 'Youtube (youtube.com)';
		$formats['youtube.com']['regex'] = '/.*youtube\.com.*v=(.*?)$/';		
		$formats['youtube.com']['html']  = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
				swfobject.embedSWF("http://www.youtube.com/v/{SOURCE}&hl=en&fs=1&autoplay={AUTOPLAY-AS-INT}", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {}, { allowFullScreen:"true", wmode: "transparent", quality: "high", play: "{AUTOPLAY}" });
			</script>';

		// Google Video
		$formats['video.google.com']['name']  = 'Google Video (video.google.com)';
		$formats['video.google.com']['regex'] = '/.*video\.google\.com.*docid=(.*?)[&]/';
		$formats['video.google.com']['html']  = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
				swfobject.embedSWF("http://video.google.com/googleplayer.swf?docid={SOURCE}&hl=en&fs=true", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {{AUTOPLAY-GOOGLE}}, { allowFullScreen:"true", wmode: "transparent", quality: "high", play: "{AUTOPLAY}" });
			</script>';

		// Vimeo
		$formats['vimeo.com']['name']  = 'Vimeo (vimeo.com)';
		$formats['vimeo.com']['regex'] = '/.*vimeo\.com\/(.*?)$/';
		$formats['vimeo.com']['html']  = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
				swfobject.embedSWF("http://www.vimeo.com/moogaloop.swf?clip_id={SOURCE}&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&fullscreen=1&autoplay={AUTOPLAY-AS-INT}", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {}, { allowFullScreen:"true", wmode: "transparent", quality: "high", scale: "showAll", play: "{AUTOPLAY}", allowscriptaccess: "always" });
			</script>';

			
		// Liveleak
		$formats['liveleak.com']['name']  = 'Liveleak (liveleak.com)';
		$formats['liveleak.com']['regex'] = '/.*liveleak\.com.*i=(.*?)$/';
		$formats['liveleak.com']['html']  = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
				swfobject.embedSWF("http://www.liveleak.com/e/{SOURCE}", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {}, { allowFullScreen:"true", wmode: "transparent", quality: "high", play: "{AUTOPLAY}", allowscriptaccess: "always" });
			</script>';


		// Myspace
		$formats['vids.myspace.com']['name']  = 'Myspace Video (vids.myspace.com)';
		$formats['vids.myspace.com']['regex'] = '/.*vids\.myspace\.com.*VideoID=(.*?)$/';
		$formats['vids.myspace.com']['html']  = '
			<div id="{ID}">
				<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
			</div>
			<script type="text/javascript">
				swfobject.embedSWF("http://lads.myspace.com/videos/vplayer.swf", "{ID}", "{WIDTH}", "{HEIGHT}", "7.0.0","", {}, { allowFullScreen:"true", wmode: "transparent", quality: "high", play: "{AUTOPLAY}", allowscriptaccess: "sameDomain", flashvars: "m={SOURCE}&v=2&type=video" });
			</script>';

		return $formats;
	}

}