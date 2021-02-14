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
	public function SetTerms($terms) {
		//todo: this needs public/user taxonomy filtering
		$ignore = ['yst_prominent_words'];
		$taxonomies = is_bool($terms) ? get_object_taxonomies($this->type) : $terms;

		foreach ($taxonomies as $taxonomy) {
			if (in_array($taxonomy, $ignore)) {
				continue;
			}

			// Get all terms for given the taxonomy of the given post
			$terms = wp_get_object_terms($this->id, $taxonomy);

			// Iterate through terms and structure the return format
			foreach ($terms as $term) {
				$this->terms[$term->taxonomy][] = new XoApiAbstractTerm(get_term($term));
			}

			// If Yoast is installed attempt to set the primary category first
			if (class_exists('WPSEO_Primary_Term')) {
				$wpseo_primary_term = new WPSEO_Primary_Term($taxonomy, $this->id);
				if ($wpseo_primary_term) {
					$primary_term = $wpseo_primary_term->get_primary_term();
					usort($this->terms[$term->taxonomy], function ($a, $b) use ($primary_term) {
						return $b->id == $primary_term ? 1 : 0;
					});
				}
			}
		}
	}

	/**
	 * Set the meta that are set for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function SetMeta($meta) {
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
		}
	}

	/**
	 * Set the ACF fields that are set for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function SetFields($fields) {
		// Return empty array if ACF is unavailable
		if (!function_exists('get_fields'))
			return;

		// Get collection of ACF fields for the post
		if (is_bool($fields)) {
			$this->fields = get_fields($this->id);
		} else {
			foreach ($fields as $field) {
				$this->fields[$field] = get_field($field, $this->id);
			}
		}
	}
}
