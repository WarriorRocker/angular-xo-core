<?php

/**
 * An abstract class that extends post and used to construct a fully formed menu object.
 *
 * @since 1.0.0
 */
class XoApiAbstractMenu extends XoApiAbstractPostObject
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
	 * Collection of children of the current menu item.
	 * 
	 * @since 2.0.0
	 * 
	 * @var XoApiAbstractMenu[]
	 */
	public $children;

	/**
	 * Optional collection of terms applied to the given menu.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $terms;

	/**
	 * Optional collection of meta set for the given menu.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $meta;

	/**
	 * Optional collection of ACF fields set for the given menu.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Generate a fully formed menu object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $menu The base menu object.
	 * @param mixed $terms Optionally include terms in menu object.
	 * @param mixed $meta Optionally include meta in menu object.
	 * @param mixed $fields Optionally include ACF fields in menu object.
	 */
	public function __construct(WP_Post $menu, $terms = false, $meta = false, $fields = false) {
		// Set base menu properties
		$this->SetBaseProperties($menu);

		// Optionally set the menu terms
		if ($terms)
			$this->SetTerms($terms);

		// Optionally set the menu fields
		if ($fields)
			$this->SetFields($fields);

		// Optionally set the menu meta
		if ($meta)
			$this->SetMeta($meta);
	}

	/**
	 * Set the base properties for the given menu.
	 * 
	 * @since 2.0.0
	 */
	public function SetBaseProperties(WP_Post $menu) {
		//print_r($menu);exit;
		// Map base menu object properties
		$this->id = intval($menu->ID);
		$this->title = $menu->title;
		$this->type = $menu->type;
		$this->order = $menu->menu_order;
		$this->parent = intval($menu->menu_item_parent);
		$this->object = $menu->object;
		$this->objectId = intval($menu->object_id);
		$this->classes = $menu->classes;
		$this->target = $menu->target;

		// Set the relative url of the menu
		if ($this->target == '_blank') {
			$this->url = $menu->url;
		} else {
			$this->url = str_replace('/./', '/', wp_make_link_relative($menu->url));
		}
	}
}
