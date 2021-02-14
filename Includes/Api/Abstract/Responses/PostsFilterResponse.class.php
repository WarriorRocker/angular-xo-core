<?php

/**
 * An abstract class that extends response including a collection of posts.
 *
 * @since 1.0.0
 */
class XoApiAbstractPostsFilterResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of posts returned by the given filters after pagination.
	 *
	 * @since 1.0.0
	 *
	 * @var XoApiAbstractPost[]
	 */
	public $posts;

	/**
	 * The amount of posts returned by the given filters after pagination.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $count;

	/**
	 * The total amount of posts found by the given filters before pagination.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $total;

	/**
	 * Generate a fully formed Xo API response including a collection of posts.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractPost[] $posts Collection of posts returned by the given filters after pagination.
	 * @param int $count The amount of posts returned by the given filters after pagination.
	 * @param int $total The total amount of posts found by the given filters before pagination.
	 */
	public function __construct($success, $message, $posts = false, $count = 0, $total = 0) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->posts = $posts;
		$this->count = $count;
		$this->total = $total;
	}
}