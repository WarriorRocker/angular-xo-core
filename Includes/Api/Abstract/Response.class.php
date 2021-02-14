<?php

/**
 * An abstract class used to return a fully formed Xo API response.
 *
 * @since 1.0.0
 */
class XoApiAbstractResponse
{
	/**
	 * Indicates a successful interaction with the API.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $success;

	/**
	 * Human readable response from the API interaction.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $message;

	/**
	 * Generate a fully formed Xo API response.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 */
	function __construct($success, $message) {
		// Map base response properties
		$this->success = $success;
		$this->message = $message;
	}
}