<?php
require_once(dirname(__FILE__) . '/../inc/solr/Service.php');

class Model_Solr extends Apache_Solr_Service
{
	public function __construct() {
		parent::__construct('s.ihaznews.com', 80, 'ihn');
	}
	
	public function indexDocument($data = null, $channel = null) {
		if (!$data)
			return false;
		try {
			$doc = new Apache_Solr_Document();
			
			$categorii = $data->category();
			$newcats = array();
			if (is_array($categorii))
				foreach($categorii as $c)
					$newcats[] = $c->nodeValue;
			else
				$newcats = $categorii;
			
			$doc->id = $doc->url_articol = $data->link();
			$doc->titlu_blog = $channel->title();
			$doc->titlu_articol = $data->title();
			$doc->descriere_blog = $channel->description();
			$doc->content_articol = strip_tags($data->content());
			$doc->descriere_articol = strip_tags($data->description());
			$doc->categorii_articol = $newcats;
			
			$this->addDocument($doc);
			$this->optimize();
			//$this->commit();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
	
	public function cauta( $query, $page = 0, $perPage = 21 ) {
		$theSearch = $this->search($query, (int) $page *  (int) $perPage, (int) $perPage);
		$hits = $theSearch->response->docs;
		$numFound = $theSearch->response->numFound;
		$ret->hits = $hits;
		$ret->numFound = $numFound;
		return $ret;
	}
	
	public function sugestii( $url ) {
		$url = str_replace(':', '\:', $url);
		$theSearch = $this->search('(id:' . $url . ')', 0, 50, 
			array('mlt' => 'on', 'mlt.fl' => 'text', 'mlt.count' => 20));
		$response = json_decode($theSearch->getRawResponse());
		foreach($response->moreLikeThis as $r)
			return $r->docs;
	}
	
	public function deleteDocument($id = null) {
		if(!$id)
			return false;
		try {
			$this->deleteByQuery('id:'. (int) $id);
			$this->optimize();
			//$this->commit();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
}