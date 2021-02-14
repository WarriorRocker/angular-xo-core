<?php

/**
 * Provide endpoints for filtering terms.
 *
 * @since 1.0.0
 */
class XoApiControllerTerms extends XoApiAbstractController
{
	protected $restBase = 'xo/v1/terms';

	public function __construct(Xo $Xo) {
		parent::__construct($Xo);
		add_action('rest_api_init', [$this, 'RegisterRoutes'], 10, 0);
	}

	public function RegisterRoutes() {
		register_rest_route($this->restBase, '/get', [
			[
				'methods' => 'GET',
				'callback' => [$this, 'Get'],
				'permission_callback' => '__return_true',
				'args' => [
					'url' => [
						'required' => true
					]
				]
			]
		]);

		register_rest_route($this->restBase, '/filter', [
			[
				'methods' => 'POST',
				'callback' => [$this, 'Filter'],
				'permission_callback' => '__return_true'
			]
		]);
	}

	/**
	 * Get a taxonomy and term by url.
	 *
	 * @since 1.0.7
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractTermsGetResponse
	 */
	public function Get(WP_REST_Request $params) {
		global $wp_rewrite;

		// Return an error if the url is missing
		if (empty($params['url']))
			return new XoApiAbstractTermsGetResponse(false, __('Missing category url.', 'xo'));

		$query_vars = $this->Xo->Services->Rewrites->GetWpQueryForURL($params['url']);

		$term_slug = $query_vars['category_name'];
		$term_taxonomy = (!empty($query_vars['taxonomy']) ? $query_vars['taxonomy'] : 'category');

		$term = get_term_by('slug', $term_slug, $term_taxonomy);

		// Return an error if the term was not found
		if (!$term)
			return new XoApiAbstractTermsGetResponse(false, __('Unable to locate term.', 'xo'));

		// Obtain the fully formed term object
		$term = new XoApiAbstractTerm($term, true, true);

		// Apply filters
		$term = apply_filters('xo/api/terms/get', $term);
		$term = apply_filters('xo/api/terms/get/id=' . $term->id, $term);
		$term = apply_filters('xo/api/terms/get/taxonomy=' . $term->taxonomy, $term);

		// Return success and fully formed term object
		return new XoApiAbstractTermsGetResponse(
			true, __('Successfully located term.', 'xo'),
			$term
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
	public function Filter(WP_REST_Request $params) {
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
		    usort($terms, function (XoApiAbstractTerm $cur, XoApiAbstractTerm $next) use ($order, $orderBy) {
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