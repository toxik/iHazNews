<?php
class Controller_Home extends Controller_Abstract {
	function init() {
		unset($_SESSION['user_type']);
	}
	
	function index() {
		$this->p['content'] = new Model_User();
		$this->p['content'] = $this->p['content']->getMessage();		
	}
}