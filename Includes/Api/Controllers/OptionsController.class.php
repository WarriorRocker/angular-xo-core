<?php

/**
 * Provide endpoints for retrieving option groups.
 *
 * @since 1.0.0
 */
class XoApiControllerOptions extends XoApiAbstractController
{
	protected $restBase = 'xo/v1/options';

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
					'name' => [
						'required' => true
					]
				]
			]
		]);

		register_rest_route($this->restBase, '/get/(?P<name>\d+)', [
			[
				'methods' => 'GET',
				'callback' => [$this, 'Get'],
				'permission_callback' => '__return_true'
			]
		]);
	}

	/**
	 * Get an option group by name.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $params Request object
	 * @return XoApiAbstractOptionsGetResponse
	 */
	public function Get(WP_REST_Request $params) {
		// Return an error if ACF is not present or activated
		if ((!function_exists('acf_get_fields')) ||
			(!function_exists('get_field')))
			return new XoApiAbstractOptionsGetResponse(false, __('ACF not present.', 'xo'));

		// Return an error if missing the name of the option group
		if (empty($params['name']))
		    return new XoApiAbstractOptionsGetResponse(false, __('Missing group name.', 'xo'));

		// Return an error if the group is not allowed over the Xo API
		if (!$allowedGroups = $this->Xo->Services->Options->GetOption('xo_acf_allowed_groups', array()))
			return new XoApiAbstractOptionsGetResponse(false, __('No allowed groups are defined.', 'xo'));

		// Check if group name is not in the allowed list
		if ((!array_key_exists($params['name'], $allowedGroups))
			|| ($allowedGroups[$params['name']] != 1))
			return new XoApiAbstractOptionsGetResponse(false, __('Not an allowed option group.', 'xo'));

		// Return an error if the option group does not exist or contains no data
		if (!$group = acf_get_fields($params['name']))
			return new XoApiAbstractOptionsGetResponse(false, __('Unable to retrieve options group.', 'xo'));

		// Define collection of individual fields within the option group
		$fields = array();

		// Iterate through the individual fields within the option group
		foreach ($group as $field)
			$fields[$field['name']] = get_field($field['name'], 'option');

		// Return success and fields for the requested option group
		return new XoApiAbstractOptionsGetResponse(
			true, __('Successfully retrieved options group.', 'xo'),
			$fields
		);
	}
}