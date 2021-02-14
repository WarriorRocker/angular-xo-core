<?php

/**
 * An abstract class that extends response including a collection of fields for the given option group.
 *
 * @since 1.0.0
 */
class XoApiAbstractOptionsGetResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fields for the given option group.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Generate a fully formed Xo API response including a collection of fields for the given option group.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param array $fields Collection of fields for the given option group.
	 */
	public function __construct($success, $message, $fields = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->fields = $fields;
	}
}