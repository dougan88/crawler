<?php
/**
 * Created by JetBrains PhpStorm.
 * User: eyurkov
 * Date: 19.11.13
 * Time: 12:28
 * To change this template use File | Settings | File Templates.
 */

class Crawler {

	public $visitedLinks = array();

	public $host;

	public function __construct($host) {
		$this->host = $host;
	}

	public function crawlSite() {
		$this->_crawlPage($this->host);
	}

	private function _crawlPage($url, $depth = 5)
	{
		if (isset($this->visitedLinks[$url]) || $depth === 0) {
			return;
		}

		$this->visitedLinks[$url] = true;

		$dom = new DOMDocument();
		$dom->recover = true;
		$dom->strictErrorChecking = false;
		@$dom->loadHTML(file_get_contents($url));
		$dom->preserveWhiteSpace = false;

		$links = $dom->getElementsByTagName('a');
		foreach ($links as $link) {
			$href = $link->getAttribute('href');
			if (0 !== strpos($href, 'http')) {
				$path = '/' . ltrim($href, '/');
				$parts = parse_url($url);
				$href = $parts['scheme'] . '://';
				if (isset($parts['user']) && isset($parts['pass'])) {
					$href .= $parts['user'] . ':' . $parts['pass'] . '@';
				}
				$href .= $parts['host'];
				if (isset($parts['port'])) {
					$href .= ':' . $parts['port'];
				}
				$href .= $path;
			}
//			echo $href . PHP_EOL;
			$host = parse_url($href, PHP_URL_HOST);
			echo $host . PHP_EOL;
			$this->_crawlPage($href, $depth - 1);


//
//			if ($parts['host'] == $this->host) {
//				$this->_crawlPage($href, $depth - 1);
//			}
		}
	}

}