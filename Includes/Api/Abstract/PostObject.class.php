<?php

/**
 * An abstract class used to construct a fully formed post object.
 *
 * @since 2.0.0
 */
class XoApiAbstractPostObject
{
	public $id;
	public $type;
	public $terms;
	public $meta;
	public $fields;

	/**
	 * Set the terms for each taxonomy that are applied to the given post.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function SetTerms() {
		//todo: this needs public/user taxonomy filtering
		$ignore = ['yst_prominent_words'];
		//print_r(wp_get_object_terms($this->id));
		//print_r(get_object_taxonomies($this->type));exit;
		// Get all terms for all taxonomies of the given post
		$terms = wp_get_object_terms($this->id, get_object_taxonomies($this->type));
		//print_r($terms);exit;

		// Iterate through terms and structure the return format
		foreach ($terms as $term) {
			//print_r($term->taxonomy);exit;
			if (!in_array($term->taxonomy, $ignore)) {
				$this->terms[$term->taxonomy][] = new XoApiAbstractTerm(get_term($term), true, true);
			}
		}
			//$this->terms[$term->taxonomy][$term->slug] = $term->name;
	}

	/**
	 * Set the meta that are set for the given post.
	 *
	 * @since 2.0.0
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
			// if (substr($key, strlen($key) - 13) == '_thumbnail_id')
			// 	$this->meta[$key] = wp_get_attachment_url($value[0]);
		}
	}

	/**
	 * Set the ACF fields that are set for the given post.
	 *
	 * @since 2.0.0
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
}
