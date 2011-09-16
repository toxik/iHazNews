<?php
class Model_AbstractDb {
	private static $instance;
	private $db;
	private function __construct() {
		try {
			$this->db = Zend_Db::factory('Mysqli', 
				array(
					'host'     => DB_HOST,
					'username' => DB_USER,
					'password' => DB_PASS,
					'dbname'   => DB_DATABASE,
					/*'persistent' => true,
					'profiler' => 
						array (
							'enabled' 	=> false, // securitate nema daca e TRUE
							'class' 	=> 'Zend_Db_Profiler_Firebug'
						)
					*/
				)
			);
		} catch (Zend_Db_Adapter_Exception $e) {
			exit('Nu m-am putut conecta la baza de date. ');
		} catch (Zend_Exception $e) {
			exit('Eroare interna. Contactati administratorul aplicatiei!');
		}
	}
	
	function getInstance() {
		if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
	}
	
	function getDb() {
		return $this->db;
	}
	
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
}