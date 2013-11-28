<?php
use project\Crawler;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';


//Checks if required parameters are provided
if (!isset($argv[1])) {
	echo 'Site is not specified!' . PHP_EOL;
	exit(0);
}

if (!isset($argv[2]) && is_readable($argv[2])) {
	echo 'Path is not specified!';
	exit(0);
}
else {
	$filePath = $argv[2];
}


//Gets URL
$url = $argv[1];

if (strpos($url, 'http') === false) {
	$url = 'http://' . $url;
}

$partsOfUrl = parse_url($url);
$site = $partsOfUrl['scheme'] . '://' . $partsOfUrl['host'];

//Checks if provided site exists and crawls through it
if(!@get_headers($site)) {
	echo 'Site is not exists!';
	exit(0);
}
else {
	$crawler = new Crawler($site, $filePath);

	if (isset($argv[3])) {
		$depth = (int)$argv[3];
		$crawler->setDepth($depth);
	}
	$crawler->crawlSite();
}


