<?php
 class Model_Furnizor extends Model_Abstract{
  function init()
  {
  
  }
  function isAdminProvider($userid){//returneaza tipul utilizatorului
   $type = $this->db->fetchOne('SELECT USER_TYPE from U_ACCOUNT WHERE USER_ID = ? ', $userid);
	return $type;
  }
  function getActiv($websiteid)//returneaza status website
  {
   $data = $this->db->fetchOne('SELECT F_ACTIVE FROM P_WEBSITES WHERE WEBSITE_ID = '.$websiteid);
	 return $data;
  }
  function isValidUser($websiteid){//returneaza id-ul userului care a adaugat website-ul
   $data = $this->db->fetchOne('SELECT ADDED_BY FROM P_WEBSITES WHERE WEBSITE_ID = '.$websiteid);
   return $data;
  }
  
  function isValidWebsite($websiteid){
	$data = $this->db->fetchOne('SELECT 1 FROM P_WEBSITES WHERE WEBSITE_ID = '.$websiteid);
	if ($data == null)
		return false;
	else 
		return true;
  }
   function listare($userid){ // listeaza website-uri
	
		$type = $this->db->fetchOne('SELECT USER_TYPE from U_ACCOUNT WHERE USER_ID = ? ', $userid);//returneaza tipul utilizatorului
		if($type == 'A')//daca e admin vede toate intrarile din tabel
		{
		 $data = $this->db->fetchAll('SELECT * from P_WEBSITES ');
		return $data;
		}
		else//daca e provider vede doar intrarile lui
		{
		$data = $this->db->fetchAll('SELECT * from P_WEBSITES WHERE ADDED_BY = ? ' , array($userid)); 
		return $data;	
		}
	}
	
	function adaugare($sitename,$url,$userid){ // inserarea unui nou website in tabel
	$data = array('URL' => $url, 'F_ACTIVE' => 'Y','SITE_NAME' => $sitename,'ADDED_BY' => $userid);		 
	 $rezultat = $this->db->insert("P_WEBSITES",$data);
	 return $rezultat;
	}
	
	function modificare($url,$websiteid,$status){ // se modifica url-ul si statusul 
	 
	 if($status == 'Y'){
			$data = array( 'URL' => $url,'F_ACTIVE' => 'Y' );
		}
	else
	    {
			$data = array('URL' => $url,'F_ACTIVE' => 'N');
		}		
	 $rezultat = $this->db->update("P_WEBSITES",$data,"WEBSITE_ID = ".(int)$websiteid) ; 
	 return $rezultat;
	}
	
	function getUrl($websiteid)//returneaza url corespunzator websiteid
	{
	 $data = $this->db->fetchOne('SELECT URL FROM P_WEBSITES WHERE WEBSITE_ID = '.(int)$websiteid);
	 return $data;
	}
	function getWid($url)
	{
	 $data = $this->db->fetchOne("SELECT WEBSITE_ID FROM P_WEBSITES WHERE URL LIKE '%".$url."%'");
	 return $data;
	 
	}
	function schimbaStatus($websiteid, $status){ //activare sau dezactivare website

	
		if($status == 'Y'){
			$data = array( 'F_ACTIVE' => 'N' );
		}
		else
		{
		 $data = array( 'F_ACTIVE' => 'Y' );
		}
		$rezultat = $this->db->update('P_WEBSITES', $data, "WEBSITE_ID = ".(int)$websiteid);
		return $rezultat;

	
	}
	function getNewsgroups()
	{
	 $rezultat = $this->db->fetchAll("SELECT * FROM LK_NEWSGROUPS");
	 return $rezultat;
	}
	function deleteSiteFromNewsgroup($websiteid)
	{
	 $rez = $this->db->delete('MAP_WEBSITES_NEWSGROUPS',array('WEBSITE_ID = ?' => $websiteid));
	 return $rez;
	}
	function addToNewsGroup($websiteid,$newsgroupsid)
	{
	 $data = array('WEBSITE_ID' => $websiteid,'NEWSGROUP_ID' => $newsgroupsid) ;
	 $rezultat = $this->db->insert('MAP_WEBSITES_NEWSGROUPS',$data);
	 return $rezultat;
	}
	function getNewsgroupsId($websiteid)
	{
	 $data = $this->db->fetchAll("SELECT NEWSGROUP_ID FROM MAP_WEBSITES_NEWSGROUPS WHERE WEBSITE_ID = ".$websiteid);
	 return $data;
	}
	function checkSubscribed($website_id){
		$info = $this->db->fetchOne('SELECT 1 
									FROM P_WEBSITES JOIN MAP_USER_WEBSITES USING(WEBSITE_ID)
									WHERE USER_ID = ? AND WEBSITE_ID = ?',
									array($_SESSION['auth']['USER_ID'], $website_id));
		if($info) return true;
		return false;
	}
	
	function isProviderToWebsite($website_id,$userid){
		if($this->db->fetchOne('SELECT 1 FROM P_WEBSITES WHERE WEBSITE_ID = ? AND ADDED_BY = ?',array($website_id,$userid))) 
			return true;
		return false;
	}
	
}
	
 
 
