<?php 
class Model_Interest extends Model_Abstract {
	private $variabila = '';
	
	function init() {
	}
	
	function getAllInterests(){
		$interest = $this->db->fetchAll('SELECT * FROM LK_INTERESTS');
		return $interest;
	}
	
	function checkUserInterests($interest_id){
		$interest = $this->db->fetchAll('SELECT 1 FROM MAP_USER_INTERESTS WHERE USER_ID = ? AND INTEREST_ID = ?',array($_SESSION['auth']['USER_ID'],$interest_id));
		if($interest)
			return true;
		return false;
	}
	
	function deleteUserInterest($interest_id){
		$this->db->delete('MAP_USER_INTERESTS','USER_ID = '.$_SESSION['auth']['USER_ID'].' AND INTEREST_ID = '.$interest_id);
	}
	
	function addUserInterest($interest_id){
		$int = array(
			'USER_ID' => $_SESSION['auth']['USER_ID'],
			'INTEREST_ID' => $interest_id
		);
		$this->db->insert('MAP_USER_INTERESTS',$int);
	}
	
	function addInterest($name){
		$int = array( 'NAME' => $name );
		$this->db->insert('LK_INTERESTS',$int);
	}
	
	function existsInterest($id){
		if($this->db->fetchOne('SELECT 1 FROM LK_INTERESTS WHERE ID = ?',$id) != null) return true;
		return false;
	}
	
	function removeInterest($id){
		$this->db->delete('LK_INTERESTS','ID = '.$id);
	}
	
	function removeInterestUser($id){
		$this->db->delete('MAP_USER_INTERESTS','INTEREST_ID = '.$id);
	}
	
	function updateInterest($id,$name){
		$int = array( 'NAME' => $name );
		$this->db->update('LK_INTERESTS',$int,'ID = '.$id);
	}
	
	function getInterestName($id){
		return $this->db->fetchOne('SELECT NAME FROM LK_INTERESTS WHERE ID = ?', $id);
	}
}