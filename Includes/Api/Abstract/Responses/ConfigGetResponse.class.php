<?php

/**
 * An abstract class that extends response including the generated configuration object.
 *
 * @since 1.0.0
 */
class XoApiAbstractConfigGetResponse extends XoApiAbstractResponse
{
	/**
	 * The generated configuration object.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $config;

	/**
	 * Generate a fully formed Xo API response including the generated configuration object.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param mixed $config The generated configuration object.
	 */
	public function __construct($success, $message, $config = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->config = $config;
	}
}