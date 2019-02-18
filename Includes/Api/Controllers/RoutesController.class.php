<?php

/**
 * Provide endpoints for retrieving dynamic routes.
 * 
 * @since 1.0.0
 */
class XoApiControllerRoutes extends XoApiAbstractController
{
	/**
	 * Get the dynamic route configuration.
	 * 
	 * @since 1.0.0
	 * 
	 * @return XoApiAbstractRoutesGetResponse
	 */
	function Get() {
		$routes = array();

		// Check if the currently logged in user can edit pages and previews are enabled
		if ((current_user_can('edit_others_pages')) &&
		   ($this->Xo->Services->Options->GetOption('xo_routing_previews_enabled', false))) {
			// Add routes for page drafts
			$this->Xo->Services->RouteGenerator->AddRoutesForPageDrafts($routes);

			// Add routes for page previews
			$this->Xo->Services->RouteGenerator->AddRoutesForPagePreviews($routes);

			// Add routes for post drafts and previews
			$this->Xo->Services->RouteGenerator->AddRoutesForPostDraftsAndPreviews($routes);
		}

		// Get the base routes
		$routes = array_merge($routes, $this->Xo->Services->RouteGenerator->GetRoutes());

		// Return an error if no routes were generated
		if (!$routes)
			return new XoApiAbstractRoutesGetResponse(false, __('Unable to retrieve routes.', 'xo'));

		// Return success 
		return new XoApiAbstractRoutesGetResponse(
			true, __('Successfully retrieved routes.', 'xo'),
			$routes
		);
	}
}