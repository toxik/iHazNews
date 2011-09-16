<?php
class Controller_Auth extends Controller_Abstract {
	private $auth;
	private $interest;

	function init() {
		$this->p['cssf'][] = 'auth';
		$this->auth = new Model_Auth();
		$this->interest = new Model_Interest();
	}
	
	function index() {
		// i DUNOOO
		if ($this->auth->isLoggedIn())
			redirect('/');
		else
			redirect('/auth/login');
	}
	
	function login() {
		if($_POST) 
		{
			$result = $this->auth->login($_POST['user'], $_POST['pass']);
			if($result)
				// force redirect to prevent re-send form on back press
				//redirect('/auth/login');
				if($this->auth->isLoggedIn() == 'A')
					redirect('administrator/index');
				else if($this->auth->isLoggedIn() == 'P')
					redirect('furnizor/index');
				else
					redirect('user/index');
			else
				$this->p['action'] = 'logUnsuccessful';
		}
		else if (!$this->auth->isLoggedIn())
			$this->p['action'] = 'noAction';
	}
	
	function register() {
	
		$int = $this->interest->getAllInterests();
		$this->p['interese'] = $this->interest->getAllInterests();
		$this->p['nr'] = count($this->p['interese']);
		if($_POST) {
			$errors = array();
			// verificare de email
			if (strlen($_POST['user']) < 3 || strlen($_POST['user']) > 50)
				$errors[] = 'Numele de utilizator trebuie sa aiba intre 3 si 50 de caractere!';
			if (!Zend_Validate::is($_POST['email'], 'EmailAddress', array( 'mx' => true ) ) )
				$errors[] = 'Emailul este incorect!';
			if (!strlen($_POST['pass'])) 
				$errors[] = 'Parola nu poate fi goala';
			if ($_POST['pass2'] != $_POST['pass']) 
				$errors[] = 'Parolele nu coincid';
			if (!in_array($_POST['type'], array('U', 'P')))
				$errors[] = 'Nu puteti fii decat Utilizator sau Provider';
			if(count($_POST['opt']) == 0)
				$errors[]='Trebuie selectat macar un interes';
			if($_POST['tos'] == null)
				$errors[]='Trebuie sa acceptati Terms of Service';
			
			if (!count($errors))
				if ( $this->auth->signUp($_POST['user'], $_POST['pass'], $_POST['email'], 
											$_POST['type'], $_POST['name'] ) ) {
					$this->auth->login($_POST['user'], $_POST['pass']);
					foreach($int as $i){
						if(in_array($i['ID'], $_POST['opt']))
								$this->interest->addUserInterest($i['ID']);
					}
					redirect('/');
				}
				else
					$errors[] = 'Numele de utilizator este deja folosit, va rugam alegeti un altul';
			$this->p['errors'] = $errors;
		}
		
	}
	
	function requestNewPass() {
		if($_POST)
		{
			$result = $this->auth->requestNewPass($_POST['username']);
			if($result){
				$this->p['action'] = 'tokenSent';
				$this->p['email'] = $result;
				}
				else
				$this->p['action'] = 'tokenSendingFail';
		}
		else 
		$this->p['action'] = 'noAction';
	}
	
	function processToken() {
		if($_GET['token'])
		{
			$result = $this->auth->processToken($_GET['token']);
			if($result) {
				$this->p['action'] = 'tokenPros';
				$this->p['email'] = $result;
			}
			else
				$this->p['action'] = 'tokenProsFail';
		}
		else
		$this->p['action'] = 'noAction';
	}
	
	function logout() {
		$this->auth->logout();
		// se apeleaza modelul si apoi redirect la prima pagina
	}
}