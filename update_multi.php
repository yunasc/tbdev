<?php

require_once('include/bittorrent.php');
dbconn();
loggedinorreturn();

require_once('include/scraper/httptscraper.php');
require_once('include/scraper/udptscraper.php');

$timeout = 2;
$udp = new udptscraper($timeout);
$http = new httptscraper($timeout);

$tid = intval($_GET['id']);
$anns_r = sql_query('SELECT info_hash, url FROM torrents_scrape WHERE tid = '.$tid);

while ($ann = mysql_fetch_array($anns_r)) {
	if (substr($ann['url'], 0, 6) == 'udp://') {
		try {
			$data = $udp->scrape($ann['url'], $ann['info_hash']);
			$data = $data[$ann['info_hash']];
			sql_query('UPDATE torrents_scrape SET state = "ok", error = "", seeders = '.$data['seeders'].', leechers = '.$data['leechers'].' WHERE tid = '.$tid.' AND url = '.sqlesc($ann['url'])) or sqlerr(__FILE__,__LINE__);
		} catch(ScraperException $e){
			/*echo('Error: ' . $e->getMessage() . "<br />\n");
			echo('Connection error: ' . ($e->isConnectionError() ? 'yes' : 'no') . "<br />\n");*/
			sql_query('UPDATE torrents_scrape SET state = "error", error = '.sqlesc($e->getMessage()).', seeders = 0, leechers = 0 WHERE tid = '.$tid.' AND url = '.sqlesc($ann['url'])) or sqlerr(__FILE__,__LINE__);
		}
	} else {
		try {
			$data = $http->scrape($ann['url'], $ann['info_hash']);
			$data = $data[$ann['info_hash']];
			sql_query('UPDATE torrents_scrape SET state = "ok", error = "", seeders = '.$data['seeders'].', leechers = '.$data['leechers'].' WHERE tid = '.$tid.' AND url = '.sqlesc($ann['url'])) or sqlerr(__FILE__,__LINE__);
		} catch(ScraperException $e){
			/*echo('Error: ' . $e->getMessage() . "<br />\n");
			echo('Connection error: ' . ($e->isConnectionError() ? 'yes' : 'no') . "<br />\n");*/
			sql_query('UPDATE torrents_scrape SET state = "error", error = '.sqlesc($e->getMessage()).', seeders = 0, leechers = 0 WHERE tid = '.$tid.' AND url = '.sqlesc($ann['url'])) or sqlerr(__FILE__,__LINE__);
		}
	}
}