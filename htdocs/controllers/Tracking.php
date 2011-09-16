<?php

class Controller_Tracking extends Controller_Abstract
{
	function index()
	{
		$this->p['single'] = true;
		
		if (!$_SERVER['HTTP_REFERER'])
			return;
		
		$u = new Model_Auth;
		$user = $u->getUserId();
		
		if (!$user)
			return;
		
		$w = new Model_Website;
		$website_id = $w->getIDbyURL($_SERVER['HTTP_REFERER']);
			if (!$website_id)
				return;
		
		$data = array(
						'USER_ID' => $user,
						'WEBSITE_ID' => $website_id,
						'FULL_URL' => $_SERVER['HTTP_REFERER']
				);
						
		$t = new Model_Tracking();
		$this->p['ts'] = $t->insert($data);
	}
	
	function end_tracking()
	{
		
		// fara linia de mai jos nu se poate face apelul din motive de securitate
		if ($_SERVER['HTTP_ORIGIN'])
			header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
		else
			header('Access-Control-Allow-Origin: *');
		$this->p['standalone'] = true;
		
		
		if (!$_GET['ts'])
			return;
			
		if ($_GET['sid'] != session_id()) {
			session_destroy();
			session_id($_GET['sid']);
			session_start();
			Zend_Debug::dump($_SESSION);
		}
		
		$u = new Model_Auth;
		
		$user = $u->getUserId();
		if (!$user)
			return;
		
		$w = new Model_Website;
		$website_id = $w->getIDbyURL($_SERVER['HTTP_REFERER']);
		if (!$website_id)
			return;
		
		$data = array(
						'USER_ID' => $user,
						'WEBSITE_ID' => $website_id,
						'FULL_URL' => $_SERVER['HTTP_REFERER'],
						'VIEW_STARTED_AT' => rawurldecode($_GET['ts'])
					);
		echo 'wtfd';
		$t = new Model_Tracking();
		$t->update($data);
	}
}