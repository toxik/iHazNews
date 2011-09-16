<?php
class Controller_Administrator extends Controller_Abstract {
	private $dataset_names = 'kinky scalado Cart-wright swypes gluhwein Jape Hanniel garrison house trophy wife hypnopompic equal protection chicken casserole outrightly Jogging Pelecanoididae tableware systolic murmur sylphlike hydrocyanic acid 48CO hematic draw play Religion, Wars of chimere MY56 stir-fry melamine trammel out of place suborder Blattaria down-at-the-heels lead nation Inducting nares Rantipole Tiddle comedically grass pea charge in Unstableness carriole scarped agma one-two punch Emanuel Svedberg Manumotive Alembroth Ship-building Epicene Storying Francis Richard Stockton extend oneself Sail loft bowl or glass boulle monomolecular Stefansson bed jacket cross-dress vena gastrica-dextra Prescind chauffeur Fistuca Ore-weed brachycranial Finland 8M8 Sarda chiliensis C6H5C2H2C2H2CO2H 8PA4 pidginize Shrugging Scalable Sampson William Thomas Beshone ABEL Curstness Kedger Qqon Salinas de Gortari turn again';
	private $dataset_domains = '.net .co.uk .com .ro .org .ca .co .me .cc';
	private $dataset_endings = 'Acct Official User Account Online';
	private $user;
	private $website;
	private $newsgroup;
	private $interest;
	private $auth;

	function init() {
		$_SESSION['user_type'] = 'administrator';
		$this->p['cssf'][] = 'admin';
		$this->p['jsf'][] = 'jquery.uitablefilter';
		$this->p['jsf'][] = 'jquery.MetaData';
		$this->p['jsf'][] = 'admin';
		$this->p['pageNo'] = 1;
		$this->auth = new Model_Auth();
		$this->user = new Model_User();
		$this->website = new Model_Website();
		$this->newsgroup = new Model_Newsgroup();
		$this->interest = new Model_Interest();
	}
	
