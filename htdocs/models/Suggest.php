<?php
class Model_Suggest extends Model_Abstract {
	private $user_id;
	private $articles_no;
	private $suggestions_no;
	private $frequency;
	private $start_date;
	private $viewed;
	private $subscribed;
	private $subscribedIds;
	private $solr;
	
	function __construct( $user_id ) {
		parent::__construct();
		$this->user_id = (int) $user_id;
		
		$info = $this->db->fetchRow(
			'SELECT N_ARTICLE_COUNT, N_SUGGESTION_COUNT, N_PERIOD
			FROM U_ACCOUNT WHERE USER_ID = ?', 
				$this->user_id
		);
		$this->articles_no = $info['N_ARTICLE_COUNT'];
		$this->suggestions_no = $info['N_SUGGESTION_COUNT'];
		$this->frequency = $info['N_PERIOD'];
		unset($info);
		
		$this->start_date = $this->start_date = $this->db->fetchOne(
			"SELECT DATE
			FROM U_NEWSLETTERS
			WHERE USER_ID = ?
			ORDER BY DATE DESC
			LIMIT 1", $this->user_id
		) ? $this->start_date : '1970-01-01 00:00';
		
		$this->viewed = $this->db->fetchCol(
			'SELECT FULL_URL, SUM(DURATION)
			FROM U_TRACKED_DATA
			WHERE USER_ID = ?
			GROUP BY FULL_URL
			ORDER BY 2 ASC', $this->user_id
		);
		
		$this->subscribed = $this->db->fetchCol(
			"SELECT w.URL as URL
			FROM P_WEBSITES w
				RIGHT JOIN MAP_USER_WEBSITES USING (WEBSITE_ID)
			WHERE F_ACTIVE = 'Y' AND USER_ID = ?
			UNION
			SELECT wn.URL as URL
			FROM P_WEBSITES wn
				RIGHT JOIN MAP_WEBSITES_NEWSGROUPS USING (WEBSITE_ID)
			WHERE F_ACTIVE = 'Y' AND NEWSGROUP_ID IN (
				SELECT NEWSGROUP_ID 
				FROM MAP_USER_NEWSGROUPS
				WHERE USER_ID = ?
			)", array( $this->user_id, $this->user_id )
		);
		
		$this->subscribedIds = $this->db->fetchCol(
			"SELECT w.WEBSITE_ID as URL
			FROM P_WEBSITES w
				RIGHT JOIN MAP_USER_WEBSITES USING (WEBSITE_ID)
			WHERE F_ACTIVE = 'Y' AND USER_ID = ?
			UNION
			SELECT wn.WEBSITE_ID as URL
			FROM P_WEBSITES wn
				RIGHT JOIN MAP_WEBSITES_NEWSGROUPS USING (WEBSITE_ID)
			WHERE F_ACTIVE = 'Y' AND NEWSGROUP_ID IN (
				SELECT NEWSGROUP_ID 
				FROM MAP_USER_NEWSGROUPS
				WHERE USER_ID = ?
			)", array( $this->user_id, $this->user_id )
		);
		
		$this->solr = new Model_Solr;
	}
	
	private function getMetaInfoForURLs( $urls ) {
		$urls = array_keys($urls);
	
		$query_string = str_replace(':', '\:', implode(' OR ', $urls));
		return $this->solr->cauta( $query_string )->hits;
	}

	function getSuggestions() {
		// sa facem requesturile la MLT
		$allSuggestions = array();
		$n = $this->articles_no > count($this->viewed) ? count($this->viewed) : $this->articles_no;
		for($i = 0; $i < $n; $i++) {
			$sugestii = $this->solr->sugestii( $this->viewed[$i] );
			if ($sugestii)
			foreach($sugestii as $sugestie)
				$allSuggestions[ $sugestie->id ] += $sugestie->score;
			unset($sugestii);
		}
		// reverse sort by value ( biggest importance first )
		arsort($allSuggestions);
		
		// filter the results, I only want to see results from sites I subscribed to
		$filteredSuggestions = array();
		foreach($allSuggestions as $k => $value) {
			$found = false;
			foreach($this->subscribed as $sub)
				if (substr($k,0,strlen($sub)) == $sub)
					$found = true;
			if ($found)
				$filteredSuggestions[$k] = $value;
		}
		unset($allSuggestions);
		
		// take out previous things I've seen ( if possible )
		$viewed = $this->viewed;
		while(
			count($filteredSuggestions) > $this->articles_no
			&& count($viewed) ) { 
			
			$viewed_url = array_pop($viewed);
			if (isset($filteredSuggestions[$viewed_url]))
				unset($filteredSuggestions[$viewed_url]);
			
		}
		
		if (count($filteredSuggestions) > $this->articles_no )
			array_splice($filteredSuggestions, $this->articles_no);
		unset($viewed);
			
		if(!$filteredSuggestions)
			return false;
		
		return $this->getMetaInfoForURLs($filteredSuggestions);
	}
	
