<?php 
class Model_Newsgroup extends Model_Abstract {
	private $variabila = '';
	
	function init() {
		
	}

	function getListCount(){
		$cnt = $this->db->fetchOne('SELECT count(ID) from LK_NEWSGROUPS');
		return $cnt;
	}
	
	function getList($pageNo = 1) {
		$resultsPerPage = 20;
		if($pageNo < 1)
			return false;
		$offset = ($pageNo - 1) * $resultsPerPage;
		$data = $this->db->fetchAll('SELECT * from  LK_NEWSGROUPS LIMIT ?, ?', array($offset, $resultsPerPage));
		return $data;
	}
	
	function create($name){
		$data = array( 'NAME' => $name );
		$rezultat = $this->db->insert('LK_NEWSGROUPS', $data);
		$rezultat = $this->db->fetchOne('SELECT MAX(ID) FROM LK_NEWSGROUPS WHERE NAME = ?', array($name));
		return $rezultat;
	}
	
	function activate($id, $activated = true){
		$data = array( 'VALID_TO' => date("Y-d-j H:i:s") );
		if($activated){
			$data = array( 'VALID_TO' => "" );
		}
		$rezultat = $this->db->update('LK_NEWSGROUPS', $data, "ID = $id");
		return $rezultat;
	}
	
	function rename($id, $newname){
		$data = array( 'NAME' => $newname);
		$rezultat = $this->db->update('LK_NEWSGROUPS', $data, "ID = $id");
		return $rezultat;
	}
	
	function getMessage() {
		return $this->variabila;
	}
	
	function getAll(){
		$inf = $this->db->fetchAll('SELECT * FROM LK_NEWSGROUPS');
		return $inf;
	}
	
	function exists($newsgroup_id){
		$inf = $this->db->fetchOne('SELECT 1 FROM LK_NEWSGROUPS WHERE ID = ?',$newsgroup_id);
		if($inf)
			return true;
		return false;
	}
	
	function getSubscribedNewsgroups(){
		$sites = $this->db->fetchAll('SELECT NAME, ID
										FROM LK_NEWSGROUPS JOIN MAP_USER_NEWSGROUPS ON ID = NEWSGROUP_ID
										WHERE USER_ID = ?
										ORDER BY NAME',
										$_SESSION['auth']['USER_ID']);
		return $sites;
	}
	
	function checkSubscribedNewsgroup($newsgroup_id){
		$info = $this->db->fetchOne('SELECT 1 
									FROM LK_NEWSGROUPS JOIN MAP_USER_NEWSGROUPS ON ID = NEWSGROUP_ID
									WHERE USER_ID = ? AND ID = ?',
									array($_SESSION['auth']['USER_ID'], $newsgroup_id));
		if($info) return true;
		return false;
	}
	
	function removeSubscriptionNewsgroup($newsgroup_id){
		$this->db->delete('MAP_USER_NEWSGROUPS','USER_ID = '.$_SESSION['auth']['USER_ID'].' AND NEWSGROUP_ID = '. $newsgroup_id);
	}
	
	function addSubscriptionNewsgroup($newsgroup_id){
		$info = array(
			'USER_ID' => $_SESSION['auth']['USER_ID'],
			'NEWSGROUP_ID' => $newsgroup_id
		);
		$this->db->insert('MAP_USER_NEWSGROUPS',$info);
	}
	
	function getNewsgroupsAndWebsites(){
		$inf = $this->db->fetchAll('SELECT WEBSITE_ID, ID, NAME FROM LK_NEWSGROUPS JOIN MAP_WEBSITES_NEWSGROUPS	ON(ID = NEWSGROUP_ID) ORDER BY 1,2');
		return $inf;
	}
	
	function getName($newsgroup_id){
		return $this->db->fetchOne('SELECT NAME FROM LK_NEWSGROUPS WHERE ID = ?',$newsgroup_id);
	}
	
}