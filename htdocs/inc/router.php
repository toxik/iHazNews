<?php
// if the application doesn't reside in root (nexuspro.info), but in a folder (nexuspro.info/example_app)
$basepath 	 = substr($_SERVER['SCRIPT_NAME'],0,-9);
$request_str = trim(substr($_SERVER['REQUEST_URI'],strlen($basepath)),'/');

// process the request
for ($i = 2, $arr = explode('/', $request_str), $n = count($arr); $i < $n; $i+=2) {
	if (!strstr($arr[$i],'[')) {
		if ($arr[$i][0] != '?')
			$_GET[$arr[$i]] = urldecode($arr[$i+1]);
		else unset($_GET[$arr[$i]]);
	} else
		$_GET[strstr($arr[$i],'[',true)][] = $arr[$i+1];
}
// process the module and action
$module 	 = !empty($arr[0]) ? $arr[0] : DEFAULT_MODULE;
$action		 = !empty($arr[1]) ? $arr[1] : DEFAULT_ACTION;
// maybe we have some ? in our URL.. let's clean it and do a redirect
define(MODULE, $module);
define(ACTION, $action);

if (strstr($request_str,'?')) {
	if (strstr($request_str,'/?') === false) {
		$request_str = str_replace('?', '/?', $request_str);
		redirect('/'.$request_str);
	}
	$path = '';
	foreach ($_GET as $key => $value)
		if (!is_array($value))
			$path .= '/' . $key . '/' . urlencode($value);
		else foreach ($value as $newKey => $newValue)
			$path .= '/' . $key . '[]/' . urlencode($newValue);
		
	$uri = $_SERVER['REQUEST_URI'];
	$path = $basepath . $module . '/' . $action . $path;
	redirect($path);
	exit();
}

// sa purificam input-ul
foreach ($_GET as $id => $val)
	if (!is_array($val))
		$_GET[$id] = strip_tags($val);
foreach ($_POST as $id => $val)
	if (!in_array($id, array('text') ) &&
			!is_array($val) )
		$_POST[$id] = strip_tags($val);

// initialize the main variable
$page = array();

// verify if the specified module exists
if (!file_exists(APP_PATH.'controllers/'.ucwords($module).'.php')) {
	header("HTTP/1.0 404 Not Found");
	//header("Location: " . $basepath);
	exit('This page does not exist! - controller');
}
//echo benchmark().'se incarca modulul <br />';
// instantiate the module
$moduleName = 'Controller_' . ucfirst($module);
$controller = new $moduleName($page);

// call the specified function
// we make sure the Controller's method exists..
if (!method_exists($moduleName,$action)) {
	header("HTTP/1.0 404 Not Found");
	header("Location: ".$basepath);
	exit('This page does not exist! - action');
}
try {
	// afisam erorile de sql daca suntem in development
	if ($_SERVER['APPLICATION_ENV'] == 'development') {
		try { 
			$controller->$action();
		} catch (Zend_Db_Exception $e) {
			exit ('Eroare la baza de date:<br />'."\n".$e->getMessage());
		}
	} else 
		$controller->$action();
}
catch (Zend_Exception $e) {
	header("HTTP/1.0 404 Not Found");
	if ($_SERVER['APPLICATION_ENV'] != 'development') {
		header("Location: ".$basepath);
	} else {
		//echo $e->getMessage();
		throw $e;
	}
	exit('This page does not exist! - load action');
}
unset ($controller); // facem curatenie in RAM
//echo benchmark().'s-a executat metoda <br />';

// start the buffer
if (!file_exists(APP_PATH.'views/'.$module.'/'.$action.'.phtml') && !$page['standalone'])
	throw new Exception('Eroare interna la incarcarea view-ului din Controller-ul '
					.ucwords($module).' pentru Action-ul '.ucwords($action).'!');
// "prindem" continutul	
ob_start();
	// include the view
	if (!$page['standalone'])
		require 'views/'.$module.'/'.$action.'.phtml';
		
$continut = ob_get_clean();
	
//echo benchmark().'s-a scris in buffer<br />';
// include the template ( unless a flag is set )
if (!$page['single'] && !$page['standalone'])
	require 'template/index.php';
else
	if (!$page['standalone'])
		echo $continut;

		