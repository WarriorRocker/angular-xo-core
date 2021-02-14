<?php

/**
 * Provide endpoints for retrieving the main site configuration.
 *
 * @since 1.0.0
 */
class XoApiControllerConfig extends XoApiAbstractController
{
	/**
	 * Get the main site configuration.
	 *
	 * @since 1.0.0
	 *
	 * @return XoApiAbstractConfigGetResponse
	 */
	public function Get() {
		$theme = wp_get_theme();


		// Generate dynamic application config
		$config = [
			'app' => [
				'title' => get_bloginfo('name'),
				'url' => get_site_url(),
				'version' => $theme->get('Version'),
				'debug' => WP_DEBUG
			],
			'paths' => [
				'apiUrl' => wp_make_link_relative(rest_url()) . 'xo/v1',
				'templateUrl' => wp_make_link_relative(get_bloginfo('template_url')),
				'adminUrl' => wp_make_link_relative(admin_url()),
				'restUrl' => wp_make_link_relative(rest_url())
			],
			'user' => false
		];

		$user = wp_get_current_user();

		if ($user->exists()) {
			$config['user'] = [
				'id' => $user->data->ID,
				'canEditPosts' => $user->has_cap('edit_posts')
			];
		}

		return new XoApiAbstractConfigGetResponse(
			true, __('Successfully generated config.', 'xo'),
			$config
		);
	}
}