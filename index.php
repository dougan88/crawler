<?php

require 'Crawler.php';

if (!isset($argv[1])) {
	throw new Exception('Site is not specified!');
}

if (!isset($argv[2]) && is_readable($argv[2])) {
	throw new Exception('Path is not specified!');
}
else {
	$filePath = $argv[2];
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
	$crawler = new Crawler($site, $filePath);

	if (isset($argv[3])) {
		$depth = (int)$argv[3];
		$crawler->setDepth($depth);
	}
	$crawler->crawlSite();
}


