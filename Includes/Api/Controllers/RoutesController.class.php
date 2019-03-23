<?php

/**
 * Provide endpoints for retrieving dynamic routes and sitemap.
 *
 * @since 1.0.0
 */
class XoApiControllerRoutes extends XoApiAbstractIndexController
{
	/**
	 * Get the dynamic route configuration.
	 *
	 * @since 1.0.0
	 *
	 * @return XoApiAbstractRoutesGetResponse
	 */
	public function Get() {
		// Check if the currently logged in user can edit pages and previews are enabled
		$previewsEnabled = ((current_user_can('edit_others_pages')) &&
		   ($this->Xo->Services->Options->GetOption('xo_routing_previews_enabled', false)));

		// Get the base routes
		$routes = $this->Xo->Services->RouteGenerator->GetRoutes($previewsEnabled);

		// Return an error if no routes were generated
		if (!$routes)
			return new XoApiAbstractRoutesGetResponse(false, __('Unable to retrieve routes.', 'xo'));

		// Return success and generated routes
		return new XoApiAbstractRoutesGetResponse(
			true, __('Successfully retrieved routes.', 'xo'),
			$routes
		);
	}

	/**
	 * Get sitemap entries for posts and terms.
	 *
	 * @since 1.0.9
	 *
	 * @return XoApiAbstractRoutesSitemapResponse
	 */
	public function Sitemap() {
		// Get a combined list of all post and term sitemap entries
		$sitemapEntries = array_merge(
			$this->Xo->Services->SitemapGenerator->GenerateSitemapForPosts(),
			$this->Xo->Services->SitemapGenerator->GenerateSitemapForTaxonomies()
		);

		// Convert the flat sitemap entries to a nested collection
		$sitemapEntries = $this->Xo->Services->SitemapGenerator->FlatSitemapToTree($sitemapEntries);

		// Return success and generated sitemap entries
		return new XoApiAbstractRoutesSitemapResponse(
			true, __('Successfully retrieved sitemap entries.', 'xo'),
			$sitemapEntries
		);
	}
}