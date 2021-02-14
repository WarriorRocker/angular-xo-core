<?php

/**
 * An abstract class that extends response including a collection of Angular Route objects.
 *
 * @since 1.0.0
 */
class XoApiAbstractRoutesGetResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fully formed Angular Route objects.
	 *
	 * @since 1.0.0
	 *
	 * @var XoApiAbstractRoute[]
	 */
	public $routes;

	/**
	 * Generate a fully formed Xo API response including a collection of Angular Route objects.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractRoute[] $routes Collection of fully formed Angular Route objects.
	 */
	public function __construct($success, $message, $routes = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->routes = $routes;
	}
}