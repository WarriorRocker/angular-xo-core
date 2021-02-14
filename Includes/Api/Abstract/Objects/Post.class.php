<?php

/**
 * An abstract class used to construct a fully formed post object.
 *
 * @since 1.0.0
 */
class XoApiAbstractPost extends XoApiAbstractPostObject
{
	/**
	 * ID of the post mapped from ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID of the post's parent mapped from post_parent.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $parent;

	/**
	 * Name of the post type mapped from post_type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Url slug of the post mapped from post_name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Title of the post mapped from post_title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Order of the post mapped from menu_order.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $order;

	/**
	 * Date of the post mapped from post_date.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $date;

	/**
	 * Modified date of the post mapped from post_modified.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $modified;

	/**
	 * Content of the post mapped from post_content with the_content filter applied.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $content;

	/**
	 * Relative URL of the post using get_permalink and wp_make_link_relative.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Optional collection of terms applied to the given post.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $terms;

	/**
	 * Optional collection of meta set for the given post.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $meta;

	/**
	 * Optional collection of ACF fields set for the given post.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Generate a fully formed post object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The base post object.
	 * @param bool $terms Optionally include terms in post object.
	 * @param bool $meta Optionally include meta in post object.
	 * @param bool $fields Optionally include ACF fields in post object.
	 * @param bool $breadcrumbs Optionally include breadcrumb items in post object.
	 */
	public function __construct(WP_Post $post, $terms = false, $meta = false, $fields = false) {
		// Set base post properties
		$this->SetBaseProperties($post);

		// Optionally set the post terms
		if ($terms)
			$this->SetTerms();

		// Optionally set the post fields
		if ($fields)
			$this->SetFields();

		// Optionally set the post meta
		if ($meta)
			$this->SetMeta();
	}

	/**
	 * Set the base properties for the given post.
	 * 
	 * @since 2.0.0
	 */
	public function SetBaseProperties(WP_Post $post) {
		// Map base post object properties
		$this->id = intval($post->ID);
		$this->parent = intval($post->post_parent);
		$this->type = $post->post_type;
		$this->slug = $post->post_name;
		$this->title = $post->post_title;
		$this->order = $post->menu_order;
		$this->date = $post->post_date;
		$this->modified = $post->post_modified;

		// Set the post content using the_content filter
		$this->content = apply_filters('the_content', $post->post_content);

		// Set the relative URL of the post using get_permalink and wp_make_link_relative
		$this->url = wp_make_link_relative(get_permalink($post->ID));
	}

	
}