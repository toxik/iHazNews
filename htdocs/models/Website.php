<?php 
class Model_Website extends Model_Abstract {
	private $variabila = '';
	
	function init() {
	}

	function getListForProvider($userid){
		$data = $this->db->fetchAll('SELECT URL FROM P_WEBSITES WHERE ADDED_BY = ?',array($userid));
		return $data;
	}
	
	function getListForProvider2($userid){
		$data = $this->db->fetchAll('SELECT WEBSITE_ID, URL FROM P_WEBSITES WHERE ADDED_BY = ?',array($userid));
		return $data;
	}

	function getListCount(){
		$cnt = $this->db->fetchOne('SELECT count(WEBSITE_ID) from P_WEBSITES');
		return $cnt;
	}
	
	function getList($pageNo = 1) {
		$resultsPerPage = 20;
		if($pageNo < 1)
			return false;
		$offset = ($pageNo - 1) * $resultsPerPage;
		$data = $this->db->fetchAll('SELECT W.WEBSITE_ID, W.URL, W.F_ACTIVE, W.U_RATING_AVG, W.U_NB_RATINGS, U.USERNAME as ADDED_BY from P_WEBSITES W, U_ACCOUNT U WHERE W.ADDED_BY = U.USER_ID LIMIT ?, ?', array($offset, $resultsPerPage));
		return $data;
	}
	
	function enable($websiteid, $enabled = true){
		$websiteid = (int) $websiteid;
		$data = array( 'F_ACTIVE' => 'N' );
		if($enabled){
			$data = array( 'F_ACTIVE' => 'Y' );
		}
		$rezultat = $this->db->update('P_WEBSITES', $data, "WEBSITE_ID = $websiteid");
		return $rezultat;
	}
	
	function update( $data, $id = null ) {
		try {
			if ($int = (int) $id) 
				$this->db->update('Sporturi', $data, 'sport_id = '. $id);
			else {
				unset($data['sport_id']);
				$this->db->insert('Sporturi', $data);
			}
			return true;
		} catch(Zend_Db_Exception $e) {
			return false;
		}
	}
	
	function getMessage() {
		return $this->variabila;
	}
	
	function getIDbyURL($url) {
		return $this->db->fetchOne('
			SELECT WEBSITE_ID 
			FROM P_WEBSITES 
			WHERE LEFT( ? , LENGTH(URL)) = URL', $url
		);
	}
	
	function isActive( $id ) {
		return $this->db->fetchOne('SELECT F_ACTIVE FROM P_WEBSITES WHERE WEBSITE_ID = ?', (int) $id)
				== 'Y' ? true : false;
	}
	
	function getSubscribedWebsites(){
		$sites = $this->db->fetchAll('SELECT WEBSITE_ID AS ID, SITE_NAME, URL, U_RATING_AVG, F_ACTIVE
										FROM P_WEBSITES JOIN MAP_USER_WEBSITES USING(WEBSITE_ID)
										WHERE USER_ID = ?
										ORDER BY SITE_NAME',
										$_SESSION['auth']['USER_ID']);
		return $sites;
	}
	
	function getOwnRating($website_id){
		$sites = $this->db->fetchOne('SELECT COALESCE(RATING,0)
										FROM U_RATINGS
										WHERE USER_ID = ? AND WEBSITE_ID = ?',
										array($_SESSION['auth']['USER_ID'], $website_id));
		return $sites;
	}
	
	function hasUserRating($website_id){
		$info = $this->db->fetchOne('SELECT 1 FROM U_RATINGS
									WHERE USER_ID = ? AND WEBSITE_ID = ?',
									array($_SESSION['auth']['USER_ID'], $website_id));
		if($info) 
			return true;
		return false;
	}
	
	function updateUserRating($website_id, $rating){
		$upd = array(
			'RATING' => $rating
		);
		$this->db->update('U_RATINGS', $upd, 'USER_ID = '.$_SESSION['auth']['USER_ID'].' AND WEBSITE_ID = '.$website_id);
	}
	
	function addUserRating($website_id, $rating){
		$add = array(
			'USER_ID' => $_SESSION['auth']['USER_ID'],
			'WEBSITE_ID' => $website_id,
			'RATING' => $rating
		);
		$this->db->insert('U_RATINGS',$add);
	}
	
	function deleteUserRating($website_id){
		$this->db->delete('U_RATINGS','USER_ID = '.$_SESSION['auth']['USER_ID'].' AND WEBSITE_ID = '.$website_id);
	}
	
	function getWebsitesInNewsgroup($newsgroup_id){
		$inf = $this->db->fetchAll('SELECT WEBSITE_ID, SITE_NAME, URL
									FROM P_WEBSITES JOIN MAP_WEBSITES_NEWSGROUPS 
									USING(WEBSITE_ID) 
									WHERE NEWSGROUP_ID = ?',
									$newsgroup_id);
		return $inf;
	}
	
	function activateUserWebsites($userid, $active){
		if($active)
			$this->db->update('P_WEBSITES',array('F_ACTIVE' => 'Y'),'ADDED_BY = '.$userid);
		else
			$this->db->update('P_WEBSITES',array('F_ACTIVE' => 'N'),'ADDED_BY = '.$userid);
	}
	
	function exists($websiteid){
		$ex = $this->db->fetchOne('SELECT 1 FROM P_WEBSITES WHERE WEBSITE_ID = ?',$websiteid);
		if($ex) return true;
		return false;
	}
}