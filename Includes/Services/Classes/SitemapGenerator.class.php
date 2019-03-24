<?php

/**
 * Service class used to generate site maps.
 *
 * @since 1.0.9
 */
class XoServiceSitemapGenerator
{
	/**
	 * Generate a collection sitemap entries by iterating through all posts within the specified post types.
	 *
	 * @since 1.0.9
	 *
	 * @return XoApiAbstractSitemapEntry[] Collection of sitemap entries.
	 */
	public function GenerateSitemapForPosts() {
		$sitemapEntries = array();

		// Get all public post types
		$postTypes = get_post_types(array(
			'public' => 1
		), 'names');

		// Setup the default query for retrieving sitemap posts
		$postsQuery = array(
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type' => $postTypes
		);

		// Filter the query used to include posts in the sitemap
		$postsQuery = apply_filters('xo/sitemap/posts/query', $postsQuery);

		// Get all sitemap posts using the generated posts query
		$posts = get_posts($postsQuery);

		// Check to ensure the posts array is not an error or empty
		if ((!is_wp_error($posts)) && (!empty($posts))) {
			// Iterate through all found posts
			foreach ($posts as $post) {
				// Generate a sitemap entry using the post title and relative URL
				$sitemapEntries[] = $this->GetSitemapEntryForPost($post);
			}
		}

		// Filter the generated sitemap entries
		$sitemapEntries = apply_filters('xo/sitemap/posts/entries', $sitemapEntries);

		// Return the generated sitemap entries
		return $sitemapEntries;
	}

	/**
	 * Generate a collection of sitemap entries by iterating through all terms within the specified taxonomies.
	 *
	 * @since 1.0.9
	 *
	 * @return XoApiAbstractSitemapEntry[] Collection of sitemap entries.
	 */
	public function GenerateSitemapForTaxonomies() {
		$sitemapEntries = array();

		// Get all public taxonomies
		$taxonomies = get_taxonomies(array(
			'public' => 1
		), 'names');

		// Setup the default query used to include terms in the sitemap
		$termsQuery = array(
			'taxonomy' => $taxonomies
		);

		// Filter the query used to include terms in the sitemap
		$termsQuery = apply_filters('xo/sitemap/terms/query', $termsQuery);

		// Get all sitemap terms using the generated terms query
		$terms = get_terms($termsQuery);

		// Check to ensure the terms array is not an error or empty
		if ((!is_wp_error($terms)) && (!empty($terms))) {
			// Iterate through all found terms
			foreach ($terms as $term) {
				// Generate a sitemap entry using the term name and relative URL
				$sitemapEntries[] = $this->GetSitemapEntryForTerm($term);
			}
		}

		// Filter the generated sitemap entries
		$sitemapEntries = apply_filters('xo/sitemap/terms/entries', $sitemapEntries);

		// Return the generated sitemap entries
		return $sitemapEntries;
	}

	/**
	 * Generate sitemap entry breadcrumbs for each part of the given URL.
	 *
	 * @since 1.1.0
	 *
	 * @param string $url Relative URL for which to generate breadcrumbs.
	 * @return XoApiAbstractSitemapEntry[] Collection of sitemap entries.
	 */
	public function GenerateBreadcrumbsForUrl($url) {
		$breadcrumbs = array();

		$urlParts = explode('/', $url);

		$currentUrl = '';
		$taxonomy = false;

		foreach ($urlParts as $urlPart) {
			if (!$urlPart)
				continue;

			$currentUrl .= '/' . $urlPart;

			if (($post = get_post(url_to_postid($currentUrl))) ||
				($post = get_page_by_path($currentUrl))) {
				$breadcrumbs[] = $this->GetSitemapEntryForPost($post);
			} else if (($taxonomy) &&
				($term = $this->GetTermByTaxonomyAndSlug($taxonomy, $urlPart))) {
				$breadcrumbs[] = $this->GetSitemapEntryForTerm($term);
			}

			$taxonomy = $this->GetTaxonomyByUrl($currentUrl);
		}

		return $breadcrumbs;
	}

	/**
	 * Get a post's sitemap entry with applied filters.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Post $post Post to build the sitemap entry.
	 * @return XoApiAbstractSitemapEntry Sitemap entry for post.
	 */
	public function GetSitemapEntryForPost(WP_Post $post) {
		$entry = new XoApiAbstractSitemapEntry(
			$post->post_title,
			wp_make_link_relative(get_permalink($post->ID))
		);

		return apply_filters('xo/sitemap/entry/post', $entry, $post);
	}

