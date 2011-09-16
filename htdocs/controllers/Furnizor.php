<?php
class Controller_Furnizor extends Controller_Abstract {
    private $m,$rezultat,$pageNo=1;
    private $statisticsMgr;
    private $website;
	private $user_2;
	
	function init() {
		$_SESSION['user_type'] = 'furnizor';
		$this->p['cssf'][] = 'furnizor';
		$this->p['cssf'][] = 'siteMng';	
		$this->p['jssf'][] = 'jquery';	
		$this->m = new Model_Furnizor();
		$this->auth = new Model_Auth();
		$this->statisticsMgr = new Model_StatisticsMgr();
		$this->website = new Model_Website();
		$this->user_2 = new Model_User();
		$user = $this->auth->getUserData();
		$userid = $user['USER_ID'];
		$rezultat = $this->m->isAdminProvider($userid);//verifica daca user-ul curent este admin sau provider
		if($rezultat != 'A' && $rezultat != 'P')
		{
		 if($rezultat == 'U')
		 {
		  redirect('/'); // daca un user incearca sa acceseze pagina providerului atunci el este redirectionat automat la pagina principala
		 }
		 else
		 {
		 redirect('/auth/login');
		 }
		}
	}
	
	function index() {
	
	
	}
	
	function siteMng() {
		
		$user = $this->auth->getUserData();	
		$userid = $user['USER_ID'];
		$this->p['USER_ID'] = $userid;
		
		
 	    $regex = "((https?|ftp)\:\/\/)?"; 
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; 
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; 
		$regex .= "(\:[0-9]{2,5})?"; 
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; 
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; 
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; 
	
	if(isset($_POST['siteName'])&& isset($_POST['urlName']))
	{
	 if( trim($_POST['siteName'])!= "" && trim($_POST['urlName']) != "")
	 { // verificare daca ambele campuri sunt completate
	 $this->p['valid'] = preg_match("/^$regex$/", $_POST['urlName']);
    if(preg_match("/^$regex$/", $_POST['urlName']))//url valid
	   {	    
		 $user = $this->auth->getUserData();	//obtine user curent
		 $userid = $user['USER_ID'];
		
		
		 $this->p['adaugare'] = $this->m->adaugare(trim($_POST['siteName']),trim($_POST['urlName']),$userid);
	   }
 
	 }
	}
		$this->p['rez'] = $this->m->listare($userid); // pt USERUL LOGAT!
	}
	
	function statistics() {

		
		$this->p['jsf'][] = 'jquery.flot';
		$this->p['cssf'][] = 'tabs';
		
		$this->p['websitesSelect'] = $this->website->getListForProvider2($this->auth->getUserId());
		//$_GET['name'] devine id-ul website-ului
		if(isset($_GET['date']) && isset($_GET['name'])){ 
			if($this->m->isProviderToWebsite($_GET['name'],$this->auth->getUserId())){
			
				$aryRange=array();
				$enddate = $_GET['dateend'];
			
			
				//parcurg datele dintre start si end si fac query-uri pt ca imi trebuie valori si pt zilele in care nu e nici o inregistrare in tabel
				$start_date = $_GET['date']; 
				$check_date = $start_date; 
				$end_date = $_GET['dateend'];
				$nume = $_GET['name'];
				$i = 0;
				$ch = array();
				$ch[$i] = array(
					"data" => strtotime($check_date)*1000,
					"unic" => $this->statisticsMgr->getUniqueVisits2($nume, $check_date),
					"tota" => $this->statisticsMgr->getTotalVisits2($nume, $check_date),
					);
				while ($check_date != $end_date) { 
					$check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date))); 
					$i++; 
					$ch[$i] = array(
					"data" => strtotime($check_date)*1000,
					"unic" => $this->statisticsMgr->getUniqueVisits2($nume, $check_date),
					"tota" => $this->statisticsMgr->getTotalVisits2($nume, $check_date),
					);
				}  							
			
				$this->p['val'] = $ch;
				
