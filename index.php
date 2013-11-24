<?php

require 'Crawler.php';

if (!isset($argv[1])) {
	throw new Exception('Site is not specified!');
}

$url = (string)$argv[1];

if (strpos($url, 'http') === false) {
	$url = 'http://' . $url;
}

$partsOfUrl = parse_url($url);
$site = $partsOfUrl['scheme'] . '://' . $partsOfUrl['host'];

if(!@get_headers($site)) {
	throw new Exception('Site is not exists!');
} else {
	$crawler = new Crawler($site);
	$crawler->crawlSite();
}


