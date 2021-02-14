<?php

/**
 * An abstract class that extends response including the configuration of a given post type.
 *
 * @since 1.0.4
 */
class XoApiAbstractPostsConfigResponse extends XoApiAbstractResponse
{
	/**
	 * WordPress post type config object.
	 *
	 * @since 1.0.4
	 *
	 * @var WP_Post_Type
	 */
	public $config;

	/**
	 * Generate a fully formed Xo API response including a post type configuration.
	 *
	 * @since 1.0.4
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param WP_Post_Type $config WordPress post type config object.
	 */
	public function __construct($success, $message, $config = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->config = $config;
	}
}