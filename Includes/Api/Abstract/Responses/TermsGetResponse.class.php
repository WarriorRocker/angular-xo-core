<?php

/**
 * An abstract class that extends response including a taxonomy and term object.
 *
 * @since 1.0.7
 */
class XoApiAbstractTermsGetResponse extends XoApiAbstractResponse
{
	/**
	 * Taxonomy object from the given url.
	 *
	 * @since 1.0.7
	 *
	 * @var WP_Taxonomy
	 */
	public $taxonomy;

	/**
	 * Fully formed term object from the given url.
	 *
	 * @since 1.0.7
	 *
	 * @var XoApiAbstractTerm
	 */
	public $term;

	/**
	 * Generate a fully formed Xo API response including a term and taxonomy object.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param string $message Human readable response from the API interaction.
	 * @param WP_Taxonomy $count WordPress taxonomy object.
	 * @param XoApiAbstractTerm $total Fully formed term object.
	 */
	function __construct($success, $message, WP_Taxonomy $taxonomy = NULL, XoApiAbstractTerm $term = NULL) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->taxonomy = $taxonomy;
		$this->term = $term;
	}
}