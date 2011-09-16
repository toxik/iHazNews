<?php 
class Model_Auth extends Model_Abstract {
	private $cookie_timeout = 600;
	function login ($user, $pass) {
		$encryptedPass = md5($pass);
		$result = $this->db->fetchRow('SELECT USER_ID, NAME, USERNAME, `E-MAIL`, USER_TYPE, N_PERIOD, N_ARTICLE_COUNT, F_ACTIVE
						   FROM U_ACCOUNT WHERE USERNAME = ? AND PASSWORD = ? AND F_ACTIVE = "Y"',
							array (
								$user,
								$encryptedPass
								)
					         );
		if (!$result) {
			unset($_SESSION['auth']);
			return false;
		}
		else  {
			// yey, a avut succes
			$_SESSION['auth'] = $result;
			return $result;
		}
	}
	
	function isLoggedIn() {
		return $_SESSION['auth']['USER_TYPE'];
	}
	
	function getUserData() {
		return $this->isLoggedIn() ? $_SESSION['auth'] : array();
	}
	
	function getUserId() {
		$u = $this->getUserData();
		if ($u)
			return (int) $u['USER_ID'];
		else 
			return null;
	}
	
	function logout() {
		session_destroy();
		session_start();
	}

	function signUp($user, $pass, $email, $type, $nume = ''){
		$encryptedPass = md5($pass);
		$data = array(
				'NAME'	   => $nume,
				'USERNAME' => $user,
				'PASSWORD' => $encryptedPass,
				'E-MAIL' => $email,
				'USER_TYPE' => $type
				);
		
		try {
			$this -> db -> insert('U_ACCOUNT',$data);
		} catch(Zend_Db_Exception $e) {
			// inseamna ca exista deja username-ul
			return false;
		}
		$data['USER_ID'] = $this->db->lastInsertId();
		return $data;
	}
	
	function resetPass($username, $whatPass = null) {
		// parametrata atfel incat sa putem folosi si pentru parola setata manual
		if (!$whatPass)
			$randomPass = substr( str_shuffle(
					'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 10);
		else
			$randomPass = $whatPass;
		$data = array('PASSWORD' => md5($randomPass));
		$this->db->update('U_ACCOUNT', $data, 'USERNAME = \''. $username . '\'');
		$result = $this->db->fetchOne('SELECT `E-MAIL` FROM U_ACCOUNT WHERE USERNAME = ?', $username);
		
		$mail = new Zend_Mail('UTF-8');
		$mail->addTo($result);
		$mail->setSubject('Schimbare parola pe iHazNews.com');
		$mail->setBodyText('Buna ziua, '."\r\n".'Parola dvs. a fost schimbata cu: '. "\r\n\r\n" . $randomPass);
		$mail->send();
		
		return $result;
	}
	
	function processToken( $token ) {
		if ( ! strlen($token) )
			return false;
		
		if ( $username = 
				$this->db->fetchOne('SELECT USERNAME FROM U_RESET_PW WHERE '.
							'IDENTIFIER = ? AND NOW() < EXPIRY', $token)
			) {
			// stergem tokenul
			$this->db->delete('U_RESET_PW', 'USERNAME = \'' . $username . '\'');
			// resetam parola
			return $this->resetPass( $username );
		}
		return false;								
	}
	
	function requestNewPass($username){
		$this->db->query('DELETE FROM U_RESET_PW WHERE NOW() > EXPIRY');
		$result = $this->db->fetchOne('SELECT `E-MAIL` FROM U_ACCOUNT WHERE USERNAME = ?', $username);
		if(!$result)
			return false;
			else
			{
				$data = array(
						      'IDENTIFIER' => $ustring = uniqid() . time(),
							  'USERNAME' => $username, 
							  'EXPIRY' => new Zend_Db_Expr(' DATE_ADD( NOW(), INTERVAL 30 MINUTE) ')
							  );
				try {
					$this->db->insert('U_RESET_PW',$data);
				} catch (Zend_Db_Exception $e) {
					// a cerut deja inca un token de reset pass in ultima jumatate de ora
					return false;
				}
			
				$mail = new Zend_Mail('UTF-8');
				$mail->addTo($result);
				$mail->setSubject('Schimbare parola pe iHazNews.com');
				$mail->setBodyText('Buna ziua, '."\r\n\r\n".'Pentru a schimba parola dvs. intrati in maxim 30 de minute pe adresa'. "\r\n\r\n" . 
									'http://ihaznews.com/auth/processToken/token/'. $ustring . "\r\n\r\n" .
									'Va multumim, ' . "\r\n" . 'Echipa iHazNews');
				$mail->send();
				return $result; 
			}
	}
	
	function checkPassword($username, $password) {
		$password = md5($password);
		return (boolean) $this->db->fetchOne(
				'SELECT PASSWORD 
				FROM U_ACCOUNT 
				WHERE USERNAME = ? AND PASSWORD = ?' , array(
					$username, $password)
			);
	}
	
	function adminAccount($username, $newData) {
		// criptam parola cu sistemul de securitate ales, userul o da in plain text
		
		$newData['PASSWORD'] = md5($newData['PASSWORD']);
		try {
			$this->db->update('U_ACCOUNT', $newData, "USERNAME='$username'");
			return true;
		} catch (Zend_Db_Exception $e) {
			return false;
		}
	}
}
	
	