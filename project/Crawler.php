<?php
/**
 * Basic Crawler class
 * <dougan888@gmail.com>
 */
namespace project;

class Crawler {

	/**
	 * The list of visited links.
	 *
	 * @var array
	 */
	public $visitedLinks = array();

	/**
	 * Path to save generated output.
	 *
	 * @var string
	 */
	private $_filePath;

	/**
	 * Host of the provided site.
	 *
	 * @var string
	 */
	public $host;

	/**
	 * Depth of crawling.
	 *
	 * @var int
	 */
	public $depth;

	/**
	 * Sets depth of crawling.
	 *
	 * @param  int $depth
	 *
	 * @return void
	 */
	public function setDepth($depth) {
		$this->depth = $depth;
	}

	/**
	 * Sets host, file path and depth, provided by user's input.
	 *
	 * @param  string $host
	 * @param  string $filePath
	 *
	 * @return void
	 */
	public function __construct($host, $filePath) {
		$this->host = $host;
		$this->_filePath = $filePath;
		$this->depth = 5;
	}

	/**
	 * Crawls site and generates input.
	 *
	 * @return void
	 */
	public function crawlSite() {
		$this->_crawlPage($this->host, $this->depth);
		$this->_generateOutput();
	}

	/**
	 * Recursively crawls the site.
	 *
	 * @param  string $url
	 * @param  int $depth
	 *
	 * @return void
	 */
	private function _crawlPage($url, $depth)
	{
		if (array_key_exists($url, $this->visitedLinks) || $depth === 0) {
			return;
		}

		$dom = new \DOMDocument();
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

	/**
	 * Counts the number of img on provided DOM object.
	 *
	 * @param  DomDocument $dom
	 *
	 * @return int
	 */
	private function _countImg($dom) {
		$images = $dom->getElementsByTagName('img');
		return $images->length;
	}

	/**
	 * Generates output files.
	 *
	 * @return void
	 */
	private function _generateOutput() {
		$name = rtrim($this->_filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'report_' . date('d.m.Y') . '.html';
		$links = $this->visitedLinks;
		arsort($links);
		ob_start();
		include('template.php');
		$content = ob_get_contents();
		ob_end_clean();
		file_put_contents($name, $content);
	}

}