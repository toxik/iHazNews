<?php 
class Model_User extends Model_Abstract {
	private $variabila = '';
	
	function init() {
	}

	function getListCount(){
		$cnt = $this->db->fetchOne('SELECT count(USER_ID) from U_ACCOUNT');
		return $cnt;
	}
	
	function getList($pageNo = 1) {
		$resultsPerPage = 20;
		if($pageNo < 1)
			return false;
		$offset = ($pageNo - 1) * $resultsPerPage;
		$data = $this->db->fetchAll('SELECT * from U_ACCOUNT LIMIT ?, ?' , array($offset,$resultsPerPage));
		return $data;
	}

	function getListFilter($user = '', $name = '') {
		$data;
		if($user != '' && $name != ''){
			$data = $this->db->fetchAll('SELECT * from U_ACCOUNT WHERE LOWER(NAME) LIKE LOWER("%?%") OR LOWER(USERNAME) LIKE LOWER("%?%")' , array($name,$user));
		}
		else if($user != ''){
			$data = $this->db->fetchAll('SELECT * from U_ACCOUNT WHERE LOWER(USERNAME) LIKE LOWER("%?%")' , array($user));
		}
		else if($name != ''){
			$data = $this->db->fetchAll('SELECT * from U_ACCOUNT WHERE LOWER(NAME) LIKE LOWER("%?%")' , array($name));
		}
		return $data;
	}
	
	function activate($userid, $activated = true) {
		$data;
		if($activated){
			$data = array( 'F_ACTIVE' => 'Y' );
		}
		else{
			$data = array( 'F_ACTIVE' => 'N' );
		}
		$rezultat = $this->db->update('U_ACCOUNT', $data, "USER_ID = $userid");
		return $rezultat;
	}
	
	function promoteAdmin($userid, $promoted = true) {
		$data = array( 'USER_TYPE' => 'U');
		if($promoted){
			$data = array( 'USER_TYPE' => 'A');
		}
		$rezultat = $this->db->update('U_ACCOUNT', $data, "USER_ID = $userid");
		return $rezultat;
	}
	
	function modifyType($userid, $type){
		$inf = array( 'USER_TYPE' => $type );
		$this->db->update('U_ACCOUNT', $inf, 'USER_ID = '.$userid);	
	}
	
	function getMessage() {
		return $this->variabila;
	}
	
	function checkSubscribed($website_id){
		return (boolean) $this->db->fetchOne('SELECT 1 
									FROM P_WEBSITES JOIN MAP_USER_WEBSITES USING(WEBSITE_ID)
									WHERE USER_ID = ? AND WEBSITE_ID = ?',
									array($_SESSION['auth']['USER_ID'], $website_id));
	}
	
	function removeSubscription($website_id){
		$this->db->delete('MAP_USER_WEBSITES','USER_ID = '.$_SESSION['auth']['USER_ID'].' AND WEBSITE_ID = '. $website_id);
	}
	
	function updateSettings($frecv, $nr_art, $nr_sugestii, $nr_rezpag = 0){
		$info = array(
			'N_PERIOD' => $frecv,
			'N_ARTICLE_COUNT' => $nr_art,
			'N_SUGGESTION_COUNT' => $nr_sugestii,
			'N_ARTICLE_PAGE' => $nr_rezpag
		);
		$this->db->update('U_ACCOUNT', $info, 'USER_ID = '.$_SESSION['auth']['USER_ID']);
	}
	
	function getSettings(){
		$info = $this->db->fetchRow('SELECT N_PERIOD, N_ARTICLE_COUNT, N_SUGGESTION_COUNT, N_ARTICLE_PAGE
									FROM U_ACCOUNT
									WHERE USER_ID = ?',
									$_SESSION['auth']['USER_ID']);
		return $info;
	}
	function getSites(){
     $data = $this->db->fetchAll("SELECT * FROM P_WEBSITES WHERE F_ACTIVE = 'Y' ");
	 return $data;
    }	
	function abonareNewsletter($userid,$websiteid)
	{
	 $data = array('USER_ID' => $userid,'WEBSITE_ID' => $websiteid);
	 $rezultat = $this->db->insert('MAP_USER_WEBSITES',$data);
	 return $rezultat;
	}
	function isAdded($userid){
	 $data = $this->db->fetchAll('SELECT WEBSITE_ID FROM MAP_USER_WEBSITES WHERE USER_ID = '.$userid);
	 return $data;
	}
	function getRating($websiteid)
	{
	 $sites = $this->db->fetchRow('SELECT AVG(COALESCE(RATING,0)) AS Medie,WEBSITE_ID 
										FROM U_RATINGS WHERE WEBSITE_ID = '.$websiteid
										);
		return $sites;
	}
	
	function exists($user_id){
		if($this->db->fetchOne('SELECT 1 FROM U_ACCOUNT WHERE USER_ID = ? ',$user_id) != null) 
			return true;
		return false;
	}
	
	function getInfo($user_id){
		$result = $this->db->fetchRow('SELECT USER_ID, NAME, USERNAME, `E-MAIL`, USER_TYPE, N_PERIOD, N_ARTICLE_COUNT, F_ACTIVE
						   FROM U_ACCOUNT WHERE USER_ID = ? ',
							$user_id
					         );
		return $result;
	}
	
	function updateInfo($userid,$nume,$email){
		$modif = array(
			'NAME' => $nume,
			'E-MAIL' => $email
		);
		$this->db->update('U_ACCOUNT', $modif, 'USER_ID = '.$userid);
	}
	
	// functie care verifica ultimul newsletter trimis pentru fiecare user si ii intoarce
	// pe cei activi si care au "nevoie" de newsletter conform setarilor lor
	function getAllActiveUsersForNewsletter() {
		return $this->db->fetchAll(
			"SELECT USER_ID as id, `E-MAIL` as e FROM U_ACCOUNT U WHERE F_ACTIVE = 'Y' AND USER_TYPE = 'U' 
				AND ( DATEDIFF(NOW(), ( 
					SELECT DATE
					FROM U_NEWSLETTERS
					WHERE USER_ID = U.USER_ID
					ORDER BY DATE DESC
					LIMIT 1 
				)) >= N_PERIOD 
				OR NOT EXISTS ( SELECT DATE FROM U_NEWSLETTERS 	WHERE USER_ID = U.USER_ID )
			)"
		);
	}
	
	function grabLastNewsletter() {
		return $this->db->fetchRow(
			'SELECT * FROM U_NEWSLETTERS WHERE USER_ID = ? ORDER BY NWS_ID DESC LIMIT 1', 
				$_SESSION['auth']['USER_ID']
		);
	}
	
}