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
	 * Status of the post mapped from post_status.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $status;

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
	 * Excerpt of the post mapped from post_excerpt with the_excerpt filter applied.
	 * 
	 * @since 2.0.0
	 * 
	 * @var string
	 */
	public $excerpt;

	/**
	 * Relative URL of the post using get_permalink and wp_make_link_relative.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Featured Image of the post set as _thumbnail_id in the post meta.
	 * 
	 * @since 2.0.0
	 * 
	 * @var string
	 */
	public $image;

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
	 * @param WP_Post|int $post The base post object.
	 * @param mixed $terms Optionally include terms in post object.
	 * @param bool $meta Optionally include meta in post object.
	 * @param mixed $fields Optionally include ACF fields in post object.
	 */
	public function __construct(WP_Post $post, $terms = false, $meta = false, $fields = false) {
		// Obtain the post object
		if (is_numeric($post)) {
			$post = get_post($post);
		}

		// Set base post properties
		$this->SetBaseProperties($post);

		// Optionally set the post terms
		if ($terms)
			$this->SetTerms($terms);

		// Optionally set the post fields
		if ($fields)
			$this->SetFields($fields);

		// Optionally set the post meta
		if ($meta)
			$this->SetMeta($meta);
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
		$this->status = $post->post_status;
		$this->slug = $post->post_name;
		$this->title = $post->post_title;
		$this->order = $post->menu_order;

		// Set dates converted to timestamps
		$this->date = get_post_datetime($post, 'date')->getTimeStamp() * 1000;
		$this->modified = get_post_datetime($post, 'modified')->getTimeStamp() * 1000;

		// Set the post content using the_content filter
		$this->content = apply_filters('the_content', $post->post_content);

		// Set the post excerpt using the_excerpt filter
		$this->excerpt = apply_filters('the_excerpt', $post->post_excerpt);

		// Set the relative URL of the post using get_permalink and wp_make_link_relative
		$this->url = str_replace('/./', '/', wp_make_link_relative(get_permalink($post->ID)));

		// Set the attached post thumbnail if found
		if ($thumbnail = get_post_meta($post->ID, '_thumbnail_id', true)) {
			$this->image = wp_get_attachment_url($thumbnail);
		}
	}
}
