<?php
	if (!defined('iNH_EXEC')) {
		header("HTTP/1.0 404 Not Found");
		header("Location: ../");
		exit('This page does not exist.');
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ro-ro" lang="ro-ro" >
<head>
	<base href="http://<?=$_SERVER['HTTP_HOST'].$basepath?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?=(isset($page['title'])?$page['title'].' | ':'').DEFAULT_TITLE?></title>

	<script type="text/javascript" src="template/js/libs.js"></script>
	<script type="text/javascript" src="template/js/jquery.goodies.js"></script>
	<? if ($page['jsf'] && is_array($page['jsf']))
		foreach ($page['jsf'] as $script)
			echo '<script type="text/javascript" src="template/js/'.$script.'.js"></script>';
	?>
	<? if(!empty($page['js'])): ?>
	<script type="text/javascript">
	<?=$page['js']?>
	</script>
	<? endif; ?>

	<link href="/template/css/<?=JQUERY_UI_THEME?>/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>
	<link href="/template/css/main.css" rel="stylesheet" type="text/css"/>
	<link href="/template/css/superfish.css" rel="stylesheet" type="text/css"/>
	<link href="/template/css/superfish-navbar.css" rel="stylesheet" type="text/css"/>
	<? if ($page['css']): ?>
	<style type="text/css"><?=$page['css']?></style>
	<? endif;
	if ($page['cssf'] && is_array($page['cssf']))
		foreach ($page['cssf'] as $script): 
	?><link href="/template/css/<?php echo $script; ?>.css" type="text/css" rel="stylesheet" />
	<?php endforeach; ?>
</head>
<body>

	<div id="wrap">
		<? if ($_SESSION['logat']): ?>
		<div id="menus">

			<div id="buttonsContainer">
				<button class="link" id="chgPassBtn" rel="button" href="/auth/changePass" >Schimba parola</button>
				<button class="link" id="logoutBtn" rel="button" href="/auth/logout">Delogare</button>
			</div>

			<div style="float:right;height:100%;margin-right:10px;">
				Utilizator:<strong><?=$_SESSION['nume']?></strong><br/>
				<?if ($_SESSION['denumire']) { ?>(<?=$_SESSION['denumire']?>)<?}?>
			</div>

			<ul class="sf-menu" style="">
			  <li><a  href="/auth/welcome">Prima pagina</a></li>
			  <? if ($_SESSION['id_tip_utilizator']==4) {?><li><a href="/users/view">Utilizatori</a>
				<ul>
					<li><a href="/users/view">Lista</a></li>
					<li><a href="/users/add">Adaugă</a></li>
				</ul>
			  </li><?} ?>
				<li><a style="cursor: default">Persoane</a>
					<ul>
						<li><a href="/persoane"<?=($_GET['module']=='persoane' && $_GET['action']=='index'?'':' class="current"')?>>Lista</a></li>
						<?if($_SESSION['id_tip_utilizator']==2){?><li><a href="/persoane/add"<?=($_GET['action']=='add'?'':' class="current"')?>>Adaugă</a></li>
						<li><a href="/persoane/index/idf/<?=$_SESSION['id_firma']?>">Angajații firmei</a>
							<ul>
								<li><a href="/persoane/index/idf/<?=$_SESSION['id_firma']?>/idta/1">Șoferi</a></li>
								<li><a href="/persoane/index/idf/<?=$_SESSION['id_firma']?>/idta/2">Dispeceri</a></li>
								<li><a href="/persoane/index/idf/<?=$_SESSION['id_firma']?>/idta/3">Persoane desemnate</a></li>
								<li><a href="/persoane/index/idf/<?=$_SESSION['id_firma']?>">Orice tip</a></li>
							</ul>
						</li>
						<?}?>
					</ul>
				</li>
				<li><a href="/masini/dispecerat">Autorizatii taxi</a></li>
				<? /*<li><a style="cursor: default">Mașini</a>
					<ul>
						<li><a href="/masini/index">Masini deținute</a></li>
					</ul>
				</li>*/?>
				<? if ($_SESSION['id_tip_utilizator']==2) {?><li><a href="/info">Informații firmă</a></li><?}?>
			</ul>

		</div>

		<? else: ?>
		<div id="menus">
			<ul class="sf-menu"><li><a>iHazNews</a></li></ul>
		</div>
		<? endif; ?>
		<div id="content">
			<? if (!empty($_SESSION['warning'])) {
				echo '<div id="warning">'.$_SESSION['warning'].'</div>';
				unset ($_SESSION['warning']);
			} ?>
			<?php echo $continut; ?>
		</div>

		<div id="footer">
		<!--	Navigatoare recomandate
				<a href="http://www.google.ro/chrome/eula.html">Google Chrome</a> sau
				<a href="http://www.mozilla.com/ro/">Mozilla Firefox</a> -->
		</div>
	</div>
</body>
</html>