	function index() {
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		$events = array(
			'Attempt of <b>Database Breach</b> from <b>%s</b>, in <b>%s</b>',
			'Attempt of <b>Security Breach</b> from <b>%s</b>, in <b>%s</b>',
			'Attempt of <b>XSS</b> from <b>%s</b>, in <b>%s</b>',
			'Attempt of <b>SQL Injection</b> from <b>%s</b>, in <b>%s</b>'			
		);
		$places = array(
			'Administration console', 'Login system', 'Register system', 'Newsgroups Administration',
			'Widget generation', 'Tracking system'
		);
		for($i = 0; $i < 20; $i++) {
			$ts = time() - $i * mt_rand(1003360, 1253360);
			$ip = mt_rand(1,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(1,255);
			$place = $places[mt_rand(0, count($places)-1)];
			$this->p['events'][$ts] = array(
				'date' 		=> date('c', $ts),
				'message'	=> sprintf( $events[mt_rand(0,count($events)-1)], $ip, $place ),
				'measure'	=> sprintf( '<b>%s</b> banned for <b>%s</b> hours', $ip, mt_rand(2, 9) )
			);
		}
		krsort($this->p['events']);
	}

	function users() {
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		$adminInfo = $this->auth->getUserData();
		$this->p['ownid'] = $adminInfo['USER_ID'];
		$pageNo = 1;
		$this->p['errors'] = array();
		if($_GET)
		{
			if(array_key_exists('pageNo',  $_GET))
				$pageNo = $_GET['pageNo'];
			if(array_key_exists('activated', $_GET))
			{
				$activated = true;
				if($_GET['activated'] == 'false')
					$activated = false;
				$userid = $_GET['userid'];
				if($userid != $adminInfo['USER_ID']){
					$rezultat = $this->user->activate($userid, $activated);
					$this->website->activateUserWebsites($userid, $activated);
					if(!$rezultat)
						$this->p['errors'][]  = 'Eroare la activarea / dezactivarea User-ului';
				}
			}
			header('Location: /administrator/users');
		}
		$this->p['usersCount'] = $this->user->getListCount();
		$this->p['users'] = $this->user->getList($pageNo);
		$this->p['pageNo'] = $pageNo;
		
	}
		
	function websites() {
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		$pageNo = 1;
		$this->p['errors'] = array();
		if($_GET)
		{
			if(array_key_exists('pageNo',  $_GET))
				$pageNo = $_GET['pageNo'];
			if(array_key_exists('activated', $_GET))
			{
				$activated = true;
				if($_GET['activated'] == 'false')
					$activated = false;
				$websiteid = $_GET['websiteid'];
				$rezultat = $this->website->enable($websiteid, $activated);
				if(!$rezultat)
					$this->p['errors'][]  = 'Eroare la activarea / dezactivarea Website-ului';
			}
		}
		$this->p['websiteCount'] = $this->website->getListCount();
		$this->p['websites'] = $this->website->getList($pageNo);
		$this->p['pageNo'] = $pageNo;
		
		$user = $this->auth->getUserData();	
		$userid = $user['USER_ID'];
		$this->p['USER_ID'] = $userid;
	}
	
	function newsgroups() {
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		$pageNo = 1;
		$this->p['newsgroupMesaj'] = '';
		$this->p['newsgroupEdit'] = '';
		$this->p['newsgroupEditID'] = '0';
		$this->p['newsgroupEditState'] = 'dezactivat';
		$this->p['errors'] = array();
		if($_GET)
		{
			if(array_key_exists('pageNo',  $_REQUEST))
				$pageNo = $_REQUEST['pageNo'];
			if(array_key_exists('activated', $_GET))
			{
				$activated = true;
				if($_GET['activated'] == 'false')
					$activated = false;
				$newsgroupid = $_GET['newsgroupid'];
				$rezultat = $this->newsgroup->activate($newsgroupid, $activated);
				if(!$rezultat)
					$this->p['errors'][]  = 'Eroare la activarea / dezactivarea Newsgroup-ului';
			}
			else if(array_key_exists('newsgroupEdit', $_GET))
			{
				$this->p['newsgroupEdit'] = $_GET['newsgroupEdit'];
				$this->p['newsgroupEditID'] = $_GET['newsgroupid'];
				$this->p['newsgroupEditState'] = $_GET['newsgroupEditState'];
			}
			//$this->p['errors'][] = 'dami-as foc';
			if(array_key_exists('createNewsgroup', $_GET) || array_key_exists('saveNewsgroup', $_GET) || array_key_exists('resetNewsgroup', $_GET))
			{
				//$this->p['errors'][] = 'Procesare _GET smecher';
				//event din cadrul form-ului de editare newsgroup (cu post). 
				//check reset form
				if(isset($_GET['resetNewsgroup']))
				{//deja sunt initializate asa. nu efectuam nimic
					/*$this->p['newsgroupEdit'] = 'Un nou newsgroup';
					$this->p['newsgroupEditID'] = '0';
					$this->p['newsgroupEditState'] = 'dezactivat';*/
				}
				else
				{
					//fetch some fresh data!
					$name = $_GET['newsgroupEdit'];
					$id = $_GET['newsgroupEditID'];
					$activ = true;
					if($_GET['stare'] == 0 || $_GET['stare'] == '0')
						$activ = false;
					if(isset($_GET['createNewsgroup']) && $name != null)
					{
						//$this->p['errors'][] = 'dami-as focx 2';
						$rezultat = $this->newsgroup->create($name);
						if(!$rezultat)
							$this->p['errors'][]  = 'Eroare la crearea Newsgroup-ului cu numele $name';
						else{
							//$this->p['newsgroupMesaj'] = 'Newsgroup creat cu succes.';
						}
						$this->newsgroup->activate($rezultat, $activ);
					}
					else if(isset($_GET['saveNewsgroup']) && $name != null)
					{
						$rezultat = $this->newsgroup->rename($id, $name);
						if(!$rezultat)
							$this->p['errors'][]  = 'Eroare la salvarea Newsgroup-ului cu numele $name, id-ul $id';
						else{
							//$this->p['newsgroupMesaj'] = 'Newsgroup creat cu succes.';
						}
						$this->newsgroup->activate($id, $activ);
					}
					
				}
			}
			header('Location: /administrator/newsgroups');
		}
		$this->p['newsgroupCount'] = $this->newsgroup->getListCount();
		$this->p['newsgroups'] = $this->newsgroup->getList($pageNo);
		$this->p['pageNo'] = $pageNo;
	}
	
	function interese(){
		if($this->auth->isLoggedIn() == 'A'){
			if($_POST['interes']){
				$this->interest->addInterest($_POST['interes']);
			}
			$this->p['interese'] = $this->interest->getAllInterests();
		}
		else{
			header('Location: /statice/denied');
		}
	}
	
	function delete_interest(){
		if($this->auth->isLoggedIn() != 'A' || $_GET['interest_id'] == null){
			header('Location: /administrator/interese');
		}
		if($this->interest->existsInterest($_GET['interest_id'])){
			$this->interest->removeInterestUser($_GET['interest_id']);
			$this->interest->removeInterest($_GET['interest_id']);
		}
		header('Location: /administrator/interese');
	}
	
	function modifica_interes(){
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		if($_GET['interest_id'] == null || $this->interest->existsInterest($_GET['interest_id']) == false)
			header('Location: /administrator/interese');
		else{
			if($_POST['nume']){
				$this->interest->updateInterest($_GET['interest_id'],$_POST['nume']);
				$this->p['modif'] = true;
			}
			$_POST['nume'] = $this->interest->getInterestName($_GET['interest_id']);
		}
	}
	
	function modifica_user(){
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}
		if($_GET['user_id'] == null || $this->user->exists($_GET['user_id']) == false)
			header('Location: /administrator/users');
		$adminInfo = $this->auth->getUserData();
		$userInfo = $this->user->getInfo($_GET['user_id']);
		
		if($adminInfo['USER_ID'] != $userInfo['USER_ID']){	
			if($_POST){
				$errors = array();
				if(strlen($_POST['nume']) < 1)
					$errors[]='Nu ati completat nimic in campul numelui.';
				if(strlen($_POST['nume']) > 50)
					$errors[]='Numele nu poate avea mai mult de 50 de caractere.';
				if (!Zend_Validate::is($_POST['email'], 'EmailAddress', array( 'mx' => true ) ) )
					$errors[] = 'Emailul este incorect!';
				if($_POST['tip'] != 'U' && $_POST['tip'] != 'A' && $_POST['tip'] != 'P')
					$errors[] = 'Eroare la tipul de utilizator!';
				if (!count($errors)){
					$this->user->updateInfo($_GET['user_id'],$_POST['nume'],$_POST['email']);
					$this->user->modifyType($_GET['user_id'],$_POST['tip']);
					
					if($_POST['reset_pass'] != null){
						$result = $this->auth->requestNewPass($userInfo['USERNAME']);
						if($result){
							$this->p['action'] = 'tokenSent';
							$this->p['email'] = $result;
							}
							else
							$this->p['action'] = 'tokenSendingFail';
					}
					$this->p['modif'] = true;
				}
				else
					$this->p['errors'] = $errors;	
			}
			$userInfo = $this->user->getInfo($_GET['user_id']);
			$this->p['username'] = $userInfo['USERNAME'];
			$this->p['nume'] = $userInfo['NAME'];
			$this->p['email'] = $userInfo['E-MAIL'];
			$this->p['utype'] = $userInfo['USER_TYPE'];	
		}
		else
			$this->p['admin'] = true;
	}
	
	function modifica_newsgroup(){
		if($this->auth->isLoggedIn() != 'A'){
			header('Location: /statice/denied');
		}		
			
		$grupInfo = $this->newsgroup->getName($_GET['newsgroup_id']);
		$this->p['nume'] = $grupInfo;
		
		if($grupInfo == null )
			header('Location: /administrator/newsgroups');			
		
		
		if($_POST){
			$errors = array();			
			if(strlen($_POST['nume']) < 1)
					$errors[]='Nu ati completat nimic in campul numelui.';
			if (!count($errors)){
				$this->newsgroup->rename($_GET['newsgroup_id'],$_POST['nume']);
				$this->p['modif'] = true;
				$grupInfo = $this->newsgroup->getName($_GET['newsgroup_id']);
				$this->p['nume'] = $grupInfo;
			}					
			$this->p['nume'] = $grupInfo;
		}
	}
}