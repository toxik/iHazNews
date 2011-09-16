<?php
class Controller_User extends Controller_Abstract {
	private $auth;
	private $user;
	private $newsgroup;
	private $interest;
	private $website;
	function init() {
		$_SESSION['user_type'] = 'user';
		$this->p['cssf'][] = 'furnizor';
		$this->p['cssf'][] = 'account';
		$this->p['cssf'][] = 'user';
		$this->p['cssf'][] = 'jquery.rating';
		$this->p['jsf'][] = 'jquery.uitablefilter';
		$this->p['jsf'][] = 'jquery.MetaData';
		$this->p['jsf'][] = 'jquery.rating';
		$this->p['jsf'][] = 'user';
		$this->auth = new Model_Auth();
		$this->user = new Model_User();
		$this->newsgroup = new Model_Newsgroup();
		$this->interest = new Model_Interest();
		$this->website = new Model_Website();
	}
	
	function index() {
		if($this->auth->getUserId() == null )		
			{redirect('/statice/denied');}
	}
	
	function account() {
		$userInfo = $this->auth->getUserData();
		$userName = $userInfo['USERNAME'];
		$userNume = $userInfo['NAME'];
		$userEmail = $userInfo['E-MAIL'];
		
		if($this->auth->getUserId() == null )		
			{redirect('/statice/denied');}
			
		if($_POST)
		{
			$errors = array();
			if(strlen($_POST['nume']) < 1)
				$errors[]='Nu ati completat nimic in campul numelui.';
			if(strlen($_POST['nume']) > 50)
				$errors[]='Numele nu poate avea mai mult de 50 de caractere.';
			if (!Zend_Validate::is($_POST['email'], 'EmailAddress', array( 'mx' => true ) ) )
				$errors[] = 'Emailul este incorect!';
			if (!$this->auth->checkPassword($userName,$_POST['oldPass'])) 
				$errors[] = 'Parola veche nu e corecta.';
			if ($_POST['newPass1'] != $_POST['newPass2']) 
				$errors[] = 'Parolele nu coincid';
				
			if (!count($errors))
			{
				$this->p['action'] = 'succes';
				$newData = array(
								 'NAME'=>$_POST['nume'],
								 'E-MAIL'=>$_POST['email']
						);
				if(strlen($_POST['newPass1']))
					$newData['PASSWORD'] = $_POST['newPass1'];
				else
					$newData['PASSWORD'] = $_POST['oldPass'];
				if ($this->auth->adminAccount($userName, $newData))
					$this->auth->login($userName, $newData['PASSWORD']);
					
				else
					$errors[] = 'Eroare la salvarea datelor!';
			}
			$this->p['errors'] = $errors;
			
			
		} else {
			$_POST['nume'] = $userNume;
			$_POST['email'] = $userEmail;
		}	
		
	}
	
	function newsletter() {	
		if($this->auth->isLoggedIn() == 'U'){
			$this->p['sites'] = $this->website->getSubscribedWebsites();
			$this->p['rate'] = array();
			foreach($this->p['sites'] as $ss)
				$this->p['rate'][$ss['ID']] = $this->website->getOwnRating($ss['ID']);
			
			$this->p['test'] = array();
			if($_POST['sett']){
				$this->user->updateSettings($_POST['fvn'],$_POST['nrart'],$_POST['nrsg']);
				$this->p['modif'] = true;
			}

			$this->p['settings'] = $this->user->getSettings();
		}
		else{
			$this->p['acc_denied'] = true;
		}
	}
	
