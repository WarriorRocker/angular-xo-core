<?php

/**
 * An abstract class that extends response including a reflected representation of the given controller.
 *
 * @since 1.0.0
 */
class XoApiAbstractControllerIndexResponse extends XoApiAbstractResponse
{
	/**
	 * Reflected controller info.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $controller;

	/**
	 * Generate a fully formed Xo API response including a reflected representation of the given controller.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param mixed $controller Reflected controller info.
	 */
	public function __construct($success, $message, $controller = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->controller = $controller;
	}
}