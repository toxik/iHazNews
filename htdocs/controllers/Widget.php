<?php
class Controller_Widget extends Controller_Abstract {
	protected $current_site_id;
	protected $website;
	protected $redirect;
	
	function init() {
		$this->redirect = $_SERVER['HTTP_REFERER'];
		if (substr($this->redirect, 7, strlen($_SERVER['SERVER_NAME']) ) == $_SERVER['SERVER_NAME'])
			$this->redirect = rawurlencode($_GET['site']);
		else
			$this->redirect = urlencode(rawurlencode($_SERVER['HTTP_REFERER']));
		
		$this->p['site'] = rawurldecode(urldecode($this->redirect));
		
		$this->p['single'] = 1;
		if (!$this->u && ACTION != 'login')
			redirect('/widget/login/site/' . $this->redirect );
	}
	
	function index() {
		// verificam daca avem un user si nu furnizor sau admin
		if($this->u['USER_TYPE'] != 'U')
			redirect( '/widget/wrongTypeDude/site/' . $this->redirect );
		
		// verificam daca site-ul curent e OK sau terminam actiunea widgetului.
		$this->website = new Model_Website;
		$this->p['website_id'] = $this->current_site_id = 
					$this->website->getIDbyURL( rawurldecode(urldecode($this->redirect)) );
		if ( ! $this->website->isActive( $this->current_site_id ) )
			redirect( '/widget/siteUnavailable/site/' . $this->redirect );
			
		// suntem tot aici ?? bun
		$u = new Model_User;
		$this->p['abonat'] = $u->checkSubscribed( $this->current_site_id );
		$this->p['rating'] = $this->website->getOwnRating($this->p['website_id']);
		
	}
	
	function wrongTypeDude()   { 
		if (!$this->u['USER_ID'])
			redirect('/widget/login');
		// daca e furnizor, si e pe pagina de widget preview il lasam sa vada un preview
		if ($this->u['USER_TYPE'] == 'P' && 
				substr($this->p['site'], 7, strlen($_SERVER['SERVER_NAME']) ) 
					== $_SERVER['SERVER_NAME'] )
			redirect('/widget/preview');
		header('HTTP/1.1 403 Forbidden'); 
	}
	function siteUnavailable() { header('HTTP/1.1 403 Forbidden'); } // just view, no code.
	function preview() { if (!$this->u['USER_ID']) redirect('/widget/login'); } // just view, no code
	
	function toggleSubscription() {
		if (!$this->u['USER_ID'])
			redirect('/widget/login');
		if($_POST) {
			$u = new Model_User;
			if ($_POST['subscribe']) {
				if (!$u->checkSubscribed( $_POST['website_id'] ))
					$u->abonareNewsletter( $this->u['USER_ID'], $_POST['website_id'] );
				echo '1';
			} else {
				$u->removeSubscription( $_POST['website_id'] );
				echo 0;
			}
		}
		exit;
	}
	
	function sendEmail() {
		if (!$this->u['USER_ID'])
			redirect('/widget/login');
		if($_POST) {
			if (!Zend_Validate::is($_POST['for'], 'EmailAddress', array( 'mx' => true ) ) )
				$errors[] = 'for';
			if (!$_POST['link']) 
				$errors[] = 'link';
			$this->p['errors'] = $errors;
			
			if (!count($errors)) {
				// trimite mailul si inregistreaza-l in baza de date
				if (!$_POST['nume_porecla'])
					$_POST['nume_porecla'] = $this->u['E-MAIL'];
				
				$mail = new Zend_Mail('UTF-8');
				$mail->addTo($_POST['for']);
				$mail->setReplyTo($this->u['E-MAIL'], $_POST['nume_porecla']);
				$mail->setSubject('Recomandare prin iHazNews.com de la ' . $_POST['nume_porecla']);
				$mail->setBodyText('Buna ziua, '."\r\n\r\n".'V-a fost recomandat urmatorul articol de catre '.
						$_POST['nume_porecla'].' : ' 
						. "\r\n\r\n\r\n" . $_POST['link']  . 
						($_POST['mesaj'] ? "\r\n\r\n" . 'V-a fost lasat si un mesaj:' . "\r\n" . $_POST['mesaj'] : '' ) . 
						"\r\n\r\n\r\n" . 'Toate cele bune,' . "\r\n" . 'Echipa iHazNews - http://ihaznews.com'
				);
				$mail->send();
				
				$this->d->insert('P_WEBSITES_RECOMMENDATIONS', array(
						'USER_ID' 			=> $this->u['USER_ID'],
						'URL'				=> $_POST['link'],
						'EMAIL_DESTINATAR'	=> $_POST['for']
					)
				);
			}
		}
	}
	
	function logout() {
		$auth = new Model_Auth;
		$auth->logout();
		//Zend_Debug::dump($_GET);
		redirect('/widget/login/site/' . $this->redirect );
	}
	
	function login() {
		if (substr($_SERVER['HTTP_REFERER'], 0, 32) == 'http://ihaznews.com/widget/login')
			$this->p['fail'] = true;
		if ($_POST) {
			$auth = new Model_Auth;
			if (!$auth = $auth->login($_POST['user'], $_POST['pass']))
				redirect('/widget/login/site/' . $this->redirect);
			else
				redirect('/widget/index/site/' . $this->redirect);
		}
		
	}
	
}