<?php

/**
 * An abstract class that extends response including a collection of terms.
 *
 * @since 1.0.0
 */
class XoApiAbstractTermsFilterResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fully formed term objects.
	 *
	 * @since 1.0.0
	 *
	 * @var XoApiAbstractTerm[]
	 */
	public $terms;

	/**
	 * Generate a fully formed Xo API response including a collection of terms.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractTerm[] $terms Collection of fully formed term objects.
	 */
	public function __construct($success, $message, $terms = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->terms = $terms;
	}
}