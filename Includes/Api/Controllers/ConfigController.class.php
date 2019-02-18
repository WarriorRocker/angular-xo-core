<?php

/**
 * Provide endpoints for retrieving the main site configuration.
 *
 * @since 1.0.0
 */
class XoApiControllerConfig extends XoApiAbstractIndexController
{
	/**
	 * Get the main site configuration.
	 *
	 * @since 1.0.0
	 *
	 * @return XoApiAbstractConfigGetResponse
	 */
	function Get() {
		$theme = wp_get_theme();

		// Generate dynamic application config
		$config = array(
			'app' => array(
				'title' => get_bloginfo('name'),
				'url' => get_site_url(),
				'version' => $theme->get('Version'),
				'debug' => WP_DEBUG
			),
			'paths' => array(
				'apiUrl' => $this->Xo->Services->Options->GetOption('xo_api_endpoint') . '/',
				'templateUrl' => wp_make_link_relative(get_bloginfo('template_url')) . '/'
			)
		);

		return new XoApiAbstractConfigGetResponse(
			true, __('Successfully generated config.', 'xo'),
			$config
		);
	}
}