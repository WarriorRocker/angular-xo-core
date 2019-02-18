<?php

/**
 * Provide endpoints for filtering terms.
 *
 * @since 1.0.0
 */
class XoApiControllerTerms extends XoApiAbstractIndexController
{
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
			$terms[] = new XoApiAbstractTerm($term, true);

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
}