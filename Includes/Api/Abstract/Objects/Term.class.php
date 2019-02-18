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
	 * Generate a fully formed term object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Term $term The base term object.
	 * @param bool $meta Optionally include meta in term object.
	 */
	function __construct(WP_Term $term, $meta = false) {
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
		foreach ($meta as $key => $value)
			if ((is_array($value)) && (count($value) == 1) && (substr($key, 0, 1) != '_'))
				$this->meta[$key] = $value[0];
	}
}