<?php

/**
 * An abstract class that extends response including a collection of menu items.
 *
 * @since 1.0.0
 */
class XoApiAbstractMenusGetResponse extends XoApiAbstractResponse
{
	/**
	 * Collection of fully formed menu items.
	 *
	 * @since 1.0.0
	 *
	 * @var XoApiAbstractMenu[]
	 */
	public $items;

	/**
	 * Generate a fully formed Xo API response including a collection of menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $success Indicates a successful interaction with the API.
	 * @param mixed $message Human readable response from the API interaction.
	 * @param XoApiAbstractMenu[] $items Collection of fully formed menu items.
	 */
	public function __construct($success, $message, $items = false) {
		// Extend base response
		parent::__construct($success, $message);

		// Map base response properties
		$this->items = $items;
	}
}