	function getRecommendations() {
		// il scoatem din "zona de confort"
		
		// ma uit la utilizatorii care au grupuri comune cu mine si scot newsgroupurile diferite
		$websites = array();
		if ($newsGroupsIds = $this->getOtherNewsGroups() )
			// scoatem site-urile din aceste newsgroup-uri si le bagam in array
			$websites = $this->db->fetchCol(
				'SELECT WEBSITE_ID 
				FROM MAP_WEBSITES_NEWSGROUPS
				WHERE NEWSGROUP_ID IN (' . implode(',', $newsGroupsIds) . ')'
			);
		// si in final sa combinam cu restul site-urilor userilor asemanatori cu mine
		$other = $this->getOtherWebSites();
		$websites = array_unique(array_merge($websites, $other));
		// apoi sa filtram site-urile pe care le avem deja
		$websites = array_diff($websites, $this->subscribedIds);
		//Zend_Debug::dump($this->subscribedIds, 'websites for uid: ' . $this->user_id);
		//Zend_Debug::dump($websites, 'suggested websites for uid: ' . $this->user_id);
		if (!$websites) 
			return false; // aparent nu exista chestii legate indepartat de mine..
		
		// dar daca exista, ne uitam la traficul din ultima perioada si filtram cu arrayul nostru
		$traffic = $this->db->fetchAll(
			'SELECT FULL_URL, SUM(DURATION) DURATA, COUNT(USER_ID) VIZITE_UNICE
			FROM U_TRACKED_DATA
			WHERE DATE_ADD(VIEW_STARTED_AT, INTERVAL -' . (int) $this->frequency . ' DAY) < NOW()
				AND WEBSITE_ID IN ( ' . implode(',', $websites) . ' )
			GROUP BY FULL_URL'
		);
		//Zend_Debug::dump($traffic);
		$viewed = $this->viewed;
		while(
			count($traffic) > $this->suggestions_no
			&& count($viewed) ) { 
			
			$viewed_url = array_pop($viewed);
			// cautare url curent in $traffic
			foreach($traffic as $k => $t)
				if ($t['FULL_URL'] == $viewed_url)
					unset($traffic[$k]);
			
		}
		unset($viewed);
		
		$newTraffic = array();
		foreach($traffic as $t) 
			$newTraffic[$t['FULL_URL']] = $t['DURATA'] / $t['VIZITE_UNICE'];
		unset ($traffic);
		arsort($newTraffic);
		
		if (count($newTraffic) > $this->suggestions_no )
			array_splice($newTraffic, $this->suggestions_no);
		
		return $this->getMetaInfoForURLs($newTraffic);
	}
	
	private function getOtherUsersLikeMe() {
		return $this->db->fetchCol(
			'SELECT USER_ID 
			FROM MAP_USER_NEWSGROUPS
			WHERE NEWSGROUP_ID IN (
				SELECT NEWSGROUP_ID
				FROM MAP_USER_NEWSGROUPS
				WHERE USER_ID = ?
			)
			UNION
			SELECT USER_ID 
			FROM MAP_USER_INTERESTS
			WHERE INTEREST_ID IN (
				SELECT INTEREST_ID
				FROM MAP_USER_INTERESTS
				WHERE USER_ID = ?
			)', array($this->user_id, $this->user_id)
		);
	}
	
	private function getOtherWebSites() {
		$other = $this->getOtherUsersLikeMe();
		//Zend_Debug::dump($other, 'USERI: ');
		if (!$other) $other = array(0);
		return $this->db->fetchCol(
			'SELECT DISTINCT WEBSITE_ID
			FROM MAP_USER_WEBSITES
			WHERE USER_ID IN (
				'. implode(',', $other) .'
			)' 
		);
	}
	
	private function getOtherNewsGroups() {
		return $this->db->fetchCol(
			'SELECT DISTINCT NEWSGROUP_ID
			FROM MAP_USER_NEWSGROUPS
			WHERE USER_ID IN (
				SELECT USER_ID 
				FROM MAP_USER_NEWSGROUPS
				WHERE NEWSGROUP_ID IN (
					SELECT NEWSGROUP_ID
					FROM MAP_USER_NEWSGROUPS
					WHERE USER_ID = ?
				)
			) AND NEWSGROUP_ID NOT IN (
				SELECT NEWSGROUP_ID 
				FROM MAP_USER_NEWSGROUPS
				WHERE USER_ID = ?
			)', array( $this->user_id, $this->user_id )
		);
	}
	
	function getTextEmail($s, $r) {
		$text = "Buna ziua, \r\n\r\nAcesta este newsletter-ul periodic de la iHazNews. \r\n\r\n";
		if ($s) {
			$text .= 'Sugestii: ' . "\r\n\r\n";
			foreach($s as $rec)
				$text .= $rec->titlu_articol . ' - ' . $rec->url_articol . "\r\n" .
							$rec->descriere_articol . "\r\n" . 'via ' . $rec->titlu_blog . "\r\n\r\n";
			$text .= "\r\n\r\n";
		} else { $text .= 'Ne pare rau, insa nu am gasit sugestii pentru dumneavoastra.' . "\r\n" .
				'Avem, in schimb, recomandarile de mai jos:' . "\r\n\r\n" ; }
		if ($r) {
			$text .= 'Recomandari: ' . "\r\n\r\n";
			foreach($r as $rec)
				$text .= $rec->titlu_articol . ' - ' . $rec->url_articol . "\r\n" .
							$rec->descriere_articol . "\r\n" . 'via ' . $rec->titlu_blog . "\r\n\r\n";
			$text .= "\r\n\r\n";
		} else { $text .= 'Ne pare rau, insa nu am gasit recomandari pentru dumneavoastra.' . "\r\n" .
				'Avem, in schimb, sugestiile de mai sus!' . "\r\n\r\n" ; }
		
		$text .= '(c) 2010-' . date('Y') . ' All rights reserved iHazNews - http://ihaznews.com ' 
				. "\r\n";
	}
	
	function saveToDatabase($continut) {
		$this->db->insert('U_NEWSLETTERS', array(
				'CONTENT'	=> &$continut,
				'USER_ID'	=> $this->user_id
			)
		);
	}
	
	// eliberam marea masa de memorie papata
	function __destruct() {
		unset($this->user_id);
		unset($this->articles_no);
		unset($this->suggestions_no);
		unset($this->start_date);
		unset($this->viewed);
		unset($this->subscribed);
		unset($this->subscribedIds);
		unset($this->solr);
	}
	
}