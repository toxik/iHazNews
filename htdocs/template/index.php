<?php
	if (!defined('iNH_EXEC')) {
		header("HTTP/1.0 404 Not Found");
		header("Location: ../");
		exit('This page does not exist.');
	}
	
	$menu = new Model_Meniu(); 
	$auth = new Model_AuthBox();
?><!DOCTYPE html>
<html lang="ro" >
<head>
	<base href="http://<?=$_SERVER['HTTP_HOST'].$basepath?>">
	<meta charset="utf-8" />
	<title><?=(isset($page['title'])?$page['title'].' | ':'').DEFAULT_TITLE?></title>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script> 
	<script type="text/javascript" src="/template/js/libs.js?v=<?php echo filemtime('template/js/libs.js'); ?>"></script>
	<script type="text/javascript" src="/template/js/jquery.goodies.js?v=<?php echo filemtime('template/js/jquery.goodies.js'); ?>"></script>

	<link href="/template/css/main.css?v=<?php echo filemtime('template/css/main.css'); ?>" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/ui-lightness/jquery-ui.css" type="text/css" /> 
	<style type="text/css">.ui-widget { font-size: 85% } .ui-datepicker { font-size: 100% }</style> 

	<? if ($page['jsf'] && is_array($page['jsf']))
		foreach ($page['jsf'] as $script)
			echo '<script type="text/javascript" src="template/js/'.$script.'.js"></script>';
	?>
	<? if(!empty($page['js'])): ?>
	<script type="text/javascript">
	<?=$page['js']?>
	</script>
	<? endif; ?>
	
	<? if ($page['css']): ?>
	<style type="text/css"><?=$page['css']?></style>
	<? endif;
	if ($page['cssf'] && is_array($page['cssf']))
		foreach ($page['cssf'] as $script): 
	?><link href="/template/css/<?php echo $script; ?>.css?v=<?php 
		echo filemtime('template/css/'.$script.'.css'); ?>" type="text/css" rel="stylesheet" />
	<?php endforeach; ?>
</head>
<body id="home"> 
<!--[if IE 5]><div id="ie5" class="ie"><![endif]--> 
<!--[if IE 6]><div id="ie6" class="ie"><![endif]--> 
<!--[if IE 7]><div id="ie7" class="ie"><![endif]--> 
<div id="wrapper">
	<div id="banner"> 
		<h1 class="logo"> 
			<a href="/" title="iHazNews"><span>iHazNews</span></a> 
		</h1> 
		<div id="social-buttons">
			<div id="fb-root"></div>
			<span id="tweet-fix">
				<a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-url="http://ihaznews.com">Tweet</a>
			</span>
			<span id="fb-fix">
				<fb:like layout="button_count" href="http://ihaznews.com" colorscheme="light" send="false" width="10" show_faces="false"></fb:like>
			</span>
		</div>
		<div id="navigation"> 
			<div class="left"></div> 
			<?php
				$menu->printMenu();
			?>
			<div class="right"></div> 
		</div> 
		<div id="authBox">
			<?php
				$auth->printBox();
			?>
		</div>
	</div> 
	<div id="content-wrapper"> 
		<div id="content"> 
				<div class="content">﻿<div class="content-body"> 
