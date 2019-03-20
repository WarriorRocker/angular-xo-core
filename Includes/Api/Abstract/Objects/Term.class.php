<?php

/**
 * An abstract class used to construct a fully formed term object.
 *
 * @since 1.0.0
 */
class XoApiAbstractTerm
{
	/**
	 * ID of the term mapped from term_id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID of the term's parent mapped from parent.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $parent;

	/**
	 * URL slug of the term mapped from slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Name of the term mapped from name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Description of the term mapped from description.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Group of the term mapped from group.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $group;

	/**
	 * Taxonomy of the term mapped from taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $taxonomy;

	/**
	 * ID of the Taxonomy of the term mapped from term_taxonomy_id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $taxonomyId;

	/**
	 * Relative URL of the term using get_term_link and wp_make_link_relative.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Optional collection of meta set for the given term.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $meta;

	/**
	 * Optional collection of ACF fields set for the given term.
	 *
	 * @since 1.0.7
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Generate a fully formed term object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Term $term The base term object.
	 * @param bool $meta Optionally include meta in term object.
	 * @param bool $fields Optionally include ACF fields in post object.
	 */
	function __construct(WP_Term $term, $meta = false, $fields = false) {
		// Map base term object properties
		$this->id = $term->term_id;
		$this->parent = $term->parent;
		$this->slug = $term->slug;
		$this->name = $term->name;
		$this->description = $term->description;
		$this->group = $term->term_group;
		$this->taxonomy = $term->taxonomy;
		$this->taxonomyId = $term->term_taxonomy_id;

		// Set the relative URL of the term using get_term_link and wp_make_link_relative
		$this->url = wp_make_link_relative(get_term_link($term));

		// Optionally set the post fields
		if ($fields)
			$this->SetFields();

		// Optionally set the term meta
		if ($meta)
			$this->SetMeta();
	}

	/**
	 * Set the meta that are set for the given term.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function SetMeta() {
		// Get all meta options for the term
		if (!$meta = get_term_meta($this->id))
			return;

		// Iterate over the retrieved meta options
		foreach ($meta as $key => $value) {
			// Skip the option if name not starts with an underscore indicating private or internal data
			if (substr($key, 0, 1) == '_')
				continue;

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

			if (!$addMeta)
				continue;

			if ((is_array($value)) && (count($value) == 1))
				$this->meta[$key] = $value[0];
		}
	}

	/**
	 * Set the ACF fields that are set for the given post.
	 *
	 * @since 1.0.7
	 *
	 * @return void
	 */
	public function SetFields() {
		// Return empty array if ACF is unavailable
		if (!function_exists('get_fields'))
			return;

		// Get collection of ACF fields for the post
		$this->fields = get_fields($this->taxonomy . '_' . $this->id);
	}
}