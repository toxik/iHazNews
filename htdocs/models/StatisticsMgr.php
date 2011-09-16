<?php 
class Model_StatisticsMgr extends Model_Abstract {
	
function getUniqueVisits($webname, $datet){
	$webname = $this->db->quote('%'.$webname.'%');
	$datet = $this->db->quote($datet);

	return $this->db->fetchOne('SELECT COUNT(DISTINCT USER_ID) FROM U_TRACKED_DATA WHERE LOWER(FULL_URL) 
		LIKE LOWER('.$webname.') AND DATE_FORMAT(VIEW_STARTED_AT, "%Y-%m-%d") = DATE_FORMAT('.$datet.', "%Y-%m-%d") AND VIEW_ENDED_AT IS NOT NULL');;
}

function getUniqueVisits2($webid, $datet){
	return $this->db->fetchOne('SELECT COUNT(DISTINCT USER_ID) FROM U_TRACKED_DATA WHERE WEBSITE_ID = ? 
		AND DATE(VIEW_STARTED_AT) = ? AND VIEW_ENDED_AT IS NOT NULL',array($webid,$datet));
}

function getTotalVisits($webname, $datet){
	$webname = $this->db->quote('%'.$webname.'%');
	$datet = $this->db->quote($datet);
	
	return $this->db->fetchOne('SELECT COUNT(*) FROM U_TRACKED_DATA WHERE LOWER(FULL_URL) LIKE LOWER('.$webname.') AND DATE_FORMAT(VIEW_STARTED_AT, "%Y-%m-%d") = DATE_FORMAT('.$datet.', "%Y-%m-%d") AND VIEW_ENDED_AT IS NOT NULL');

}

function getTotalVisits2($webid, $datet){	
	return $this->db->fetchOne('SELECT COUNT(*) FROM U_TRACKED_DATA WHERE WEBSITE_ID = ? 
	AND DATE(VIEW_STARTED_AT) = ? AND VIEW_ENDED_AT IS NOT NULL',array($webid,$datet));

}

function getAverageRatings($userid){
	$inf = $this->db->fetchAll('SELECT WEBSITE_ID, SITE_NAME, AVG(RATING) AS RATING
								FROM P_WEBSITES JOIN U_RATINGS
								USING(WEBSITE_ID)
								WHERE ADDED_BY = ?
								GROUP BY WEBSITE_ID, SITE_NAME',
								$userid);
	return $inf;
}

function getSubscribers($userid){
	$inf = $this->db->fetchAll('SELECT WEBSITE_ID, COUNT(USER_ID) AS NR
								FROM P_WEBSITES JOIN MAP_USER_WEBSITES
								USING(WEBSITE_ID)
								WHERE ADDED_BY = ?
								GROUP BY WEBSITE_ID',
								$userid);
	return $inf;
}

}