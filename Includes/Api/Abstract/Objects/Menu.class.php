<?php

/**
 * An abstract class that extends post and used to construct a fully formed menu object.
 *
 * @since 1.0.0
 */
class XoApiAbstractMenu extends XoApiAbstractPost
{
	/**
	 * ID of the menu item mapped from ID.
	 *
	 * @since 1.0.8
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID of the menu item's parent mapped from menu_item_parent.
	 *
	 * @since 1.0.8
	 *
	 * @var int
	 */
	public $parent;

	/**
	 * Type of the object of the linked menu item mapped from type.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Link text of the menu item mapped from title.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Order of the menu item mapped from menu_order.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $order;

	/**
	 * Date of the menu item mapped from post_date.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $date;

	/**
	 * Modified date of the menu item mapped from post_modified.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $modified;

	/**
	 * URL of the menu item or relative URL if using target="_blank" mapped from url.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Type of linked menu item object mapped from object.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $object;

	/**
	 * ID of the linked menu item object mapped from object_id.
	 *
	 * @since 1.0.8
	 *
	 * @var string
	 */
	public $objectId;

	/**
	 * Additional CSS classes that may be used in the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $classes;

	/**
	 * Target for the anchor link applied to the menu item.
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public $target;

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
		$this->type = $menu->type;
		$this->parent = intval($menu->menu_item_parent);
		$this->object = $menu->object;
		$this->objectId = intval($menu->object_id);
		$this->classes = $menu->classes;
		$this->target = $menu->target;

		// Set the relative url of the menu
		if ($this->target == '_blank') {
			$this->url = $menu->url;
		} else {
			$this->url = wp_make_link_relative($menu->url);
		}
	}
}