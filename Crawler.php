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

	private $_filePath;

	public $host;

	public $depth;

	public function setDepth($depth) {
		$this->depth = $depth;
	}

	public function __construct($host, $filePath) {
		$this->host = $host;
		$this->_filePath = $filePath;
		$this->depth = 5;
	}

	public function crawlSite() {
		$this->_crawlPage($this->host, $this->depth);
		$this->_generateOutput();
	}

	private function _crawlPage($url, $depth)
	{
		if (array_key_exists($url, $this->visitedLinks) || $depth === 0) {
			return;
		}

		$dom = new DOMDocument();
		$dom->recover = true;
		$dom->strictErrorChecking = false;
		@$dom->loadHTML(file_get_contents($url));
		$dom->preserveWhiteSpace = false;

		$this->visitedLinks[$url] = $this->_countImg($dom);

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

			$host = parse_url($href, PHP_URL_SCHEME) . '://' . parse_url($href, PHP_URL_HOST);

			if ($host == $this->host) {
				$this->_crawlPage($href, $depth - 1);
			}
		}
	}

	private function _countImg($dom) {
		$images = $dom->getElementsByTagName('img');
		return $images->length;
	}

	private function _generateOutput() {
		$name = rtrim($this->_filePath, '/') . '/report_' . date('d.m.Y') . '.html';
		$links = $this->visitedLinks;
		arsort($links);
		ob_start();
		include('template.php');
		$content = ob_get_contents();
		ob_end_clean();
		file_put_contents($name, $content);
	}

}