				$this->p['dateRanges'] = $aryRange; //vectorul cu datele de pus in grafic
				$utilUnic = array();
				$utilTotal = array();
				foreach($aryRange as $dataCurenta) {
					
					$unic = $this->statisticsMgr->getUniqueVisits($nume, $dataCurenta);
					$tota = $this->statisticsMgr->getTotalVisits($nume, $dataCurenta);
					array_push($utilUnic, $unic);
					array_push($utilTotal, $tota);
				}
			
				$this->p['uniqueVisits'] = $utilUnic; //vectorul cu setul 1 de numere corespunzatoare datelor de pus in grafic
				$this->p['totalVisits'] = $utilTotal; //vectorul cu setul 2 de numere corespunzatoare datelor de pus in grafic
				$this->p['ratings'] = $this->statisticsMgr->getAverageRatings($this->auth->getUserId());
				$this->p['subs'] = $this->statisticsMgr->getSubscribers($this->auth->getUserId());
				
				$this->p['ok'] = true;
			}
			else redirect('/furnizor/statistics');
		
		} //end processing if
	}
	
	function widgetCreation() {
		
	}
	function modificaStatus(){
	
	if(isset($_GET['wid'])){ 
	 $status = $this->m->getActiv($_GET['wid']);//obtine statusul website-ului 
	 $this->p['status'] = $this->m->schimbaStatus($_GET['wid'],$status);
	 redirect('/furnizor/siteMng');
	 }
	}
	function modificare(){	
	
	if($this->website->exists($_GET['wid']) == false)
		redirect('/furnizor/siteMng');
	
	
	
	$user = $this->auth->getUserData();	
	$userid = $user['USER_ID'];
	$rezultat = $this->m->isAdminProvider($userid);
	$usrid = $this->m->isValidUser($_GET['wid']);
	$regex = "((https?|ftp)\:\/\/)?"; 
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; 
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; 
    $regex .= "(\:[0-9]{2,5})?"; 
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; 
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; 
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; 
	
	
	
	if($this->auth->getUserId() != $_GET['userid'] || $this->m->isValidWebsite($_GET['wid']) == false){
		if($rezultat == 'A')
			{redirect('/administrator/websites');}
		else
			{redirect('/furnizor/siteMng/');}
	}
	
     $this->p['reg'] = $regex;
	if($usrid == $userid || $rezultat == 'A')// daca se incearca modificarea id-ului website de un alt provider 
											//atunci va fi redirectat catre pagina proprie de administrare site, doar admin are voie sa vada toate intrarile
	{
		$this->p['url'] = $this->m->getUrl($_GET['wid']);
		$this->p['on'] = $this->m->getActiv($_GET['wid']);
		
		if(isset($_POST['urlName'])&&($_POST['urlName']) != ""&&(trim($_POST['urlName']) != "")){
		if(preg_match("/^$regex$/", $_POST['urlName'])){
	        $this->p['editare'] = $this->m->modificare(trim($_POST['urlName']),$_GET['wid'],$_POST['activare']);
			if($rezultat == 'A')
				{redirect('/administrator/websites');				}
			else
				{redirect('/furnizor/siteMng/');}
		}
		}
	}
	else{
		if($rezultat == 'A')
			{redirect('/administrator/websites');				}
		else
			{redirect('/furnizor/siteMng/');}	     
	    }
	}
	
	function manageNewsgroups()
	{
	
	 $this->p['NewsGroups'] = $this->m->getNewsgroups();
	  $k = 0;
	  $websiteid = $_GET['wid'];
	  $arraynews = $this->p['NewsGroups'];
	  $cont = 0;
	  foreach($arraynews as $val => $l)
	  {
	   $k++;
	  }
	  $arr = array();
	  for($i = 0 ;$i <= $k;$i++){
		 
		  if(isset($_POST['news'.$i])){
		      $arr[$i] =  $i+1;
			  $cont++;
		  	  
		  }
	
	}
	$this->p['test'] = $this->m->getNewsgroupsId($websiteid);
	if(isset($_POST['add'])&&$cont!=0){
	$this->p['del'] = $this->m->deleteSiteFromNewsgroup($websiteid);
	foreach($arr as $newsgroupsid){
	$this->p['addWN'] = $this->m->addToNewsGroup($websiteid,$newsgroupsid);
	}
	redirect("/furnizor/manageNewsgroups/wid/".$websiteid."/s/1");
	}
	}
}