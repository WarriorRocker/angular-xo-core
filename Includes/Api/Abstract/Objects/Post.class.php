<?php

/**
 * An abstract class used to construct a fully formed post object.
 *
 * @since 1.0.0
 */
class XoApiAbstractPost
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
	 * Optional set of breadcrumb items for the given post's URL.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $breadcrumbs;

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
	function __construct(WP_Post $post, $terms = false, $meta = false, $fields = false, $breadcrumbs = false) {
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

		// Optionally set the post terms
		if ($terms)
			$this->SetTerms();

		// Optionally set the post fields
		if ($fields)
			$this->SetFields();

		// Optionally set the post meta
		if ($meta)
			$this->SetMeta();

		// Optionally set the post breadcrumbs
		if ($breadcrumbs)
			$this->SetBreadcrumbs();
	}

	/**
	 * Set the terms for each taxonomy that are applied to the given post.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function SetTerms() {
		// Get all terms for all taxonomies of the given post
		$terms = wp_get_object_terms($this->id, get_object_taxonomies($this->type));

		// Iterate through terms and structure the return format
		foreach ($terms as $term)
			$this->terms[$term->taxonomy][$term->slug] = $term->name;
	}

	/**
	 * Set the meta that are set for the given post.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function SetMeta() {
		// Get all meta options for the post
		if (!$meta = get_post_meta($this->id))
			return;

		// Iterate over the retrieved meta options
		foreach ($meta as $key => $value) {
			$addMeta = true;

			// Check if meta option should be excluded
			if (!empty($this->fields)) {
				foreach ($this->fields as $exclude => $excludeValue) {
					if (substr($key, 0, strlen($exclude)) == $exclude) {
						$addMeta = false;
						break;
					}
				}
			}

			// Only return the option if name not starting with an underscore indicating private or internal data
			if (($addMeta) && (substr($key, 0, 1) != '_'))
				$this->meta[$key] = $value[0];

			// Get the attached post thumbnail if found
			if (substr($key, strlen($key) - 13) == '_thumbnail_id')
				$this->meta[$key] = wp_get_attachment_url($value[0]);
		}
	}

	/**
	 * Set the ACF fields that are set for the given post.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function SetFields() {
		// Return empty array if ACF is unavailable
		if (!function_exists('get_fields'))
			return;

		// Get collection of ACF fields for the post
		$this->fields = get_fields($this->id);
	}

	/**
	 * Set the breadcrumb items for the given post.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function SetBreadcrumbs() {
		$this->breadcrumbs = array();

		$parts = explode('/', $this->url);

		$url = '';
		foreach ($parts as $part) {
			if (!$part)
				continue;

			$url .= '/' . $part;

			if (($post = get_post(url_to_postid($url))) ||
				($post = get_page_by_path($url))) {
				$this->breadcrumbs[] = new XoApiAbstractPost($post);
			}
		}
	}
}