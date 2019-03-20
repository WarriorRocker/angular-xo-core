<?php

/**
 * Provide endpoints for filtering terms.
 *
 * @since 1.0.0
 */
class XoApiControllerTerms extends XoApiAbstractIndexController
{
	/**
	 * Get a taxonomy and term by url.
	 *
	 * @since 1.0.7
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractTermsGetResponse
	 */
	public function Get($params) {
		// Return an error if the url is missing
		if (empty($params['url']))
			return new XoApiAbstractTermsGetResponse(false, __('Missing category url.', 'xo'));

		// Get the taxonomy by parsing the requested url
		$taxonomy = $this->GetTaxonomyByUrl($params['url']);

		// Return an error if the taxonomy was not found
		if (!$taxonomy)
			return new XoApiAbstractTermsGetResponse(false, __('Unable to locate taxonomy.', 'xo'));

		// Get the term within the found taxonomy by comparing the requested url
		$term = $this->GetTermByTaxonomyAndUrl($taxonomy, $params['url']);

		// Return an error if the term was not found
		if (!$term)
			return new XoApiAbstractTermsGetResponse(false, __('Unable to locate term.', 'xo'));

		// Return success and the taxonomy and term objects
		return new XoApiAbstractTermsGetResponse(
			true, __('Successfully located term.', 'xo'),
			new XoApiAbstractTerm($term, true, true)
		);
	}

	/**
	 * Filter, search, or list terms by various properties similar to get_terms().
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractTermsFilterResponse
	 */
	function Filter($params) {
		// Return an error if the taxonomy is missing
		if (empty($params['taxonomy']))
			return new XoApiAbstractTermsFilterResponse(false, __('Missing terms taxonomy.', 'xo'));

		// Collect search vars
		$order = ((!empty($params['order'])) ? $params['order'] : 'ASC');
		$orderBy = ((!empty($params['orderby'])) ? $params['orderby'] : '');

		// Get the terms for the given taxonomy
		$taxonomyTerms = get_terms(array(
			'taxonomy' => $params['taxonomy']
		));

		// Return an error if no terms were found for the given taxonomy
		if (is_wp_error($taxonomyTerms))
			return new XoApiAbstractTermsFilterResponse(false, __('Terms for taxonomy not found.', 'xo'));

		// Iterate through terms and retrieve fully formed term objects
		$terms = array();
		foreach ($taxonomyTerms as $term)
			$terms[] = new XoApiAbstractTerm($term, true, true);

		// Sort the terms by the given term meta key
		if ($orderBy)
		    usort($terms, function ($cur, $next) use ($order, $orderBy) {
		        $orderCur = ((!empty($cur->meta[$orderBy])) ? intval($cur->meta[$orderBy]) : 0);
				$orderNext = ((!empty($next->meta[$orderBy])) ? intval($next->meta[$orderBy]) : 0);
		        return (($order == 'ASC') ? $orderCur > $orderNext : $orderCur < $orderNext);
		    });

		// Return success and collection of fully formed term objects
		return new XoApiAbstractTermsFilterResponse(
			true, __('Successfully located terms taxonomy.', 'xo'),
			$terms
		);
	}

	/**
	 * Summary of GetTaxonomyByUrl
	 *
	 * @since 1.0.7
	 *
	 * @param mixed $url
	 * @return boolean|WP_Taxonomy
	 */
	private function GetTaxonomyByUrl($url) {
		$taxonomies = get_taxonomies(array(), 'objects');

		foreach ($taxonomies as $taxonomy_config) {
			if (!$taxonomy_config->public)
				continue;

			if (empty($taxonomy_config->rewrite['slug']))
				continue;

			$taxonomyUrl = '/' . $taxonomy_config->rewrite['slug'] . '/';

			if (strpos($url, $taxonomyUrl) === 0)
				return $taxonomy_config;
		}

		return false;
	}

	/**
	 * Summary of GetTermByTaxonomyAndUrl
	 * @param WP_Taxonomy $taxonomy
	 * @param mixed $url
	 * @return boolean|WP_Term
	 */
	private function GetTermByTaxonomyAndUrl(WP_Taxonomy $taxonomy, $url) {
		$url = trailingslashit($url);

		$taxonomyTerms = get_terms(array(
			'taxonomy' => $taxonomy->name
		));

		if ((is_wp_error($taxonomyTerms)) || (empty($taxonomyTerms)))
			return false;

		foreach ($taxonomyTerms as $term) {
			$termUrl = trailingslashit(wp_make_link_relative(get_term_link($term)));

			if (strpos($url, $termUrl) === 0)
				return $term;
		}

		return false;
	}
}