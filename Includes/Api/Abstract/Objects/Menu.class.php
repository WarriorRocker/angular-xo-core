<?php

/**
 * An abstract class that extends post and used to construct a fully formed menu object.
 * 
 * @since 1.0.0
 */
class XoApiAbstractMenu extends XoApiAbstractPost
{
	/**
	 * Additional css classes that may be used in the front-end.
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	public $classes;

	/**
	 * Generate a fully formed menu object.
	 * 
	 * @since 1.0.0
	 * 
	 * @param WP_Post $menu The base menu object.
	 * @param bool $terms Optionally include terms in menu object.
	 * @param bool $meta Optionally include meta in menu object.
	 * @param bool $fields Optionally include ACF fields in menu object.
	 */
	function __construct(WP_Post $menu, $terms = false, $meta = false, $fields = false) {
		// Extend the fully formed post object
		parent::__construct($menu, $terms, $meta, $fields);

		// Map base menu object properties
		$this->title = $menu->title;
		$this->classes = $menu->classes;
		$this->parent = $menu->menu_item_parent;

		// Set the relative url of the menu
		$this->url = wp_make_link_relative($menu->url);
	}
}