<?php

/**
 * Provide endpoints for retrieving dynamic routes.
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

		// Return success
		return new XoApiAbstractRoutesGetResponse(
			true, __('Successfully retrieved routes.', 'xo'),
			$routes
		);
	}
}