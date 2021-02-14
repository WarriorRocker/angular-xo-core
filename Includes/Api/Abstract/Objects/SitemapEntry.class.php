<?php

/**
 * An abstract class used to construct a fully formed sitemap entry object.
 *
 * @since 1.0.9
 */
class XoApiAbstractSitemapEntry
{
	/**
	 * Title text of the sitemap entry.
	 *
	 * @since 1.0.9
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Relative URL of the sitemap entry.
	 *
	 * @since 1.0.9
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Collection of children which fall under the current parent.
	 *
	 * @since 1.0.9
	 *
	 * @var XoApiAbstractSitemapEntry[]
	 */
	public $children;

	/**
	 * Generate a fully formed sitemap entry object.
	 *
	 * @since 1.0.9
	 *
	 * @param string $title Title text of the sitemap entry.
	 * @param string $url Relative URL of the sitemap entry.
	 */
	public function __construct($title = '', $url = '', $children = array()) {
		// Map base sitemap entry properties
		$this->title = $title;
		$this->url = $url;
		$this->children = $children;
	}
}