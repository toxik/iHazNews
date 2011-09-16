<?php
// every controller will have in its $this->p the editable, global variable $page.
abstract class Controller_Abstract {
	protected $p, $s, $d;
	function __construct(&$page) {
		$this->p = &$page;
		$this->u = new Model_Auth;
		$this->u = $this->u->getUserData();
		// conectarea la baza de date.. ??? probabil se va sterge
		$dbInstance = Model_AbstractDb::getInstance();
		$this->d = $dbInstance->getDb();
		// $this->p['dblog'] = $this->d->getProfiler();
		
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_Paginator::setDefaultItemCountPerPage(RESULTS_PER_PAGE);
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('/views/general/paginator.phtml');
		$this->init();
	}
	
	// functia obligatorie a fiecarui modul
	abstract function index();
	
	function paginator(&$query, &$params, $p = null) {
		if ($p === null) 
			$p = $_GET['p'];
		$p = !empty($p) ? (int) ($p - 1) : 0;
		
		// do a "head count" - cate rezultate trebuiesc paginate
		$nrRezultate = $this->d->fetchOne('SELECT COUNT(*) as nrRezultate FROM ('.$query.') tabelBaza', $params );
		
		//$nrRezultate = 10000;
		
		$this->p['pg'] = Zend_Paginator::factory(create_array($nrRezultate));
		$this->p['pg']->setCurrentPageNumber($p+1);
		$this->p['pg'] = $this->p['pg']->getPages();
		
		$query .= ' LIMIT ?,?';
		$params[] = $p * RESULTS_PER_PAGE;
		$params[] = RESULTS_PER_PAGE;
		
		return $p;
	}
	
	function init() {
		
	}
}