<?php
class Model_Tracking extends Model_Abstract
{
	function insert ($data)
	{
		if (!empty($data))
		{
			
			//$data['VIEW_STARTED_AT'] = new Zend_Db_Expr('NOW()');
			
			$this->db->insert('U_TRACKED_DATA', $data);
			$lastId = $this->db->lastInsertId();
			try {
				return $this->db->fetchOne('SELECT VIEW_STARTED_AT FROM U_TRACKED_DATA WHERE ID = ?',
								$lastId
					);
			} catch (Zend_Db_Exception $e) {
				
			}
		}
	}
	
	function update ($data)
	{
		try {
			$data['VIEW_ENDED_AT'] = new Zend_Db_Expr('NOW()');
			$this->db->update('U_TRACKED_DATA', 
								$data, 
								'USER_ID = ' . $data['USER_ID'] . 
								' AND FULL_URL = ' . $this->db->quote($data['FULL_URL']) .
								' AND VIEW_STARTED_AT = ' . $this->db->quote($data['VIEW_STARTED_AT'])
							);
		} catch (Zend_Db_Exception $e) {
			
		}
	}
}