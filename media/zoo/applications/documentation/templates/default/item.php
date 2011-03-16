<?php
/**
* @package   ZOO Component
* @file      item.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include assets css/js
if (strtolower(substr($GLOBALS['mainframe']->getTemplate(), 0, 3)) != 'yoo') {
	JHTML::stylesheet('reset.css', 'media/zoo/assets/css/');
}
JHTML::stylesheet('zoo.css', $this->template->getURI().'/assets/css/');

// include syntaxhighlighter
JHTML::script('shCore.js', $this->template->getURI().'/assets/syntaxhighlighter/scripts/');
JHTML::script('shAutoloader.js', $this->template->getURI().'/assets/syntaxhighlighter/scripts/');

JHTML::stylesheet('shCore.css', $this->template->getURI().'/assets/syntaxhighlighter/styles/');
JHTML::stylesheet('shThemeRDark.css', $this->template->getURI().'/assets/syntaxhighlighter/styles/');

$css_class = $this->application->getGroup().'-'.$this->template->name;

?>

<div id="yoo-zoo" class="yoo-zoo <?php echo $css_class; ?> <?php echo $css_class.'-'.$this->item->alias; ?>">

	<div class="item">
	
		<?php echo $this->renderer->render('item.full', array('view' => $this, 'item' => $this->item)); ?>
		<?php echo CommentHelper::renderComments($this, $this->item); ?>
		
	</div>

	<script type="text/javascript">

		function syntaxhighlighterpath()	{
		  var args = arguments,
			  result = []
			  ;

		  for(var i = 0; i < args.length; i++)
			  result.push(args[i].replace('@', '<?php echo JRoute::_(JURI::root().$this->template->getURI().'/assets/syntaxhighlighter/scripts/', false); ?>'));

		  return result
		};

		SyntaxHighlighter.defaults['gutter'] = false;

		SyntaxHighlighter.autoloader.apply(null, syntaxhighlighterpath(
		    'applescript            @shBrushAppleScript.js',
			'actionscript3 as3      @shBrushAS3.js',
			'bash shell             @shBrushBash.js',
			'coldfusion cf          @shBrushColdFusion.js',
			'cpp c                  @shBrushCpp.js',
			'c# c-sharp csharp      @shBrushCSharp.js',
			'css                    @shBrushCss.js',
			'delphi pascal          @shBrushDelphi.js',
			'diff patch pas         @shBrushDiff.js',
			'erl erlang             @shBrushErlang.js',
			'groovy                 @shBrushGroovy.js',
			'java                   @shBrushJava.js',
			'jfx javafx             @shBrushJavaFX.js',
			'js jscript javascript  @shBrushJScript.js',
			'perl pl                @shBrushPerl.js',
			'php                    @shBrushPhp.js',
			'text plain             @shBrushPlain.js',
			'py python              @shBrushPython.js',
			'ruby rails ror rb      @shBrushRuby.js',
			'sass scss              @shBrushSass.js',
			'scala                  @shBrushScala.js',
			'sql                    @shBrushSql.js',
			'vb vbnet               @shBrushVb.js',
			'xml xhtml xslt html    @shBrushXml.js'
		));

		SyntaxHighlighter.all();
	</script>

</div>