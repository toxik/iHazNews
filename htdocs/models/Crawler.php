<?php
class Model_Crawler extends Model_Abstract
{	
	public function IndexWebsitesContent()
	{
		$sql = 'select WEBSITE_ID, URL from P_WEBSITES where F_ACTIVE = \'Y\' ';
		$websitesToIndex = $this->db->fetchAll($sql);
		
		$s = new Model_Solr();
		
		foreach ($websitesToIndex as $website)
		{
			try
			{
				$channel = new Zend_Feed_Rss($website['URL'] . '/feed/');
				
				foreach ($channel as $item)
				{
					$sql = 'SELECT RSS_URL FROM P_WEBSITES_RSS WHERE RSS_URL = ?';	
					$result = $this->db->fetchOne($sql, $item->link());
					
					$s->indexDocument($item, $channel);
					
					if($result) // Daca link-ul este deja indexat
						continue;
					
					/*
					$row = array(
						'WEBSITE_ID'  => $website['WEBSITE_ID'],
						'TITLE'       => $item->title(),
						'LINK'		  => $item->link(),
						'DESCRIPTION' => $item->description(),
						'PUBDATE'     => $item->pubDate()
					);
					//Zend_Debug::dump($row);
					$this->db->insert('P_WEBSITES_CONTENT', $row);
					*/

					$row = array(
						'RSS_URL'     => $item->link(),
						'WEBSITE_ID'  => $website['WEBSITE_ID']
					);
					$this->db->insert('P_WEBSITES_RSS', $row);
				}

			}
			catch(Exception $e) { }
		}
	}
}