<div id="home-content-wrapper">
	
	<div id="home-content"> 
		<?php 
		if ($continut):
			echo $continut; ?>
		<?php
		else:
		?>
		
		<div id="home-nav"> 
			<div class="home-nav-section"> 
				<h3>Rich effects and UI widgets</h3> 
				<p>jQuery UI features low-level effect and interaction API's (like drag and drop) as well as full-featured and highly configurable ready-to-use widgets. Supports IE 6.0+, Firefox 3+, Safari 3.1+, Opera 9.6+ and Google Chrome.</p> 
				<ul> 
					<li><a href="/demos" class="learn-more">Browse all effects &#38; widgets</a></li> 
				</ul> 
			</div> 
			
			<div class="home-nav-section"> 
				<h3>Powerful theme framework</h3> 
				<p>Unique CSS framework, ThemeRoller tool and theme gallery makes creating a custom look and feel for your application fast and easy.</p> 
				<ul> 
					<li><a href="/themeroller#themeGallery" class="learn-more">Explore the theme gallery</a></li> 
					<li><a href="/themeroller" class="learn-more">Design a custom theme</a></li> 
				</ul> 
			</div> 
			
			<div class="home-nav-section last"> 
				<h3>Flexible &amp; easy to learn</h3> 
				<p>Leverages the power of jQuery, making it easy to start right away with detailed documentation, tutorials and a vibrant community.</p> 
				<ul> 
					<li><a href="/docs/Getting_Started" class="learn-more">Get started</a></li> 
					<li><a href="/demos" class="learn-more">View documentation</a></li> 
					<li><a href="/support" class="learn-more">Join the discussion</a></li> 
				</ul> 
			</div> 
		</div><!-- /home-nav --> 
	
		<div id="home-sidebar"> 
			<div class="home-sidebar-section"> 
				<h2>Recent activity</h2> 
				<p>jQuery UI 1.8 adds position, button, autocomplete, new widget factory, lighter core. <a href="http://blog.jqueryui.com/2010/03/jquery-ui-18/" class="learn-more">What's new</a></p> 
				<p>Help us design future plugins <a href="http://wiki.jqueryui.com/" class="learn-more">Planning wiki</a></p> 
			</div> 
			
			<div class="home-sidebar-section"> 
				<h2>Developer links</h2> 
				<ul> 
					<li><a href="http://jquery-ui.googlecode.com/files/jquery-ui-1.8.11.zip">Latest dev bundle <span>(1.8.11)</span></a></li> 
					<li><a href="/docs/Git">Fork jQuery UI on GitHub</a></li> 
					<li><a href="http://code.google.com/apis/libraries/devguide.html#jqueryUI">Google CDN for jQuery UI</a></li> 
					<li><a href="/development">Development status</a></li> 
					<li><a href="http://wiki.jqueryui.com">Development &amp; planning wiki</a></li> 
				</ul> 
			</div> 
			
			<div id="jq-books" class="home-sidebar-section"> 
				<h2>Books about jQuery UI</h2> 
				<ul> 
					<li class="jq-clearfix"> 
						<h3><a href="http://www.packtpub.com/user-interface-library-for-jquery-ui-1-7?utm_source=jqueryui.com&amp;utm_medium=link&amp;utm_content=pod&amp;utm_campaign=mdb_001590">jQuery UI 1.7: The User Interface Library for jQuery</a></h3> 
						<div class="jq-author">Dan Wellman</div> 
						<a href="http://www.packtpub.com/user-interface-library-for-jquery-ui-1-7?utm_source=jqueryui.com&amp;utm_medium=link&amp;utm_content=pod&amp;utm_campaign=mdb_001590" class="jq-buyNow">Buy Now</a> 
					</li> 
				</ul> 
			</div> 
		</div><!-- home-sidebar --> 
	<?php endif; ?>
	</div><!-- /home-content --> 
	
 
</div><!-- /home-content-wrapper --> 
</div>		</div> 
		</div> 
	</div> 
	
	<div id="footer"> 
		<div class="bg"></div> 
		<div class="inner"> 
			<p><span class="first">&copy; 2011 iHazNews Team</span></p> 
			<div id="dock" style="float: right; padding-right: 12px;"> 
				<?php  $menu->printMenu('about'); ?>
			</div> 
		</div> 
	</div> 
</div> 
<!--[if lte IE 7]></div><![endif]--> 
<script src="http://ihaznews.com/tracking"></script>
<script src="http://connect.facebook.net/en_US/all.js#appId=228562197159771&amp;xfbml=1"></script>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>  
</body> 
</html> 