	function rate(){
		if($this->auth->isLoggedIn() == 'U')
			if($_GET['website_id'] != null && $this->user->checkSubscribed($_GET['website_id']))
				if($_GET['rating'] != null){
					if(is_numeric($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 6)
						if($this->website->hasUserRating($_GET['website_id']))
							$this->website->updateUserRating($_GET['website_id'], $_GET['rating']);
						else 
							$this->website->addUserRating($_GET['website_id'], $_GET['rating']);
				}
				else if($this->website->hasUserRating($_GET['website_id']))
						$this->website->deleteUserRating($_GET['website_id']);
		
		header('Location: /user/newsletter');
	}
	
	function delete_subscription(){
		if($this->auth->isLoggedIn() != 'U' || $_GET['website_id'] == null)
			header('Location: /user/newsletter');
		if($this->user->checkSubscribed($_GET['website_id'])){
			$this->user->removeSubscription($_GET['website_id']);
		}
		header('Location: /user/newsletter');
	}
	
	function newsgroups() {
		if($this->auth->isLoggedIn() == 'U'){
			$this->p['subs_ngr'] = $this->newsgroup->getSubscribedNewsgroups();
			$this->p['ngr'] = $this->newsgroup->getAll();
			foreach($this->p['ngr'] as $ngr){
				$this->p['subscribed'][$ngr['ID']] = $this->newsgroup->checkSubscribedNewsgroup($ngr['ID']);
			}
		}
		else{
			$this->p['acc_denied'] = true;
		}		
	}
	
	function newsgroup_info(){
		if($this->auth->isLoggedIn() != 'U')
			header('Location: /statice/denied');
		if($_GET['newsgroup_id'] == null || $this->newsgroup->exists($_GET['newsgroup_id']) == false)
			header('Location: /user/newsgroups');
		else{
			$this->p['subscribed'] = $this->newsgroup->checkSubscribedNewsgroup($_GET['newsgroup_id']);
			$this->p['websites'] = $this->website->getWebsitesInNewsgroup($_GET['newsgroup_id']);
			$this->p['name'] = $this->newsgroup->getName($_GET['newsgroup_id']);
		}
	}
	
	function add_newsgroup(){
		if($this->auth->isLoggedIn() != 'U' || $_GET['newsgroup_id'] == null)
			header('Location: /user/newsgroup');
		if(!$this->newsgroup->checkSubscribedNewsgroup($_GET['newsgroup_id'])){
			$this->newsgroup->addSubscriptionNewsgroup($_GET['newsgroup_id']);
		}
		header('Location: /user/newsgroups');
	}
	
	function remove_newsgroup(){
		if($this->auth->isLoggedIn() != 'U' || $_GET['newsgroup_id'] == null || $this->newsgroup->exists($_GET['newsgroup_id']) == false)
			header('Location: /user/newsgroups');
		if($this->newsgroup->checkSubscribedNewsgroup($_GET['newsgroup_id'])){
			$this->newsgroup->removeSubscriptionNewsgroup($_GET['newsgroup_id']);
		}
		header('Location: /user/newsgroups');
	}
	
	function interese(){
		if($this->auth->isLoggedIn() == 'U'){
			$int = $this->interest->getAllInterests();
			if($_POST['opt']){
				if(count($_POST['opt']) > 0){
					foreach($int as $i){
						if(in_array($i['ID'], $_POST['opt'])){
							if($this->interest->checkUserInterests($i['ID']) == false)
								$this->interest->addUserInterest($i['ID']);
						}
						else{
							$this->interest->deleteUserInterest($i['ID']);
						}
					}
					$this->p['modif'] = true;
				}
				else
					$this->p['err'] = true;
			}
			
			$this->p['interese'] = $this->interest->getAllInterests();
			foreach($this->p['interese'] as $i)
				$this->p['checked'][$i['ID']] = $this->interest->checkUserInterests($i['ID']);
			$this->p['nr'] = count($this->p['interese']);
		}
		else{
			$this->p['acc_denied'] = true;
		}
	}
	
	function browse() {
	 if($this->auth->isLoggedIn() == 'U' || $this->auth->isLoggedIn() == 'A'){
	$user = $this->auth->getUserData();
		  $userid = $user['USER_ID'];
		  
		$site = $this->user->getSites();
		$arr = array();
		$ngr = array();
		
	    foreach($site as $siteid => $wid)
		{$rez = $this->user->getRating($wid['WEBSITE_ID']);
		$ngr[$wid['WEBSITE_ID']] = array();
		if(isset($rez)){
		 $arr[$wid['WEBSITE_ID']] = $this->user->getRating($wid['WEBSITE_ID']);}
		}
	
		$this->p['userID'] = $userid ;
		
		$this->p['rate'] = $arr;
		$this->p['isOn'] = $this->user->isAdded($userid);
		$this->p['sites'] = $this->user->getSites();
		$this->p['nwsgr'] = $this->newsgroup->getNewsgroupsAndWebsites();
	  
		foreach($this->p['nwsgr'] as $n){
			$add = array( 'nume' => $n['NAME'], 'id' => $n['ID']);
			$ngr[$n['WEBSITE_ID']][] = $add;
		}
		
		$this->p['ngr'] = $ngr;
		
		}
		else{
		 $this->p['error'] = true;
		}
		
	}
	function add(){
	 if($this->auth->isLoggedIn() == 'U' || $this->auth->isLoggedIn() == 'A'){
	  if(isset($_GET['wid'])){
	      $user = $this->auth->getUserData();
		  $userid = $user['USER_ID'];
	      $this->p['addN']= $this->user->abonareNewsletter($userid,$_GET['wid']);
          redirect('/user/browse/');
      }	  
	 }
	}
	function release(){
	 if($this->auth->isLoggedIn() == 'U' || $this->auth->isLoggedIn() == 'A'){
	  if(isset($_GET['wid'])){
	  
	      $this->p['delN']= $this->user->removeSubscription($_GET['wid']);
          redirect('/user/browse/');
	  }
	 }
	}
	function addRating(){
	 if($this->auth->isLoggedIn() == 'U'){
			if($_GET['website_id'] != null && $this->user->checkSubscribed($_GET['website_id']))
				if($_GET['rating'] != null){
					if(is_numeric($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 6)
						if($this->website->hasUserRating($_GET['website_id']))
							$this->website->updateUserRating($_GET['website_id'], $_GET['rating']);
						else 
							$this->website->addUserRating($_GET['website_id'], $_GET['rating']);
				}
				else if($this->website->hasUserRating($_GET['website_id']))
						$this->website->deleteUserRating($_GET['website_id']);
						
			redirect('/user/browse');		
		}	
	}
	 
	function lastNewsletter() {
		if($this->auth->getUserId() == null )		
			{redirect('/statice/denied');}
			
		$newsletter = $this->user->grabLastNewsletter();
		if ($newsletter) {
			echo $newsletter['CONTENT'];
			exit;
		}
		$this->p['single'] = 1;
	}
	
	function search() {
		
	}
}