<?php
class Model_Abstract {
	protected $db;
	function __construct() {
		$dbInstance = Model_AbstractDb::getInstance();
		$this->db = $dbInstance->getDb();
		$this->init();
	}
	
	function init() {
		
	}
}