<?php

/**
 * An abstract class that extends response including the fully formed post object.
 *
 * @since 1.0.0
 */
class XoApiAbstractPostsGetResponse extends XoApiAbstractResponse
{
	/**
	 * The fully formed post object.
	 *
	 * @since 1.0.0
	 *
	 * @var XoApiAbstractPost
	 */
	public $post;

	/**
	 * Generate a fully formed Xo API response including a collection of posts.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractPost $post The fully formed post object.
	 */
	public function __construct($success, $message, XoApiAbstractPost $post = NULL) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->post = $post;
	}
}