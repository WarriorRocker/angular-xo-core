<?php

/**
 * An abstract class that extends response including a collection of sitemap entry objects.
 *
 * @since 1.0.9
 */
class XoApiAbstractRoutesSitemapResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fully formed sitemap entry objects.
	 *
	 * @since 1.0.9
	 *
	 * @var XoApiAbstractSitemapEntry[]
	 */
	public $entries;

	/**
	 * Generate a fully formed Xo API response including a collection of sitemap entry objects.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractSitemapEntry[] $routes Collection of fully formed sitemap entry objects.
	 */
	public function __construct($success, $message, $entries = array()) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->entries = $entries;
	}
}