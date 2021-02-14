<?php

/**
 * An abstract class that extends response including a collection of breadcrumb sitemap entry objects.
 *
 * @since 1.0.9
 */
class XoApiAbstractRoutesBreadcrumbsResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fully formed breadcrumb sitemap entry objects.
	 *
	 * @since 1.1.0
	 *
	 * @var XoApiAbstractSitemapEntry[]
	 */
	public $breadcrumbs;

	/**
	 * Generate a fully formed Xo API response including a collection of breadcrumb sitemap entry objects.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractSitemapEntry[] $routes Collection of fully formed breadcrumb sitemap objects.
	 */
	public function __construct($success, $message, $breadcrumbs = array()) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->breadcrumbs = $breadcrumbs;
	}
}