	/**
	 * Get a term's sitemap entry with applied filters.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Term $term Term to build the sitemap entry.
	 * @return XoApiAbstractSitemapEntry Sitemap entry for term.
	 */
	public function GetSitemapEntryForTerm(WP_Term $term) {
		$entry = new XoApiAbstractSitemapEntry(
			$term->name,
			wp_make_link_relative(get_term_link($term))
		);

		return apply_filters('xo/sitemap/entry/term', $entry, $term);
	}

	/**
	 * Turn a flat and unsorted array of sitemap entries recursively into a nested array.
	 *
	 * @since 1.0.9
	 *
	 * @param XoApiAbstractSitemapEntry[] $items Collection of sitemap entries.
	 * @param string $baseUrl Set the base from which to return
	 * @return XoApiAbstractSitemapEntry[] Collection of nested sitemap entries.
	 */
	public function FlatSitemapToTree($sitemapEntries = array(), $baseUrl = '/') {
		// Collection of sitemap parents
		$sitemapParents = array();

		// Set the URL depth to search for parents
		$depth = substr_count($baseUrl, '/');

		// Iterate through the current set of sitemap entries
		foreach ($sitemapEntries as $entry) {
			// Skip the current entry if the URL depth does not match
			if ((substr($entry->url, 0, strlen($baseUrl)) != $baseUrl)
				|| (substr_count($entry->url, '/') != $depth))
				continue;

			// Recursively generate children at the current URL
		    $entry->children = $this->FlatSitemapToTree($sitemapEntries, $entry->url . '/');

			// Add the current entry to the sitemap parents collection
			$sitemapParents[] = $entry;
		}

		// Check if the parents collection has entries
		if ($sitemapParents)
		    usort($sitemapParents, function (XoApiAbstractSitemapEntry$a, XoApiAbstractSitemapEntry$b) {
		        return strcmp($a->url, $b->url);
		    });

		// Return the current collection of sitemap parents
		return $sitemapParents;
	}

	/**
	 * Get a taxonomy by comparing the given URL with the base rewrite slug.
	 *
	 * @since 1.1.0
	 *
	 * @param string $url URL base to search for registered taxonomies.
	 * @return boolean|WP_Taxonomy Taxonomy found for the given URL.
	 */
	public function GetTaxonomyByUrl($url) {
		$taxonomies = get_taxonomies(array(
			'public' => 1
		), 'objects');

		foreach ($taxonomies as $taxonomy_config) {
			if (!$taxonomy_config->public)
				continue;

			if (empty($taxonomy_config->rewrite['slug']))
				continue;

			$taxonomyUrl = '/' . $taxonomy_config->rewrite['slug'];

			if ($taxonomyUrl == $url)
				return $taxonomy_config;
		}

		return false;
	}

	/**
	 * Get a term by comparing the given URL with a taxonomy and term slug.
	 *
	 * @since 1.1.0
	 *
	 * @param string $url URL base to search for terms.
	 * @return boolean|WP_Term Term found for the given URL.
	 */
	public function GetTermByUrl($url) {
		$urlParts = explode('/', $url);

		$currentUrl = '';
		$taxonomy = false;
		$term = false;

		foreach ($urlParts as $urlPart) {
			if (!$urlPart)
				continue;

			$currentUrl .= '/' . $urlPart;

			if (($taxonomy)
				&& ($termGet = $this->GetTermByTaxonomyAndSlug($taxonomy, $urlPart))) {
				$term = $termGet;
			}

			$taxonomy = $this->GetTaxonomyByUrl($currentUrl);
		}

		return $term;
	}

	/**
	 * Get a term within a given taxonomy by comparing the requested slug.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Taxonomy $taxonomy Taxonomy to search for terms.
	 * @param string $slug Slug of the term to search.
	 * @return boolean|WP_Term Term found for the given slug.
	 */
	public function GetTermByTaxonomyAndSlug(WP_Taxonomy $taxonomy, $slug) {
		$taxonomyTerms = get_terms(array(
			'taxonomy' => $taxonomy->name,
			'slug' => $slug
		));

		if ((is_wp_error($taxonomyTerms)) || (empty($taxonomyTerms)))
			return false;

		return $taxonomyTerms[0];
	}
}